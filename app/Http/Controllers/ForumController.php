<?php

namespace App\Http\Controllers;

use App\Models\ForumJawaban;
use App\Models\ForumPost;
use App\Models\ForumPostTag;
use App\Models\ForumSimpan;
use App\Models\ForumTag;
use App\Models\ForumVote;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $is_logged_in = auth()->check();
        $user = auth()->user();
        $id_user = $user ? $user->id_user : null;
        $role = $user ? $user->role : '';
        $username = $user ? $user->username : '';

        $sort = $request->query('sort', 'hot');
        $tag_filter = $request->query('tag', '');
        $tipe_filter = $request->query('tipe', '');
        $search = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $per_page = 15;
        $offset = ($page - 1) * $per_page;

        $where_clauses = [];
        $params = [];

        if (!empty($tag_filter)) {
            $where_clauses[] = "p.id_post IN (SELECT pt.id_post FROM tb_forum_post_tag pt JOIN tb_forum_tag t ON pt.id_tag = t.id_tag WHERE t.nama_tag = ?)";
            $params[] = $tag_filter;
        }

        if (!empty($tipe_filter) && in_array($tipe_filter, ['pertanyaan', 'diskusi', 'berbagi'])) {
            $where_clauses[] = "p.tipe = ?";
            $params[] = $tipe_filter;
        }

        if (!empty($search)) {
            $where_clauses[] = "(p.judul LIKE ? OR p.konten LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        switch ($sort) {
            case 'baru':
                $order_sql = "ORDER BY forum_feed.tgl_post DESC";
                break;
            case 'top':
                $order_sql = "ORDER BY forum_feed.vote_count DESC, forum_feed.views DESC, forum_feed.tgl_post DESC";
                break;
            case 'unsolved':
                $where_clauses[] = "p.tipe = 'pertanyaan' AND p.status = 'open'";
                $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
                $order_sql = "ORDER BY forum_feed.tgl_post DESC";
                break;
            case 'saved':
                if (!$id_user) {
                    return redirect()->route('login', ['msg' => 'login_required']);
                }
                $where_clauses[] = "p.id_post IN (SELECT fs.id_post FROM tb_forum_simpan fs WHERE fs.id_user = ?)";
                $params[] = $id_user;
                $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
                $order_sql = "ORDER BY forum_feed.tgl_post DESC";
                break;
            default: // 'hot'
                $order_sql = "ORDER BY forum_feed.is_pinned DESC, (forum_feed.vote_count * 2 + forum_feed.views * 0.05 + forum_feed.jawaban_count * 3) DESC, forum_feed.tgl_post DESC";
                break;
        }

        $countRow = DB::selectOne("SELECT COUNT(*) as total FROM tb_forum_post p {$where_sql}", $params);
        $total_posts = $countRow ? (int) $countRow->total : 0;
        $total_pages = (int) ceil($total_posts / $per_page);

        // Derived table required so MySQL 8 / TiDB allow arithmetic expressions on computed vote_count/jawaban_count in ORDER BY
        $sql = "
            SELECT * FROM (
                SELECT p.*,
                       u.username, u.role as user_role,
                       COALESCE((SELECT SUM(CASE WHEN v.tipe_vote = 'up' THEN 1 WHEN v.tipe_vote = 'down' THEN -1 ELSE 0 END) FROM tb_forum_vote v WHERE v.id_post = p.id_post), 0) as vote_count,
                       (SELECT COUNT(*) FROM tb_forum_jawaban j WHERE j.id_post = p.id_post) as jawaban_count
                FROM tb_forum_post p
                JOIN tb_user u ON p.id_user = u.id_user
                {$where_sql}
            ) AS forum_feed
            {$order_sql}
            LIMIT {$per_page} OFFSET {$offset}
        ";

        $postRows = DB::select($sql, $params);
        $postIds = array_map(fn($r) => (int) $r->id_post, $postRows);

        $tagsByPost = [];
        $votesByPost = [];
        $bookmarksSet = [];

        if (!empty($postIds)) {
            $placeholders = implode(',', array_fill(0, count($postIds), '?'));
            $tagRows = DB::select("
                SELECT pt.id_post, t.nama_tag, t.warna
                FROM tb_forum_post_tag pt
                JOIN tb_forum_tag t ON pt.id_tag = t.id_tag
                WHERE pt.id_post IN ({$placeholders})
            ", $postIds);
            foreach ($tagRows as $tr) {
                $tagsByPost[(int) $tr->id_post][] = [
                    'nama_tag' => $tr->nama_tag,
                    'warna' => $tr->warna,
                ];
            }

            if ($id_user) {
                $votesByPost = ForumVote::where('id_user', $id_user)
                    ->whereIn('id_post', $postIds)
                    ->pluck('tipe_vote', 'id_post')
                    ->toArray();

                $bookmarksSet = ForumSimpan::where('id_user', $id_user)
                    ->whereIn('id_post', $postIds)
                    ->pluck('id_post')
                    ->flip()
                    ->toArray();
            }
        }

        $posts = [];
        foreach ($postRows as $row) {
            $p = (array) $row;
            $pid = (int) $p['id_post'];
            $p['tags'] = $tagsByPost[$pid] ?? [];
            $p['user_vote'] = $votesByPost[$pid] ?? null;
            $p['bookmarked'] = isset($bookmarksSet[$pid]);
            $posts[] = $p;
        }

        $sidebar = Cache::remember('forum.sidebar_stats.v1', 45, function () {
            $allTagsRows = DB::select("
                SELECT t.id_tag, t.nama_tag, t.warna, t.icon, COUNT(pt.id_post) as post_count
                FROM tb_forum_tag t
                LEFT JOIN tb_forum_post_tag pt ON t.id_tag = pt.id_tag
                GROUP BY t.id_tag, t.nama_tag, t.warna, t.icon
                ORDER BY post_count DESC
                LIMIT 10
            ");

            $annRows = DB::select("
                SELECT p.*, u.username
                FROM tb_forum_post p
                JOIN tb_user u ON p.id_user = u.id_user
                WHERE p.is_announcement = 1
                ORDER BY p.tgl_post DESC
                LIMIT 3
            ");

            $topContribRows = DB::select("
                SELECT username, role, total_score FROM (
                    SELECT u.username, u.role,
                        (SELECT COALESCE(SUM(CASE WHEN v.tipe_vote = 'up' THEN 1 ELSE 0 END), 0)
                         FROM tb_forum_vote v
                         JOIN tb_forum_post p2 ON v.id_post = p2.id_post
                         WHERE p2.id_user = u.id_user) +
                        (SELECT COALESCE(SUM(CASE WHEN v2.tipe_vote = 'up' THEN 1 ELSE 0 END), 0)
                         FROM tb_forum_vote v2
                         JOIN tb_forum_jawaban j2 ON v2.id_jawaban = j2.id_jawaban
                         WHERE j2.id_user = u.id_user) AS total_score
                    FROM tb_user u
                ) AS contrib
                WHERE total_score > 0
                ORDER BY total_score DESC
                LIMIT 5
            ");

            return [
                'allTags' => array_map(fn($r) => (array) $r, $allTagsRows),
                'announcements' => array_map(fn($r) => (array) $r, $annRows),
                'totalMembers' => User::count(),
                'totalSolved' => ForumPost::where('status', 'solved')->count(),
                'totalForumPosts' => ForumPost::count(),
                'topContributors' => array_map(fn($r) => (array) $r, $topContribRows),
            ];
        });

        $allTags = $sidebar['allTags'];
        $announcements = $sidebar['announcements'];
        $totalMembers = $sidebar['totalMembers'];
        $totalSolved = $sidebar['totalSolved'];
        $totalForumPosts = $sidebar['totalForumPosts'];
        $topContributors = $sidebar['topContributors'];

        return view('forum.index', compact(
            'is_logged_in',
            'id_user',
            'role',
            'username',
            'sort',
            'tag_filter',
            'tipe_filter',
            'search',
            'page',
            'per_page',
            'total_posts',
            'total_pages',
            'posts',
            'allTags',
            'announcements',
            'totalMembers',
            'totalSolved',
            'totalForumPosts',
            'topContributors'
        ));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login', ['msg' => 'login_required']);
        }

        $user = auth()->user();
        $role = $user->role ?? '';
        $allTags = ForumTag::orderBy('nama_tag', 'asc')->get()->map(fn($t) => $t->toArray())->toArray();
        $msg = session('msg', '');
        $msg_type = session('msg_type', '');

        return view('forum.create', compact('role', 'allTags', 'msg', 'msg_type'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login', ['msg' => 'login_required']);
        }

        $user = auth()->user();
        $id_user = $user->id_user;
        $role = $user->role ?? '';

        $judul = trim((string) $request->input('judul'));
        $tipe = (string) $request->input('tipe', 'pertanyaan');
        $konten = trim((string) $request->input('konten'));
        $tags_selected = array_values(array_unique(array_map('intval', (array) $request->input('tags', []))));

        if (!in_array($tipe, ['pertanyaan', 'diskusi', 'berbagi'], true)) {
            $tipe = 'pertanyaan';
        }

        if ($judul === '' || trim(strip_tags($konten)) === '') {
            return redirect()->route('forum.create')
                ->with('msg', 'Judul dan konten tidak boleh kosong.')
                ->with('msg_type', 'warning');
        }
        if (mb_strlen($judul) > 250) {
            return redirect()->route('forum.create')
                ->with('msg', 'Judul terlalu panjang (maksimal 250 karakter).')
                ->with('msg_type', 'warning');
        }
        if (count($tags_selected) > 5) {
            return redirect()->route('forum.create')
                ->with('msg', 'Maksimal memilih 5 tag.')
                ->with('msg_type', 'warning');
        }

        $validTagIds = ForumTag::whereIn('id_tag', $tags_selected)->pluck('id_tag')->all();
        $konten = sanitize_rich_text($konten);

        DB::beginTransaction();
        try {
            $is_announcement = (strtolower($role) === 'admin' || strtolower($role) === 'pengajar') && $request->has('is_announcement') ? 1 : 0;

            $post = ForumPost::create([
                'id_user' => $id_user,
                'judul' => $judul,
                'konten' => $konten,
                'tipe' => $tipe,
                'is_announcement' => $is_announcement,
                'tgl_post' => now(),
            ]);

            foreach ($validTagIds as $tag_id) {
                ForumPostTag::create([
                    'id_post' => $post->id_post,
                    'id_tag' => (int) $tag_id,
                ]);
            }

            DB::commit();
            $this->flushForumCaches();

            return redirect()->route('forum.show', ['id' => $post->id_post, 'new' => 1]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('forum.create')
                ->with('msg', 'Gagal membuat postingan. Silakan coba lagi.')
                ->with('msg_type', 'error');
        }
    }

    public function show(Request $request, $id = null)
    {
        $id_post = (int) ($id ?? $request->query('id'));
        if ($id_post <= 0) {
            return redirect()->route('forum.index');
        }

        $is_logged_in = auth()->check();
        $user = auth()->user();
        $id_user = $user ? $user->id_user : null;
        $role = $user ? $user->role : '';
        $username = $user ? $user->username : '';

        ForumPost::where('id_post', $id_post)->increment('views');

        $postRow = DB::selectOne("
            SELECT p.*, u.username, u.role as user_role,
                   COALESCE((SELECT SUM(CASE WHEN v.tipe_vote = 'up' THEN 1 WHEN v.tipe_vote = 'down' THEN -1 ELSE 0 END) FROM tb_forum_vote v WHERE v.id_post = p.id_post), 0) as vote_count
            FROM tb_forum_post p
            JOIN tb_user u ON p.id_user = u.id_user
            WHERE p.id_post = ?
        ", [$id_post]);

        if (!$postRow) {
            abort(404, 'Postingan tidak ditemukan.');
        }
        $post = (array) $postRow;

        $ansRows = DB::select("
            SELECT j.*, u.username, u.role as user_role,
                   COALESCE((SELECT SUM(CASE WHEN v.tipe_vote = 'up' THEN 1 WHEN v.tipe_vote = 'down' THEN -1 ELSE 0 END) FROM tb_forum_vote v WHERE v.id_jawaban = j.id_jawaban), 0) as vote_count
            FROM tb_forum_jawaban j
            JOIN tb_user u ON j.id_user = u.id_user
            WHERE j.id_post = ?
            ORDER BY j.is_solusi DESC, vote_count DESC, j.tgl_jawaban ASC
        ", [$id_post]);
        $answers = array_map(fn($r) => (array) $r, $ansRows);

        $tagRows = DB::select("
            SELECT t.* FROM tb_forum_post_tag pt JOIN tb_forum_tag t ON pt.id_tag = t.id_tag WHERE pt.id_post = ?
        ", [$id_post]);
        $tags = array_map(fn($r) => (array) $r, $tagRows);

        $userVotePost = null;
        $isBookmarked = false;
        $userVoteAnswers = [];

        if ($is_logged_in) {
            $uv = ForumVote::where('id_user', $id_user)->where('id_post', $id_post)->first();
            if ($uv) {
                $userVotePost = $uv->tipe_vote;
            }

            $isBookmarked = ForumSimpan::where('id_user', $id_user)->where('id_post', $id_post)->exists();

            if (!empty($answers)) {
                $ansIds = array_column($answers, 'id_jawaban');
                $uvAns = ForumVote::where('id_user', $id_user)->whereIn('id_jawaban', $ansIds)->get();
                foreach ($uvAns as $row) {
                    $userVoteAnswers[$row->id_jawaban] = $row->tipe_vote;
                }
            }
        }

        $msg = session('msg', '');
        $msg_type = session('msg_type', '');

        return view('forum.show', compact(
            'is_logged_in',
            'id_user',
            'role',
            'username',
            'post',
            'answers',
            'tags',
            'userVotePost',
            'isBookmarked',
            'userVoteAnswers',
            'msg',
            'msg_type'
        ));
    }

    public function handlePost(Request $request, $id)
    {
        $id_post = (int) $id;
        if (!auth()->check()) {
            return redirect()->route('forum.show', ['id' => $id_post])
                ->with('msg', 'Anda harus login untuk membalas.')
                ->with('msg_type', 'error');
        }

        $user = auth()->user();
        $id_user = $user->id_user;
        $role = $user->role ?? '';
        $username = $user->username ?? '';

        $post = ForumPost::findOrFail($id_post);

        // 1. Submit Jawaban
        if ($request->has('submit_jawaban')) {
            $konten = sanitize_rich_text(trim((string) $request->input('konten')));
            if (trim(strip_tags($konten)) === '') {
                return redirect()->route('forum.show', ['id' => $id_post])
                    ->with('msg', 'Komentar tidak boleh kosong.')
                    ->with('msg_type', 'warning');
            }

            ForumJawaban::create([
                'id_post' => $id_post,
                'id_user' => $id_user,
                'konten' => $konten,
                'tgl_jawaban' => now(),
            ]);

            if ((int) $post->id_user !== (int) $id_user) {
                $pesan_notif = "{$username} menjawab postingan Anda: \"" . mb_substr((string) $post->judul, 0, 30) . "...\"";
                $link_notif = route('forum.show', ['id' => $id_post], false);

                Notifikasi::create([
                    'id_user' => $post->id_user,
                    'judul' => 'Jawaban Baru',
                    'pesan' => $pesan_notif,
                    'link' => $link_notif,
                ]);
            }

            $this->flushForumCaches();

            return redirect()->to(route('forum.show', ['id' => $id_post]) . '#answers');
        }

        // 2. Mark as Solved
        if ($request->has('mark_solved')) {
            if (((int) $id_user === (int) $post->id_user || strtolower($role) === 'admin') && $post->tipe === 'pertanyaan') {
                $id_jawaban_solved = (int) $request->input('id_jawaban');
                $targetJawaban = ForumJawaban::where('id_jawaban', $id_jawaban_solved)
                    ->where('id_post', $id_post)
                    ->first();

                if (!$targetJawaban) {
                    abort(404, 'Jawaban tidak ditemukan pada postingan ini.');
                }

                DB::beginTransaction();
                try {
                    ForumJawaban::where('id_post', $id_post)->update(['is_solusi' => 0]);
                    $targetJawaban->is_solusi = true;
                    $targetJawaban->save();
                    $post->status = 'solved';
                    $post->save();

                    DB::commit();
                    $this->flushForumCaches();
                    return redirect()->route('forum.show', ['id' => $id_post]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    report($e);
                    return redirect()->route('forum.show', ['id' => $id_post])
                        ->with('msg', 'Gagal menandai solusi. Silakan coba lagi.')
                        ->with('msg_type', 'error');
                }
            }
            abort(403, 'Anda tidak memiliki izin menandai solusi pada postingan ini.');
        }

        return redirect()->route('forum.show', ['id' => $id_post]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $post = ForumPost::findOrFail((int) $id);
        if ((int) $post->id_user !== (int) $user->id_user && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki izin mengedit postingan ini.');
        }

        $judul = trim((string) $request->input('judul'));
        $tipe = (string) $request->input('tipe', $post->tipe);
        $konten = sanitize_rich_text(trim((string) $request->input('konten')));

        if (!in_array($tipe, ['pertanyaan', 'diskusi', 'berbagi'], true)) {
            $tipe = $post->tipe;
        }
        if ($judul === '' || mb_strlen($judul) > 250 || trim(strip_tags($konten)) === '') {
            return redirect()->route('forum.show', ['id' => $post->id_post])
                ->with('msg', 'Judul (maks. 250 karakter) dan konten tidak boleh kosong.')
                ->with('msg_type', 'warning');
        }

        $post->update([
            'judul' => $judul,
            'tipe' => $tipe,
            'konten' => $konten,
            'tgl_update' => now(),
        ]);

        return redirect()->route('forum.show', ['id' => $post->id_post])
            ->with('msg', 'Postingan berhasil diperbarui!')
            ->with('msg_type', 'success');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $post = ForumPost::findOrFail((int) $id);
        if ((int) $post->id_user !== (int) $user->id_user && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki izin menghapus postingan ini.');
        }

        $post->delete();
        $this->flushForumCaches();

        return redirect()->route('forum.index');
    }

    public function updateJawaban(Request $request, $id, $id_jawaban)
    {
        $user = auth()->user();
        $post = ForumPost::findOrFail((int) $id);
        $jawaban = ForumJawaban::where('id_jawaban', (int) $id_jawaban)
            ->where('id_post', $post->id_post)
            ->firstOrFail();

        if ((int) $jawaban->id_user !== (int) $user->id_user && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki izin mengedit jawaban ini.');
        }

        $konten = sanitize_rich_text(trim((string) $request->input('konten')));
        if (trim(strip_tags($konten)) === '') {
            return redirect()->route('forum.show', ['id' => $post->id_post])
                ->with('msg', 'Jawaban tidak boleh kosong.')
                ->with('msg_type', 'warning');
        }

        $jawaban->update(['konten' => $konten]);

        return redirect()->route('forum.show', ['id' => $post->id_post])
            ->with('msg', 'Jawaban berhasil diperbarui!')
            ->with('msg_type', 'success');
    }

    public function deleteJawaban($id, $id_jawaban)
    {
        $user = auth()->user();
        $post = ForumPost::findOrFail((int) $id);
        $jawaban = ForumJawaban::where('id_jawaban', (int) $id_jawaban)
            ->where('id_post', $post->id_post)
            ->firstOrFail();

        if ((int) $jawaban->id_user !== (int) $user->id_user && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki izin menghapus jawaban ini.');
        }

        $wasSolution = (bool) $jawaban->is_solusi;
        $jawaban->delete();

        if ($wasSolution) {
            $post->update(['status' => 'open']);
        }
        $this->flushForumCaches();

        return redirect()->route('forum.show', ['id' => $post->id_post])
            ->with('msg', 'Jawaban berhasil dihapus.')
            ->with('msg_type', 'success');
    }

    public function vote(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu.',
            ]);
        }

        $id_user = auth()->user()->id_user;
        $id = (int) $request->input('id', 0);
        $type = $request->input('type', '');
        $vote = $request->input('vote', '');

        if (!$id || !in_array($type, ['post', 'jawaban']) || !in_array($vote, ['up', 'down'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid.',
            ]);
        }

        $col = $type === 'post' ? 'id_post' : 'id_jawaban';
        $targetExists = $type === 'post'
            ? ForumPost::where('id_post', $id)->exists()
            : ForumJawaban::where('id_jawaban', $id)->exists();

        if (!$targetExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Target voting tidak ditemukan.',
            ], 404);
        }

        try {
            $existing = ForumVote::where('id_user', $id_user)->where($col, $id)->first();
            $action = '';

            if ($existing) {
                if ($existing->tipe_vote === $vote) {
                    $existing->delete();
                    $action = 'removed';
                } else {
                    $existing->tipe_vote = $vote;
                    $existing->save();
                    $action = $vote;
                }
            } else {
                ForumVote::create([
                    'id_user' => $id_user,
                    $col => $id,
                    'tipe_vote' => $vote,
                ]);
                $action = $vote;
            }

            $scoreRow = DB::selectOne("
                SELECT COALESCE(SUM(CASE WHEN tipe_vote = 'up' THEN 1 WHEN tipe_vote = 'down' THEN -1 ELSE 0 END), 0) as score
                FROM tb_forum_vote WHERE {$col} = ?
            ", [$id]);
            $new_count = $scoreRow ? (int) $scoreRow->score : 0;
            Cache::forget('forum.sidebar_stats.v1');

            return response()->json([
                'status' => 'success',
                'action' => $action,
                'new_count' => $new_count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.',
            ]);
        }
    }

    public function simpan(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu.',
            ]);
        }

        $id_user = auth()->user()->id_user;
        $id_post = (int) $request->input('id_post', 0);

        if (!$id_post) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Post tidak valid.',
            ]);
        }

        if (!ForumPost::where('id_post', $id_post)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Postingan tidak ditemukan.',
            ], 404);
        }

        try {
            $exists = ForumSimpan::where('id_user', $id_user)
                ->where('id_post', $id_post)
                ->exists();

            if ($exists) {
                ForumSimpan::where('id_user', $id_user)
                    ->where('id_post', $id_post)
                    ->delete();
                return response()->json([
                    'status' => 'success',
                    'action' => 'removed',
                ]);
            } else {
                ForumSimpan::create([
                    'id_user' => $id_user,
                    'id_post' => $id_post,
                ]);
                return response()->json([
                    'status' => 'success',
                    'action' => 'saved',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.',
            ]);
        }
    }

    private function flushForumCaches(): void
    {
        Cache::forget('forum.sidebar_stats.v1');
        Cache::forget('home.public_payload.v1');
    }
}
