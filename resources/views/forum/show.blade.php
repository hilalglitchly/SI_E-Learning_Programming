@extends('layouts.app')

@section('title', $post['judul'] . ' - E-FORVM')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}?v={{ file_exists(public_path('assets/css/forum.css')) ? filemtime(public_path('assets/css/forum.css')) : time() }}">
    <!-- Highlight.js for code blocks -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <style>
        .forum-detail-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 1rem;
            min-height: calc(100vh - 120px);
        }
        
        .forum-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.5rem;
            color: var(--foreground);
            text-decoration: none;
            font-weight: 800;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        .forum-back-btn:hover {
            color: var(--accent);
        }
        
        .post-detail-card {
            background-color: var(--card);
            border: 3px solid var(--border);
            box-shadow: 8px 8px 0px var(--shadow-color);
            margin-bottom: 2rem;
            display: flex;
        }
        
        .post-detail-body {
            flex: 1;
            padding: 20px 24px;
            min-width: 0;
        }
        
        .post-detail-title {
            font-size: 1.8rem;
            font-weight: 900;
            margin: 15px 0;
            line-height: 1.2;
            color: var(--foreground);
        }
        
        .post-detail-content {
            font-size: 1.05rem;
            line-height: 1.6;
            color: var(--foreground);
            margin-bottom: 20px;
        }
        
        /* Quill Content Styling overrides */
        .post-detail-content p { margin-bottom: 15px; }
        .post-detail-content pre { 
            background: #282c34; 
            color: #abb2bf;
            padding: 15px; 
            border: 3px solid var(--border); 
            border-radius: 0;
            overflow-x: auto;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .post-detail-content blockquote {
            border-left: 5px solid var(--secondary);
            padding-left: 15px;
            margin-left: 0;
            color: var(--muted-foreground);
            font-style: italic;
        }
        .post-detail-content ul, .post-detail-content ol {
            margin-bottom: 15px;
            padding-left: 20px;
        }
        
        .answers-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid var(--border);
            padding-bottom: 10px;
        }
        
        .answers-header h3 {
            font-size: 1.4rem;
            font-weight: 900;
            text-transform: uppercase;
        }
        
        .answer-card {
            background-color: var(--card);
            border: 2px solid var(--border);
            margin-bottom: 1.5rem;
            display: flex;
            transition: transform 0.1s, box-shadow 0.1s;
            position: relative;
        }
        
        .answer-card:hover {
            box-shadow: 4px 4px 0px var(--shadow-color);
            transform: translate(-2px, -2px);
        }
        
        .answer-card.is-solution {
            border: 3px solid #22C55E;
            box-shadow: 6px 6px 0px #22C55E;
        }
        
        .solution-badge {
            background-color: #22C55E;
            color: white;
            font-weight: 900;
            font-size: 0.8rem;
            padding: 4px 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-bottom: 2px solid var(--border);
            width: 100%;
        }
        
        .answer-body {
            flex: 1;
            padding: 16px 20px;
            min-width: 0;
        }
        
        .answer-content {
            margin: 15px 0;
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--foreground);
        }
        
        /* Quill formatting for answer */
        .answer-content pre {
            background: #282c34;
            padding: 12px;
            border: 2px solid var(--border);
            overflow-x: auto;
        }
        
        .mark-solved-btn {
            background-color: transparent;
            border: 2px solid #22C55E;
            color: #22C55E;
            font-weight: 800;
            padding: 6px 12px;
            font-size: 0.8rem;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.1s;
        }
        
        .mark-solved-btn:hover {
            background-color: #22C55E;
            color: white;
            transform: translate(-2px, -2px);
            box-shadow: 3px 3px 0px var(--shadow-color);
        }
        
        .reply-box {
            background-color: var(--card);
            border: 3px solid var(--border);
            box-shadow: 6px 6px 0px var(--shadow-color);
            padding: 20px;
            margin-top: 3rem;
        }
        
        .reply-box h3 {
            font-size: 1.2rem;
            font-weight: 900;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        #editor-container {
            height: 200px;
            background-color: var(--background);
            font-family: inherit;
            font-size: 1rem;
        }
        
        .ql-toolbar {
            background-color: var(--muted);
            border-color: var(--border) !important;
            border-width: 3px 3px 0 3px !important;
        }
        
        .ql-container {
            border-color: var(--border) !important;
            border-width: 0 3px 3px 3px !important;
        }
        
        .submit-reply-btn {
            margin-top: 15px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 900;
            text-transform: uppercase;
            background-color: var(--primary);
            color: white;
            border: 3px solid var(--border);
            box-shadow: 4px 4px 0px var(--shadow-color);
            cursor: pointer;
            transition: all 0.1s;
        }
        
        .submit-reply-btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px var(--shadow-color);
        }
        
        @media (max-width: 768px) {
            .post-detail-card, .answer-card {
                flex-direction: column;
            }
            .forum-post-votes {
                flex-direction: row;
                border-right: none;
                border-bottom: 3px solid var(--border);
                justify-content: center;
                gap: 20px;
            }
        }
    </style>
@endsection

@section('content')
<div class="forum-detail-container">
    <a href="{{ route('forum.index') }}" class="forum-back-btn"><i class='bx bx-arrow-back'></i> Kembali ke Forum</a>
    
    @if (!empty($msg))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                NeoToast('{{ addslashes($msg) }}', '{{ $msg_type }}');
            });
        </script>
    @endif

    <!-- MAIN POST -->
    <article class="post-detail-card">
        <!-- Vote Column -->
        <div class="forum-post-votes">
            <button class="forum-vote-btn {{ $userVotePost === 'up' ? 'voted-up' : '' }}" 
                    onclick="vote({{ $post['id_post'] }}, 'post', 'up')" title="Upvote" {{ !$is_logged_in ? 'disabled' : '' }}>
                <i class='bx bxs-up-arrow'></i>
            </button>
            <span class="forum-vote-count" id="vote-count-post-{{ $post['id_post'] }}">{{ $post['vote_count'] }}</span>
            <button class="forum-vote-btn {{ $userVotePost === 'down' ? 'voted-down' : '' }}" 
                    onclick="vote({{ $post['id_post'] }}, 'post', 'down')" title="Downvote" {{ !$is_logged_in ? 'disabled' : '' }}>
                <i class='bx bxs-down-arrow'></i>
            </button>
        </div>
        
        <!-- Body -->
        <div class="post-detail-body">
            <div class="forum-post-meta" style="font-size: 0.9rem;">
                <span class="forum-post-avatar">{{ getInitial($post['username']) }}</span>
                <span class="forum-post-username">{{ $post['username'] }}</span>
                <span class="forum-post-role {{ $post['user_role'] === 'Pengajar' ? 'role-pengajar' : ($post['user_role'] === 'Admin' ? 'role-admin' : '') }}">{{ $post['user_role'] }}</span>
                <span class="forum-post-time">{{ timeAgo($post['tgl_post']) }}</span>
            </div>
            
            <h1 class="post-detail-title">{{ $post['judul'] }}</h1>
            
            @if (!empty($tags))
            <div class="forum-post-tags" style="margin-bottom: 20px;">
                <span class="forum-tipe-badge tipe-{{ $post['tipe'] }}" style="margin-right: 10px; padding: 4px 10px; font-size: 0.8rem;">{{ $post['tipe'] === 'pertanyaan' ? '❓ Pertanyaan' : ($post['tipe'] === 'diskusi' ? '💬 Diskusi' : '📤 Berbagi') }}</span>
                @foreach ($tags as $t)
                <a href="{{ route('forum.index', ['tag' => $t['nama_tag']]) }}" class="forum-tag" style="border-left: 4px solid {{ $t['warna'] }}; padding: 4px 10px; font-size: 0.8rem;">
                    #{{ $t['nama_tag'] }}
                </a>
                @endforeach
                
                @if ($post['tipe'] === 'pertanyaan')
                <span class="forum-status status-{{ $post['status'] }}" style="margin-left: auto; padding: 4px 10px; font-size: 0.8rem;">
                    {{ $post['status'] === 'solved' ? '✓ SOLVED' : '○ OPEN' }}
                </span>
                @endif
            </div>
            @endif
            
            <div class="post-detail-content">
                {!! $post['konten'] !!}
            </div>
            
            <div class="forum-post-actions" style="margin-top: 20px; padding-top: 15px;">
                <button class="forum-action-btn" onclick="sharePost({{ $post['id_post'] }}, '{{ addslashes($post['judul']) }}')">
                    <i class='bx bx-share-alt'></i> Bagikan
                </button>
                @if ($is_logged_in)
                <button class="forum-action-btn" id="bookmark-btn-{{ $post['id_post'] }}" onclick="toggleBookmark({{ $post['id_post'] }})">
                    <i class='bx {{ $isBookmarked ? 'bxs-bookmark' : 'bx-bookmark' }}'></i> 
                    <span>{{ $isBookmarked ? 'Tersimpan' : 'Simpan' }}</span>
                </button>
                @endif
                <span class="forum-action-btn" style="cursor: default;"><i class='bx bx-show'></i> {{ $post['views'] }} Dilihat</span>
            </div>
        </div>
    </article>

    <!-- ANSWERS SECTION -->
    <div id="answers">
        <div class="answers-header">
            <h3>{{ count($answers) }} Jawaban</h3>
        </div>
        
        @if (empty($answers))
            <div class="forum-empty-state" style="padding: 2rem; margin-bottom: 2rem; border-width: 2px; box-shadow: 4px 4px 0px var(--shadow-color);">
                <i class='bx bx-comment-detail' style="font-size: 2.5rem;"></i>
                <p>Belum ada jawaban. Jadilah yang pertama memberikan solusi!</p>
            </div>
        @else
            @foreach ($answers as $ans)
                @php
                    $ansVote = $userVoteAnswers[$ans['id_jawaban']] ?? null;
                @endphp
            <div class="answer-card {{ $ans['is_solusi'] ? 'is-solution' : '' }}" id="jawaban-{{ $ans['id_jawaban'] }}">
                @if ($ans['is_solusi'])
                <div class="solution-badge" style="position: absolute; top: 0; left: 0; right: 0; z-index: 1;">
                    <i class='bx bx-check-circle'></i> JAWABAN TERBAIK
                </div>
                @endif
                
                <!-- Vote Column -->
                <div class="forum-post-votes" style="{{ $ans['is_solusi'] ? 'padding-top: 40px;' : '' }}">
                    <button class="forum-vote-btn {{ $ansVote === 'up' ? 'voted-up' : '' }}" 
                            onclick="vote({{ $ans['id_jawaban'] }}, 'jawaban', 'up')" title="Upvote" {{ !$is_logged_in ? 'disabled' : '' }}>
                        <i class='bx bxs-up-arrow'></i>
                    </button>
                    <span class="forum-vote-count" id="vote-count-jawaban-{{ $ans['id_jawaban'] }}">{{ $ans['vote_count'] }}</span>
                    <button class="forum-vote-btn {{ $ansVote === 'down' ? 'voted-down' : '' }}" 
                            onclick="vote({{ $ans['id_jawaban'] }}, 'jawaban', 'down')" title="Downvote" {{ !$is_logged_in ? 'disabled' : '' }}>
                        <i class='bx bxs-down-arrow'></i>
                    </button>
                </div>
                
                <!-- Answer Body -->
                <div class="answer-body" style="{{ $ans['is_solusi'] ? 'padding-top: 40px;' : '' }}">
                    <div class="forum-post-meta">
                        <span class="forum-post-avatar" style="width: 28px; height: 28px; font-size: 0.7rem;">{{ getInitial($ans['username']) }}</span>
                        <span class="forum-post-username">{{ $ans['username'] }}</span>
                        <span class="forum-post-role {{ $ans['user_role'] === 'Pengajar' ? 'role-pengajar' : ($ans['user_role'] === 'Admin' ? 'role-admin' : '') }}">{{ $ans['user_role'] }}</span>
                        <span class="forum-post-time">{{ timeAgo($ans['tgl_jawaban']) }}</span>
                    </div>
                    
                    <div class="answer-content">
                        {!! $ans['konten'] !!}
                    </div>
                    
                    <!-- Post owner can mark as solved -->
                    @if ($is_logged_in && ($id_user == $post['id_user'] || strtolower($role) === 'admin') && $post['tipe'] === 'pertanyaan' && !$ans['is_solusi'])
                    <div style="margin-top: 15px; text-align: right;">
                        <form method="POST" action="{{ route('forum.post', ['id' => $post['id_post']]) }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="id_jawaban" value="{{ $ans['id_jawaban'] }}">
                            <button type="submit" name="mark_solved" value="1" class="mark-solved-btn">
                                <i class='bx bx-check'></i> Tandai sebagai Solusi
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- REPLY FORM -->
    <div class="reply-box">
        <h3>Berikan Jawaban Anda</h3>
        @if ($is_logged_in)
            <form method="POST" action="{{ route('forum.post', ['id' => $post['id_post']]) }}" id="replyForm">
                @csrf
                <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">
                <input type="hidden" name="konten" id="hidden_konten">
                
                <div id="editor-container"></div>
                
                <button type="submit" name="submit_jawaban" value="1" class="submit-reply-btn" onclick="return submitReply()">
                    <i class='bx bx-send'></i> Kirim Jawaban
                </button>
            </form>
        @else
            <div style="padding: 20px; background-color: var(--muted); border: 2px solid var(--border); text-align: center;">
                <p style="margin-bottom: 10px; font-weight: 700;">Anda harus masuk untuk ikut berdiskusi.</p>
                <a href="{{ route('login') }}" class="forum-fab" style="font-size: 0.8rem; padding: 8px 16px;"><i class='bx bx-log-in'></i> Masuk Sekarang</a>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<!-- Highlight.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
// Initialize Code Highlighting
document.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('pre code').forEach((el) => {
        hljs.highlightElement(el);
    });
});

// Initialize Quill Editor if logged in
@if ($is_logged_in)
var quill = new Quill('#editor-container', {
    theme: 'snow',
    placeholder: 'Tulis jawaban Anda di sini (Gunakan ikon < > untuk memasukkan kode)...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image'],
            ['clean']
        ]
    }
});

function submitReply() {
    var konten = quill.root.innerHTML;
    // Cek jika kosong
    if (quill.getText().trim().length === 0 && !konten.includes('<img')) {
        NeoToast('Jawaban tidak boleh kosong!', 'warning');
        return false;
    }
    document.getElementById('hidden_konten').value = konten;
    return true;
}
@endif

// ========== VOTING SYSTEM ==========
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
            const countEl = document.getElementById('vote-count-' + type + '-' + id);
            if (countEl) countEl.textContent = data.new_count;

            const container = type === 'post' ? document.querySelector('.post-detail-card') : document.getElementById('jawaban-' + id);
            if (container) {
                const upBtn = container.querySelector('.forum-vote-btn:first-child');
                const downBtn = container.querySelector('.forum-vote-btn:last-of-type');
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

// ========== BOOKMARK SYSTEM ==========
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

function sharePost(postId, title) {
    const url = window.location.href.split('#')[0]; // Remove hash if any
    if (navigator.share) {
        navigator.share({ title: title, url: url });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            NeoToast('Link berhasil disalin ke clipboard!', 'success');
        });
    }
}
</script>
@endsection
