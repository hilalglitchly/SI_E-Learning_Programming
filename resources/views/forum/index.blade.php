@extends('layouts.app')

@section('title', 'E-FORVM — Forum Diskusi Programming')

@section('meta')
    <meta name="description" content="Forum diskusi programming terbuka. Tanya jawab, berbagi kode, dan diskusi seputar pemrograman bersama komunitas E-Learning Programming.">
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}?v={{ file_exists(public_path('assets/css/forum.css')) ? filemtime(public_path('assets/css/forum.css')) : time() }}">
@endsection

@section('content')
<!-- ============================================
     TICKER MARQUEE
     ============================================ -->
<div class="forum-ticker">
    <div class="forum-ticker-inner">
        <span><i class='bx bxs-hot'></i> FORUM PROGRAMMER TERBUKA INDONESIA</span>
        <span><i class='bx bx-star'></i> TANYA KODE & REVIEW ARSITEKTUR</span>
        <span><i class='bx bx-group'></i> KOMUNITAS WEB DEVELOPER BELAJAR BERSAMA</span>
        <span><i class='bx bx-code-alt'></i> KOLABORASI OPEN-SOURCE</span>
        <span><i class='bx bx-trophy'></i> CHALLENGE MINGGUAN AKTIF</span>
        <!-- Duplicate for seamless loop -->
        <span><i class='bx bxs-hot'></i> FORUM PROGRAMMER TERBUKA INDONESIA</span>
        <span><i class='bx bx-star'></i> TANYA KODE & REVIEW ARSITEKTUR</span>
        <span><i class='bx bx-group'></i> KOMUNITAS WEB DEVELOPER BELAJAR BERSAMA</span>
        <span><i class='bx bx-code-alt'></i> KOLABORASI OPEN-SOURCE</span>
        <span><i class='bx bx-trophy'></i> CHALLENGE MINGGUAN AKTIF</span>
    </div>
</div>

<!-- ============================================
     MAIN 3-COLUMN LAYOUT
     ============================================ -->
<div class="forum-layout">

    <!-- ========== SIDEBAR LEFT ========== -->
    <aside class="forum-sidebar-left">
        <!-- Explore -->
        <div class="forum-sidebar-box">
            <div class="forum-sidebar-title"><i class='bx bx-compass'></i> Eksplorasi</div>
            <ul class="forum-explore-list">
                <li class="forum-explore-item">
                    <a href="{{ route('forum.index', ['sort' => 'hot']) }}" class="{{ $sort === 'hot' ? 'active' : '' }}">
                        <i class='bx bxs-hot'></i> Populer & Hangat
                        <span class="forum-explore-badge">HOT</span>
                    </a>
                </li>
                <li class="forum-explore-item">
                    <a href="{{ route('forum.index', ['sort' => 'baru']) }}" class="{{ $sort === 'baru' ? 'active' : '' }}">
                        <i class='bx bx-time-five'></i> Terbaru Masuk
                    </a>
                </li>
                <li class="forum-explore-item">
                    <a href="{{ route('forum.index', ['sort' => 'top']) }}" class="{{ $sort === 'top' ? 'active' : '' }}">
                        <i class='bx bx-trophy'></i> Teratas Pekan Ini
                        <span class="forum-explore-badge" style="background-color: var(--secondary); color: #000;">TOP</span>
                    </a>
                </li>
                <li class="forum-explore-item">
                    <a href="{{ route('forum.index', ['sort' => 'unsolved']) }}" class="{{ $sort === 'unsolved' ? 'active' : '' }}">
                        <i class='bx bx-help-circle'></i> Belum Terjawab
                    </a>
                </li>
            </ul>
        </div>

        <!-- Topik -->
        <div class="forum-sidebar-box">
            <div class="forum-sidebar-title"><i class='bx bx-hash'></i> Topik</div>
            <ul class="forum-topic-list">
                @foreach ($allTags as $tag)
                <li class="forum-topic-item">
                    <a href="{{ route('forum.index', ['tag' => $tag['nama_tag']]) }}">
                        <span class="forum-topic-icon" style="background-color: {{ $tag['warna'] }}; color: #000;">
                            <i class='bx {{ $tag['icon'] }}'></i>
                        </span>
                        {{ $tag['nama_tag'] }}
                        <span class="forum-topic-count">{{ number_format($tag['post_count']) }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
            <a href="{{ route('forum.index') }}" class="forum-view-all-btn">
                <i class='bx bx-plus'></i> Lihat Semua Topik
            </a>
        </div>

        <!-- Aturan -->
        <div class="forum-sidebar-box">
            <div class="forum-sidebar-title"><i class='bx bx-shield-quarter'></i> Aturan</div>
            <ul class="forum-rules-list">
                <li><i class='bx bx-check-circle'></i> <span><strong>Snippet Kode:</strong> Jangan langsung kasih kode, tapi jelaskan logic-nya juga.</span></li>
                <li><i class='bx bx-check-circle'></i> <span><strong>Hormati Pemula:</strong> Kritik substansi, bukan orangnya.</span></li>
                <li><i class='bx bx-check-circle'></i> <span><strong>No Spam/Promo:</strong> Jualan bootcamp tanpa izin akan langsung di-ban.</span></li>
            </ul>
        </div>
    </aside>

    <!-- ========== MAIN FEED ========== -->
    <main class="forum-main-feed">

        <!-- Mobile Topic Buttons -->
        <div class="forum-mobile-header">
            <a href="{{ route('forum.index', ['sort' => 'hot']) }}" class="forum-mobile-topic-btn {{ $sort === 'hot' ? 'active' : '' }}" style="{{ $sort === 'hot' ? 'background-color:var(--secondary);color:#000;' : '' }}"><i class='bx bxs-hot'></i> Hot</a>
            <a href="{{ route('forum.index', ['sort' => 'baru']) }}" class="forum-mobile-topic-btn {{ $sort === 'baru' ? 'active' : '' }}" style="{{ $sort === 'baru' ? 'background-color:var(--secondary);color:#000;' : '' }}"><i class='bx bx-time-five'></i> Baru</a>
            <a href="{{ route('forum.index', ['sort' => 'top']) }}" class="forum-mobile-topic-btn {{ $sort === 'top' ? 'active' : '' }}" style="{{ $sort === 'top' ? 'background-color:var(--secondary);color:#000;' : '' }}"><i class='bx bx-trophy'></i> Top</a>
            @if ($is_logged_in)
            <a href="{{ route('forum.create') }}" class="forum-mobile-topic-btn" style="background-color:var(--primary);color:#FFF;"><i class='bx bx-plus'></i> Post</a>
            @endif
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('forum.index') }}" class="forum-search-bar">
            <input type="text" name="q" class="forum-search-input" placeholder="Ada bug apa hari ini? Tulis pertanyaan atau bagikan kode..." value="{{ $search }}">
            @if (!empty($tag_filter))
                <input type="hidden" name="tag" value="{{ $tag_filter }}">
            @endif
            @if (!empty($sort) && $sort !== 'hot')
                <input type="hidden" name="sort" value="{{ $sort }}">
            @endif
            <button type="submit" class="forum-search-btn"><i class='bx bx-search'></i> Kirim</button>
        </form>

        <!-- Filter Chips -->
        <div class="forum-filter-chips">
            <a href="{{ route('forum.index', array_filter(['tipe' => 'pertanyaan', 'tag' => $tag_filter ?: null])) }}" class="forum-chip {{ $tipe_filter === 'pertanyaan' ? 'active' : '' }}"><i class='bx bx-help-circle'></i> Pertanyaan</a>
            <a href="{{ route('forum.index', array_filter(['tipe' => 'diskusi', 'tag' => $tag_filter ?: null])) }}" class="forum-chip {{ $tipe_filter === 'diskusi' ? 'active' : '' }}"><i class='bx bx-chat'></i> Diskusi</a>
            <a href="{{ route('forum.index', array_filter(['tipe' => 'berbagi', 'tag' => $tag_filter ?: null])) }}" class="forum-chip {{ $tipe_filter === 'berbagi' ? 'active' : '' }}"><i class='bx bx-share-alt'></i> Berbagi</a>
            @if (!empty($tipe_filter) || !empty($tag_filter) || !empty($search))
                <a href="{{ route('forum.index') }}" class="forum-chip" style="background-color: var(--primary); color: #FFF;"><i class='bx bx-x'></i> Reset Filter</a>
            @endif
        </div>

        <!-- Active Tag Filter Notice -->
        @if (!empty($tag_filter))
            <div class="forum-announcement" style="background-color: var(--accent); border-color: var(--border);">
                <span class="forum-announcement-badge" style="background-color: #FFF; color: #000;">FILTER</span>
                <span class="forum-announcement-text" style="color: #FFF;">Menampilkan postingan dengan tag: <strong>#{{ $tag_filter }}</strong></span>
                <a href="{{ route('forum.index') }}" class="forum-announcement-dismiss" style="color: #FFF;">&times;</a>
            </div>
        @endif

        <!-- Sort Tabs -->
        <div class="forum-sort-tabs">
            <a href="{{ route('forum.index', array_filter(['sort' => 'hot', 'tag' => $tag_filter ?: null, 'tipe' => $tipe_filter ?: null])) }}" class="forum-sort-tab {{ $sort === 'hot' ? 'active' : '' }}"><i class='bx bxs-hot'></i> Hot</a>
            <a href="{{ route('forum.index', array_filter(['sort' => 'baru', 'tag' => $tag_filter ?: null, 'tipe' => $tipe_filter ?: null])) }}" class="forum-sort-tab {{ $sort === 'baru' ? 'active' : '' }}"><i class='bx bx-time-five'></i> Baru</a>
            <a href="{{ route('forum.index', array_filter(['sort' => 'top', 'tag' => $tag_filter ?: null, 'tipe' => $tipe_filter ?: null])) }}" class="forum-sort-tab {{ $sort === 'top' ? 'active' : '' }}"><i class='bx bx-up-arrow-alt'></i> Top</a>
            <a href="{{ route('forum.index', array_filter(['sort' => 'unsolved', 'tag' => $tag_filter ?: null])) }}" class="forum-sort-tab {{ $sort === 'unsolved' ? 'active' : '' }}"><i class='bx bx-help-circle'></i> Belum Solved</a>
        </div>

        <!-- Announcements -->
        @foreach ($announcements as $ann)
        <div class="forum-announcement">
            <span class="forum-announcement-badge">PINNED</span>
            <span class="forum-announcement-text">
                <a href="{{ route('forum.show', ['id' => $ann['id_post']]) }}">{{ $ann['judul'] }}</a>
            </span>
        </div>
        @endforeach

        <!-- Post List -->
        @if (empty($posts))
            <div class="forum-empty-state">
                <i class='bx bx-message-square-detail'></i>
                <h3>Belum Ada Postingan</h3>
                <p>Jadilah yang pertama memulai diskusi!</p>
                @if ($is_logged_in)
                    <a href="{{ route('forum.create') }}" class="forum-fab" style="margin-top: 1rem; display: inline-flex;"><i class='bx bx-plus'></i> Buat Postingan</a>
                @endif
            </div>
        @else
            @foreach ($posts as $post)
                @php
                    $tags = $post['tags'] ?? [];
                    $userVote = $post['user_vote'] ?? null;
                    $bookmarked = $post['bookmarked'] ?? false;
                    $codeBlock = extractCodeBlock($post['konten']);
                    $textPreview = getTextPreview($post['konten']);
                @endphp
            <div class="forum-post-card" id="post-{{ $post['id_post'] }}">
                <!-- Vote Column -->
                <div class="forum-post-votes">
                    <button class="forum-vote-btn {{ $userVote === 'up' ? 'voted-up' : '' }}" 
                            onclick="vote({{ $post['id_post'] }}, 'post', 'up')" 
                            title="Upvote"
                            {{ !$is_logged_in ? 'disabled' : '' }}>
                        <i class='bx bxs-up-arrow'></i>
                    </button>
                    <span class="forum-vote-count" id="vote-count-{{ $post['id_post'] }}">{{ $post['vote_count'] }}</span>
                    <button class="forum-vote-btn {{ $userVote === 'down' ? 'voted-down' : '' }}" 
                            onclick="vote({{ $post['id_post'] }}, 'post', 'down')"
                            title="Downvote"
                            {{ !$is_logged_in ? 'disabled' : '' }}>
                        <i class='bx bxs-down-arrow'></i>
                    </button>
                </div>

                <!-- Post Body -->
                <div class="forum-post-body">
                    <!-- Meta -->
                    <div class="forum-post-meta">
                        <span class="forum-post-avatar">{{ getInitial($post['username']) }}</span>
                        <span class="forum-post-username">{{ $post['username'] }}</span>
                        <span class="forum-post-role {{ $post['user_role'] === 'Pengajar' ? 'role-pengajar' : ($post['user_role'] === 'Admin' ? 'role-admin' : '') }}">{{ $post['user_role'] }}</span>
                        @if ($post['is_pinned'])
                            <span class="forum-pinned-badge"><i class='bx bxs-pin'></i> Pinned</span>
                        @endif
                        <span class="forum-tipe-badge tipe-{{ $post['tipe'] }}">{{ $post['tipe'] === 'pertanyaan' ? '❓ Pertanyaan' : ($post['tipe'] === 'diskusi' ? '💬 Diskusi' : '📤 Berbagi') }}</span>
                        <span class="forum-post-time">{{ timeAgo($post['tgl_post']) }}</span>
                    </div>

                    <!-- Title -->
                    <a href="{{ route('forum.show', ['id' => $post['id_post']]) }}" class="forum-post-title">
                        {{ $post['judul'] }}
                    </a>

                    <!-- Preview -->
                    <div class="forum-post-preview">
                        {{ $textPreview }}
                    </div>

                    <!-- Code Block Preview -->
                    @if ($codeBlock)
                    <div class="forum-code-preview" data-lang="{{ $codeBlock['lang'] }}">
<code>{{ $codeBlock['code'] }}</code>
                    </div>
                    @endif

                    <!-- Tags -->
                    @if (!empty($tags))
                    <div class="forum-post-tags">
                        @foreach ($tags as $t)
                        <a href="{{ route('forum.index', ['tag' => $t['nama_tag']]) }}" class="forum-tag" style="border-left: 4px solid {{ $t['warna'] }};">
                            #{{ $t['nama_tag'] }}
                        </a>
                        @endforeach
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="forum-post-actions">
                        <a href="{{ route('forum.show', ['id' => $post['id_post']]) }}" class="forum-action-btn">
                            <i class='bx bx-message-rounded-dots'></i> {{ $post['jawaban_count'] }} Jawaban
                        </a>
                        <button class="forum-action-btn" onclick="sharePost({{ $post['id_post'] }}, '{{ addslashes($post['judul']) }}')">
                            <i class='bx bx-share-alt'></i> Bagikan
                        </button>
                        @if ($is_logged_in)
                        <button class="forum-action-btn" id="bookmark-btn-{{ $post['id_post'] }}" onclick="toggleBookmark({{ $post['id_post'] }})">
                            <i class='bx {{ $bookmarked ? 'bxs-bookmark' : 'bx-bookmark' }}'></i> 
                            <span>{{ $bookmarked ? 'Tersimpan' : 'Simpan' }}</span>
                        </button>
                        @endif

                        <!-- Status Badge -->
                        @if ($post['tipe'] === 'pertanyaan')
                        <span class="forum-status status-{{ $post['status'] }}">
                            {{ $post['status'] === 'solved' ? '✓ SOLVED' : '○ OPEN' }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Load More / Pagination -->
            @if ($total_pages > 1)
                @if ($page < $total_pages)
                    <a href="{{ route('forum.index', array_filter(['page' => $page + 1, 'sort' => $sort, 'tag' => $tag_filter ?: null, 'tipe' => $tipe_filter ?: null, 'q' => $search ?: null])) }}" class="forum-load-more">
                        <i class='bx bx-chevron-down'></i> Muat {{ min($per_page, $total_posts - $page * $per_page) }} Thread Lainnya
                    </a>
                @endif
            @endif
        @endif
    </main>

    <!-- ========== SIDEBAR RIGHT ========== -->
    <aside class="forum-sidebar-right">
        <!-- Create Post CTA -->
        @if ($is_logged_in)
        <a href="{{ route('forum.create') }}" class="forum-fab" style="width: 100%; justify-content: center;">
            <i class='bx bx-edit'></i> Buat Postingan
        </a>
        @else
        <a href="{{ route('login') }}" class="forum-fab" style="width: 100%; justify-content: center; background-color: var(--accent);">
            <i class='bx bx-log-in'></i> Masuk untuk Posting
        </a>
        @endif

        <!-- About Forum -->
        <div class="forum-sidebar-box">
            <div class="forum-sidebar-title"><i class='bx bx-info-circle'></i> Tentang Forum</div>
            <div class="forum-about-text">
                Wadah tanya-jawab teknis, bedah arsitektur kode, dan mentoring langsung bersama pengajar dan software engineer se-Indonesia.
            </div>
            <div class="forum-stats-row">
                <div class="forum-stat-item">
                    <span class="forum-stat-number">{{ number_format($totalMembers) }}</span>
                    <span class="forum-stat-label">Anggota</span>
                </div>
                <div class="forum-stat-item">
                    <span class="forum-stat-number">{{ number_format($totalSolved) }}</span>
                    <span class="forum-stat-label">Solved</span>
                </div>
            </div>
        </div>

        <!-- Top Contributors -->
        @if (!empty($topContributors))
        <div class="forum-sidebar-box">
            <div class="forum-sidebar-title"><i class='bx bx-crown'></i> Top Kontributor</div>
            <ul class="forum-top-list">
                @foreach ($topContributors as $idx => $contrib)
                <li class="forum-top-item">
                    <span class="forum-top-rank">#{{ $idx + 1 }}</span>
                    <span class="forum-top-avatar" style="{{ $idx === 0 ? 'background-color: #FFD700;' : ($idx === 1 ? 'background-color: #C0C0C0;' : ($idx === 2 ? 'background-color: #CD7F32;' : '')) }}">{{ getInitial($contrib['username']) }}</span>
                    <span class="forum-top-name">{{ $contrib['username'] }}</span>
                    <span class="forum-top-score">{{ number_format($contrib['total_score']) }} XP</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Bantuan Instruktur -->
        <div class="forum-sidebar-box">
            <div class="forum-sidebar-title"><i class='bx bx-support'></i> Butuh Bantuan?</div>
            <div class="forum-cta-box">
                <p>Stuck berjam-jam pada satu error? Ajukan pertanyaan atau hubungi instruktur.</p>
                <a href="{{ route('forum.create') }}" class="forum-cta-btn" style="margin-bottom: 8px;">
                    <i class='bx bx-edit'></i> Ajukan Pertanyaan
                </a>
                <a href="{{ route('home') }}#kontak" class="forum-cta-btn" style="background-color: #25D366; color: #FFF;">
                    <i class='bx bxl-whatsapp'></i> Chat CS
                </a>
            </div>
        </div>

        <!-- Pelajari Format -->
        <div class="forum-sidebar-box">
            <div class="forum-sidebar-title"><i class='bx bx-book-open'></i> Tips Posting</div>
            <ul class="forum-rules-list">
                <li><i class='bx bx-code-alt'></i> <span>Gunakan blok kode untuk membagikan snippet</span></li>
                <li><i class='bx bx-hash'></i> <span>Tambahkan tag agar pertanyaan mudah ditemukan</span></li>
                <li><i class='bx bx-check-double'></i> <span>Tandai jawaban terbaik sebagai "Solusi"</span></li>
            </ul>
        </div>
    </aside>
</div>
@endsection

@section('footer')
    @include('partials.forum_footer')
@endsection

@section('scripts')
<script>
// ========== VOTING SYSTEM (AJAX) ==========
function vote(id, type, voteType) {
    @if (!$is_logged_in)
    NeoToast('Silakan login terlebih dahulu untuk voting.', 'warning');
    return;
    @endif

    fetch('{{ route("api.forum.vote") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            id: id, 
            type: type, 
            vote: voteType,
            csrf_token: '{{ csrf_token() }}'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // Update vote count
            const countEl = document.getElementById('vote-count-' + id);
            if (countEl) countEl.textContent = data.new_count;

            // Update button styles
            const card = document.getElementById('post-' + id);
            if (card) {
                const upBtn = card.querySelector('.forum-vote-btn:first-child');
                const downBtn = card.querySelector('.forum-vote-btn:last-of-type');
                upBtn.classList.remove('voted-up');
                downBtn.classList.remove('voted-down');
                if (data.action === 'up') upBtn.classList.add('voted-up');
                if (data.action === 'down') downBtn.classList.add('voted-down');
            }
        } else {
            NeoToast(data.message || 'Gagal memproses vote.', 'error');
        }
    })
    .catch(() => NeoToast('Terjadi kesalahan jaringan.', 'error'));
}

// ========== BOOKMARK SYSTEM (AJAX) ==========
function toggleBookmark(postId) {
    fetch('{{ route("api.forum.simpan") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            id_post: postId,
            csrf_token: '{{ csrf_token() }}'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const btn = document.getElementById('bookmark-btn-' + postId);
            if (btn) {
                const icon = btn.querySelector('i');
                const text = btn.querySelector('span');
                if (data.action === 'saved') {
                    icon.className = 'bx bxs-bookmark';
                    text.textContent = 'Tersimpan';
                    NeoToast('Postingan disimpan!', 'success');
                } else {
                    icon.className = 'bx bx-bookmark';
                    text.textContent = 'Simpan';
                    NeoToast('Bookmark dihapus.', 'success');
                }
            }
        } else {
            NeoToast(data.message || 'Gagal menyimpan.', 'error');
        }
    })
    .catch(() => NeoToast('Terjadi kesalahan jaringan.', 'error'));
}

// ========== SHARE ==========
function sharePost(postId, title) {
    const url = '{{ url("/forum") }}/' + postId;
    
    if (navigator.share) {
        navigator.share({ title: title, url: url });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            NeoToast('Link berhasil disalin ke clipboard!', 'success');
        }).catch(() => {
            // Fallback
            const input = document.createElement('input');
            input.value = url;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            NeoToast('Link berhasil disalin!', 'success');
        });
    }
}
</script>
@endsection
