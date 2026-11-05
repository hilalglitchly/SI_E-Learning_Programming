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
                $order_sql = "ORDER BY p.tgl_post DESC";
                break;
            case 'top':
                $order_sql = "ORDER BY vote_count DESC, p.views DESC, p.tgl_post DESC";
                break;
            case 'unsolved':
                $where_clauses[] = "p.tipe = 'pertanyaan' AND p.status = 'open'";
                $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
                $order_sql = "ORDER BY p.tgl_post DESC";
                break;
            default: // 'hot'
                $order_sql = "ORDER BY p.is_pinned DESC, (vote_count * 2 + p.views * 0.05 + jawaban_count * 3) DESC, p.tgl_post DESC";
                break;
        }

        $countRow = DB::selectOne("SELECT COUNT(*) as total FROM tb_forum_post p {$where_sql}", $params);
        $total_posts = $countRow ? (int) $countRow->total : 0;
        $total_pages = (int) ceil($total_posts / $per_page);

        $sql = "
            SELECT p.*, 
                   u.username, u.role as user_role,
                   COALESCE((SELECT SUM(CASE WHEN v.tipe_vote = 'up' THEN 1 WHEN v.tipe_vote = 'down' THEN -1 ELSE 0 END) FROM tb_forum_vote v WHERE v.id_post = p.id_post), 0) as vote_count,
                   (SELECT COUNT(*) FROM tb_forum_jawaban j WHERE j.id_post = p.id_post) as jawaban_count
            FROM tb_forum_post p
            JOIN tb_user u ON p.id_user = u.id_user
            {$where_sql}
            {$order_sql}
            LIMIT {$per_page} OFFSET {$offset}
        ";

        $postRows = DB::select($sql, $params);
        $posts = [];
        foreach ($postRows as $row) {
            $p = (array) $row;

            $tagRows = DB::select("
                SELECT t.nama_tag, t.warna 
                FROM tb_forum_post_tag pt 
                JOIN tb_forum_tag t ON pt.id_tag = t.id_tag 
                WHERE pt.id_post = ?
            ", [$p['id_post']]);
            $p['tags'] = array_map(fn($r) => (array) $r, $tagRows);

            if ($id_user) {
                $voteRow = ForumVote::where('id_user', $id_user)
                    ->where('id_post', $p['id_post'])
                    ->first();
                $p['user_vote'] = $voteRow ? $voteRow->tipe_vote : null;

                $p['bookmarked'] = ForumSimpan::where('id_user', $id_user)
                    ->where('id_post', $p['id_post'])
                    ->exists();
            } else {
                $p['user_vote'] = null;
                $p['bookmarked'] = false;
            }

            $posts[] = $p;
        }

        $allTagsRows = DB::select("
            SELECT t.id_tag, t.nama_tag, t.warna, t.icon, COUNT(pt.id_post) as post_count 
            FROM tb_forum_tag t 
            LEFT JOIN tb_forum_post_tag pt ON t.id_tag = pt.id_tag 
            GROUP BY t.id_tag, t.nama_tag, t.warna, t.icon 
            ORDER BY post_count DESC 
            LIMIT 10
        ");
        $allTags = array_map(fn($r) => (array) $r, $allTagsRows);

        $annRows = DB::select("
            SELECT p.*, u.username 
            FROM tb_forum_post p 
            JOIN tb_user u ON p.id_user = u.id_user 
            WHERE p.is_announcement = 1 
            ORDER BY p.tgl_post DESC 
            LIMIT 3
        ");
        $announcements = array_map(fn($r) => (array) $r, $annRows);

        $totalMembers = User::count();
        $totalSolved = ForumPost::where('status', 'solved')->count();
        $totalForumPosts = ForumPost::count();

        $topContribRows = DB::select("
            SELECT u.username, u.role,
                (SELECT COALESCE(SUM(CASE WHEN v.tipe_vote = 'up' THEN 1 ELSE 0 END), 0)
                 FROM tb_forum_vote v 
                 JOIN tb_forum_post p2 ON v.id_post = p2.id_post 
                 WHERE p2.id_user = u.id_user) +
                (SELECT COALESCE(SUM(CASE WHEN v2.tipe_vote = 'up' THEN 1 ELSE 0 END), 0)
                 FROM tb_forum_vote v2 
                 JOIN tb_forum_jawaban j2 ON v2.id_jawaban = j2.id_jawaban 
                 WHERE j2.id_user = u.id_user) as total_score
            FROM tb_user u
            HAVING total_score > 0
            ORDER BY total_score DESC
            LIMIT 5
        ");
        $topContributors = array_map(fn($r) => (array) $r, $topContribRows);

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
        $tipe = $request->input('tipe', 'pertanyaan');
        $konten = trim((string) $request->input('konten'));
        $tags_selected = (array) $request->input('tags', []);

        if ($judul === '' || trim(strip_tags($konten)) === '') {
            return redirect()->route('forum.create')
                ->with('msg', 'Judul dan konten tidak boleh kosong.')
                ->with('msg_type', 'warning');
        }
        if (strlen($judul) > 250) {
            return redirect()->route('forum.create')
                ->with('msg', 'Judul terlalu panjang (maksimal 250 karakter).')
                ->with('msg_type', 'warning');
        }
        if (count($tags_selected) > 5) {
            return redirect()->route('forum.create')
                ->with('msg', 'Maksimal memilih 5 tag.')
                ->with('msg_type', 'warning');
        }

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

            foreach ($tags_selected as $tag_id) {
                ForumPostTag::create([
                    'id_post' => $post->id_post,
                    'id_tag' => (int) $tag_id,
                ]);
            }

            DB::commit();

            return redirect()->route('forum.show', ['id' => $post->id_post, 'new' => 1]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('forum.create')
                ->with('msg', 'Gagal membuat postingan: ' . $e->getMessage())
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
                $pesan_notif = "{$username} menjawab postingan Anda: \"" . substr($post->judul, 0, 30) . "...\"";
                $link_notif = route('forum.show', ['id' => $id_post], false);

                Notifikasi::create([
                    'id_user' => $post->id_user,
                    'judul' => 'Jawaban Baru',
                    'pesan' => $pesan_notif,
                    'link' => $link_notif,
                ]);
            }

            return redirect()->to(route('forum.show', ['id' => $id_post]) . '#answers');
        }

        // 2. Mark as Solved
        if ($request->has('mark_solved')) {
            if ((int) $id_user === (int) $post->id_user || strtolower($role) === 'admin') {
                $id_jawaban_solved = (int) $request->input('id_jawaban');

                DB::beginTransaction();
                try {
                    ForumJawaban::where('id_post', $id_post)->update(['is_solusi' => 0]);
                    ForumJawaban::where('id_jawaban', $id_jawaban_solved)->update(['is_solusi' => 1]);
                    $post->status = 'solved';
                    $post->save();

                    DB::commit();
                    return redirect()->route('forum.show', ['id' => $id_post]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->route('forum.show', ['id' => $id_post])
                        ->with('msg', 'Gagal menandai solusi: ' . $e->getMessage())
                        ->with('msg_type', 'error');
                }
            }
        }

        return redirect()->route('forum.show', ['id' => $id_post]);
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
}
