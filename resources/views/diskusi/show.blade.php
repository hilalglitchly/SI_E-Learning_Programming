@extends('layouts.app')

@section('title', 'Diskusi: ' . $diskusi['judul_diskusi'])

@section('styles')
    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        body {
            --classroom-bg: var(--background);
            --classroom-accent: var(--primary);
            --brutal-border: 3px solid var(--border);
            --brutal-shadow: 5px 5px 0px var(--shadow-color);
            background-color: var(--classroom-bg);
        }

        .brutal-card {
            background-color: var(--card);
            color: var(--card-foreground);
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .brutal-hover:hover {
            box-shadow: 0px 0px 0px var(--border) !important;
            transform: translate(5px, 5px) !important;
        }

        .btn-accent {
            display: inline-block;
            background-color: var(--accent);
            color: var(--accent-foreground);
            padding: 0.8rem 1.5rem;
            text-transform: uppercase;
            font-weight: bold;
            font-family: inherit;
            font-size: 1rem;
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-align: center;
        }

        .btn-accent:hover {
            background-color: var(--primary);
            color: var(--primary-foreground);
            box-shadow: 0px 0px 0px var(--border) !important;
            transform: translate(5px, 5px) !important;
        }
    </style>
@endsection

@section('content')
<!-- MAIN CONTENT -->
<main class="neo-layout-container" style="background-color: var(--classroom-bg);">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem; margin-top: 1rem; padding-left: 2rem;">
        <a href="{{ route('kelas.show', ['id' => $diskusi['id_kelas']]) }}" class="brutal-hover neo-box" style="background-color: var(--classroom-accent); padding: 0.5rem 1rem; text-decoration: none; color: var(--primary-foreground); font-weight: 800; font-size: 0.9rem; display: inline-block;">← KEMBALI KE KELAS</a>
    </div>

    <div class="neo-content-inner" style="max-width: 900px; margin: 0 auto; padding: 0 2rem 3rem 2rem;">
        
        @if (!empty($pesan))
            <div class="brutal-card" style="background-color: #a8e6cf; color: black; font-weight: bold;">{{ $pesan }}</div>
        @endif

        <!-- Topik Utama -->
        <div class="brutal-card" style="border-left: 10px solid var(--primary); margin-bottom: 2.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <h1 style="font-size: 1.8rem; font-weight: 900; text-transform: uppercase; margin: 0;">{{ $diskusi['judul_diskusi'] }}</h1>
                <span style="font-size: 0.85rem; background-color: var(--muted); padding: 0.3rem 0.8rem; font-weight: bold; color: var(--foreground); border: 2px solid var(--border);">{{ date('d M Y, H:i', strtotime($diskusi['tgl_post'])) }}</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 0.8rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--border);">
                <div style="width: 35px; height: 35px; background-color: var(--foreground); color: var(--background); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">👤</div>
                <div>
                    <div style="font-weight: 800; font-size: 1rem;">{{ $diskusi['username'] }}</div>
                    <div style="font-size: 0.8rem; font-weight: bold; color: var(--primary); text-transform: uppercase;">{{ $diskusi['role'] }}</div>
                </div>
            </div>

            <div style="font-size: 1.1rem; line-height: 1.6; font-weight: 500;" class="ql-editor-content">
                {!! sanitize_rich_text($diskusi['isi_diskusi']) !!}
            </div>
        </div>

        <!-- Daftar Balasan -->
        <h3 style="font-size: 1.4rem; text-transform: uppercase; font-weight: 800; margin-bottom: 1.5rem;">Balasan ({{ count($balasanList) }})</h3>
        
        <div style="margin-bottom: 3rem;">
            @if (count($balasanList) > 0)
                @foreach ($balasanList as $balas)
                    <div class="brutal-card" style="margin-bottom: 1rem; background-color: var(--background);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <strong style="font-size: 1rem;">{{ $balas['username'] }}</strong>
                                <span style="font-size: 0.75rem; background-color: var(--accent); color: var(--accent-foreground); padding: 0.1rem 0.5rem; font-weight: bold; border: 1px solid var(--border); text-transform: uppercase;">{{ $balas['role'] }}</span>
                            </div>
                            <span style="font-size: 0.8rem; color: var(--muted-foreground); font-weight: bold;">{{ date('d M Y, H:i', strtotime($balas['tgl_balasan'])) }}</span>
                        </div>
                        <div style="font-size: 1rem; line-height: 1.5;" class="ql-editor-content">
                            {!! sanitize_rich_text($balas['isi_balasan']) !!}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="brutal-card" style="text-align: center; padding: 2rem; background-color: var(--background);">
                    <p style="font-weight: bold; color: var(--muted-foreground); margin: 0;">Belum ada balasan. Jadilah yang pertama menanggapi!</p>
                </div>
            @endif
        </div>

        <!-- Form Kirim Balasan -->
        @if (strtolower($role) === 'murid' || $role === 'Pengajar' || strtolower($role) === 'admin')
        <div class="brutal-card" style="border-top: 6px solid var(--accent);">
            <h4 style="text-transform: uppercase; font-weight: 800; margin-bottom: 1rem; font-size: 1.2rem;">Kirim Balasan</h4>
            <form method="POST" action="{{ route('diskusi.balasan.store', ['id' => $id_diskusi]) }}">
                @csrf
                <input type="hidden" name="action" value="post_balasan">
                <div style="margin-bottom: 2.5rem;">
                    <input type="hidden" name="isi_balasan" id="hidden_isi_balasan">
                    <div id="editor_balasan" style="height: 150px;"></div>
                </div>
                <button type="submit" class="btn-accent" style="width: 100%;">Kirim Balasan</button>
            </form>
        </div>
        @endif

    </div>
</main>
@endsection

@section('scripts')
<!-- Quill.js Library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link'],
        ['clean']
    ];

    var editorBalasanContainer = document.getElementById('editor_balasan');
    if (editorBalasanContainer) {
        var quillBalasan = new Quill('#editor_balasan', {
            theme: 'snow',
            placeholder: 'Tulis balasan Anda di sini...',
            modules: { toolbar: toolbarOptions }
        });
        
        var formBalasan = editorBalasanContainer.closest('form');
        formBalasan.addEventListener('submit', function(e) {
            var html = quillBalasan.root.innerHTML;
            if (quillBalasan.getText().trim().length === 0 && !html.includes('<img')) {
                e.preventDefault();
                NeoAlert('Peringatan', 'Balasan tidak boleh kosong!', 'warning');
                return false;
            }
            document.getElementById('hidden_isi_balasan').value = html;
        });
    }
</script>
@endsection

