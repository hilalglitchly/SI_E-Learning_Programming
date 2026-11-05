@php
$is_logged_in = auth()->check();
$user = auth()->user();
$role = $user ? strtolower($user->role) : '';
$username = $user ? $user->username : 'Guest';
@endphp

<script>
    if (localStorage.getItem("theme") === "dark") {
        document.documentElement.classList.add("dark-mode");
    }
</script>

<!-- TOP NAVBAR (Floating Neo-Brutalism Style) -->
<nav class="neo-navbar" style="width: 96%; max-width: 1400px; margin: 20px auto; position: sticky; top: 20px; z-index: 1000; border: 3px solid #000; box-shadow: 8px 8px 0px #000; border-radius: 0; background-color: #FFD700; color: #000;">
    <a href="{{ $is_logged_in ? route('dashboard') : route('home') }}" class="neo-navbar-brand" style="font-weight: 900; color: #000; display: flex; align-items: center; gap: 8px; text-decoration: none; line-height: 1.1;">
        <span style="font-size: 1.8rem; font-family: monospace; font-weight: 900;">&lt;/&gt;</span>
        <div style="display: flex; flex-direction: column;">
            <span>E-LEARNING</span>
            <span>PROGRAMMING</span>
        </div>
    </a>
    
    <ul class="neo-navbar-menu">
        <li>
            <a id="nav-beranda" href="{{ $is_logged_in ? route('dashboard') : route('home') }}" style="color: #000;" class="{{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                <i class='bx bxs-home'></i> BERANDA
            </a>
        </li>
        
        @if ($role === 'murid')
            <li><a href="{{ route('tugas.index') }}" style="color: #000;" class="{{ request()->routeIs('tugas.index') ? 'active' : '' }}"><i class='bx bx-task'></i> TUGAS</a></li>
            <li><a href="{{ route('enroll.index') }}" style="color: #000;" class="{{ request()->routeIs('enroll.*') ? 'active' : '' }}"><i class='bx bx-search'></i> CARI KELAS</a></li>
        @elseif ($role === 'pengajar')
            <li><a href="{{ route('tugas.index') }}" style="color: #000;" class="{{ request()->routeIs('tugas.index') || request()->routeIs('penilaian.*') ? 'active' : '' }}"><i class='bx bx-task'></i> NILAI TUGAS</a></li>
        @elseif ($role === 'admin')
            <li><a href="{{ route('admin.users.create') }}" style="color: #000;" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class='bx bxs-user-plus'></i> TAMBAH USER</a></li>
            <li><a href="{{ route('laporan.index') }}" style="color: #000;" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}"><i class='bx bxs-report'></i> LAPORAN</a></li>
        @elseif ($role === 'pimpinan')
            <li><a href="{{ route('laporan.index') }}" style="color: #000;" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}"><i class='bx bxs-report'></i> LAPORAN</a></li>
        @endif
        
        <li><a href="{{ route('forum.index') }}" style="color: #000;" class="{{ request()->routeIs('forum.*') ? 'active' : '' }}"><i class='bx bx-message-rounded-dots'></i> FORUM</a></li>

        @if ($is_logged_in)
            <li><a href="{{ route('profil.index') }}" style="color: #000;" class="{{ request()->routeIs('profil.*') ? 'active' : '' }}"><i class='bx bxs-user-circle'></i> AKUN</a></li>
        @else
            <li><a id="nav-katalog" href="{{ route('home') }}#katalog" style="color: #000;"><i class='bx bx-search'></i> KATALOG</a></li>
            <li><a id="nav-faq" href="{{ route('home') }}#faq" style="color: #000;"><i class='bx bx-question-mark'></i> FAQ</a></li>
            <li><a id="nav-kontak" href="{{ route('home') }}#kontak" style="color: #000;"><i class='bx bxs-contact'></i> KONTAK</a></li>
        @endif
    </ul>

    <div class="neo-navbar-user">
        <button title="Ganti Tema" id="theme-toggle" onclick="toggleDarkMode()" class="neo-navbar-btn brutal-hover" style="padding: 8px; font-size: 1.2rem; line-height: 1; background-color: #FFF; border: 3px solid #000; box-shadow: 3px 3px 0px #000; color: #000; margin-right: 10px;">
            <i class='bx bxs-moon' id='theme-icon'></i>
        </button>
        
        @if ($is_logged_in)
            <!-- NOTIFICATION DROPDOWN -->
            <div class="neo-notif-dropdown" style="position: relative; display: inline-block; margin-right: 15px;">
                <button id="notif-btn" title="Notifikasi" class="neo-navbar-btn brutal-hover" style="padding: 8px; font-size: 1.2rem; line-height: 1; background-color: #FFF; border: 3px solid #000; box-shadow: 3px 3px 0px #000; color: #000; position: relative; cursor: pointer;">
                    <i class='bx bxs-bell'></i>
                    <span id="notif-badge" style="display: none; position: absolute; top: -8px; right: -8px; background-color: #FF0000; color: #FFF; font-size: 0.7rem; font-weight: 900; padding: 2px 6px; border: 2px solid #000; border-radius: 50%; box-shadow: 1px 1px 0px #000;">0</span>
                </button>
                
                <div id="notif-menu" style="position: absolute; top: 120%; right: 0; width: 320px; background-color: var(--card); border: 4px solid #000; box-shadow: 6px 6px 0px #000; z-index: 1002; display: none; color: var(--foreground);">
                    <div style="background-color: var(--secondary); color: #000; padding: 10px 15px; font-weight: 900; border-bottom: 3px solid #000; display: flex; justify-content: space-between; align-items: center;">
                        <span>NOTIFIKASI</span>
                        <div style="display: flex; gap: 10px;">
                            <button onclick="markAllRead()" style="background: none; border: none; font-size: 0.75rem; font-weight: bold; text-decoration: underline; cursor: pointer; color: #000;">Tandai Dibaca</button>
                            <button onclick="deleteAllNotif()" style="background: none; border: none; font-size: 0.75rem; font-weight: bold; text-decoration: underline; cursor: pointer; color: #D32F2F;">Hapus Semua</button>
                        </div>
                    </div>
                    <div id="notif-list" style="max-height: 300px; overflow-y: auto; padding: 0;">
                        <div style="padding: 20px; text-align: center; color: var(--muted-foreground); font-weight: bold;">Memuat...</div>
                    </div>
                </div>
            </div>

            <div class="neo-user-dropdown" style="position: relative; display: inline-block;">
                <div class="neo-user-badge" style="cursor: pointer; display: flex; flex-direction: column; align-items: flex-start; line-height: 1.2; background-color: #FFF; padding: 8px 14px; border: 3px solid #000; box-shadow: 3px 3px 0px #000; transition: transform 0.1s, box-shadow 0.1s, background-color 0.2s;">
                    <strong style="font-size: 1em; color: #000;">{{ strtoupper($username) }}</strong>
                    <span style="font-size: 0.85em; color: #000; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                        {{ $user->role ?? 'Guest' }} <i class='bx bxs-chevron-down' style="font-size: 1.1em;"></i>
                    </span>
                </div>
                
                <div class="neo-dropdown-menu" style="position: absolute; top: 100%; right: 0; z-index: 1001; min-width: 100%; padding-top: 5px; display: none;">
                    <a href="{{ route('logout') }}" id="logout-btn" class="neo-logout-btn brutal-hover" style="background-color: var(--primary); color: #FFF; border: 3px solid #000; box-shadow: 4px 4px 0px #000; padding: 8px 16px; font-weight: bold; text-transform: uppercase; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; box-sizing: border-box;">
                        <i class='bx bx-log-out'></i> LOG OUT
                    </a>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="brutal-hover" style="padding: 8px 16px; font-weight: 900; background-color: #FFF; color: #000; border: 3px solid #000; box-shadow: 3px 3px 0px #000; text-decoration: none; text-transform: uppercase; margin-right: 10px; font-size: 0.9rem;">Masuk</a>
            <a href="{{ route('register') }}" class="brutal-hover" style="padding: 8px 16px; font-weight: 900; background-color: var(--primary); color: #FFF; border: 3px solid #000; box-shadow: 3px 3px 0px #000; text-decoration: none; text-transform: uppercase; font-size: 0.9rem;">Daftar</a>
        @endif
    </div>
</nav>

<script src="{{ asset('assets/js/neo-alert.js') }}"></script>
<script>
    function toggleDarkMode() {
        const isDark = document.documentElement.classList.toggle("dark-mode");
        localStorage.setItem("theme", isDark ? "dark" : "light");
        const icon = document.getElementById("theme-icon");
        if (icon) {
            icon.className = isDark ? "bx bxs-sun" : "bx bxs-moon";
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const isDark = document.documentElement.classList.contains("dark-mode");
        const icon = document.getElementById("theme-icon");
        if (icon && isDark) {
            icon.className = "bx bxs-sun";
        }

        // Hover Listener for User Dropdown
        const userDropdown = document.querySelector(".neo-user-dropdown");
        const dropdownMenu = document.querySelector(".neo-dropdown-menu");
        const userBadge = document.querySelector(".neo-user-badge");
        let dropdownTimeout;

        if (userDropdown && dropdownMenu && userBadge) {
            userDropdown.addEventListener("mouseenter", () => {
                clearTimeout(dropdownTimeout);
                dropdownMenu.style.display = "block";
                userBadge.style.transform = "translate(2px, 2px)";
                userBadge.style.boxShadow = "1px 1px 0px #000";
                userBadge.style.backgroundColor = "#FFD700";
            });
            
            userDropdown.addEventListener("mouseleave", () => {
                dropdownTimeout = setTimeout(() => {
                    dropdownMenu.style.display = "none";
                    userBadge.style.transform = "none";
                    userBadge.style.boxShadow = "3px 3px 0px #000";
                    userBadge.style.backgroundColor = "#FFF";
                }, 300); // Jeda 300ms sebelum hilang
            });
        }

        // ScrollSpy for Landing Page Navigation
        const sections = [
            { id: 'katalog', nav: document.getElementById('nav-katalog') },
            { id: 'faq', nav: document.getElementById('nav-faq') },
            { id: 'kontak', nav: document.getElementById('nav-kontak') }
        ];
        const navBeranda = document.getElementById('nav-beranda');

        // Check if we are on the landing page with those sections
        if (sections[0].nav && document.getElementById('katalog')) {
            window.addEventListener('scroll', () => {
                let current = '';
                const scrollY = window.pageYOffset;

                sections.forEach(sec => {
                    const el = document.getElementById(sec.id);
                    if (el) {
                        const sectionTop = el.offsetTop - 250; // Offset for sticky navbar
                        const sectionHeight = el.offsetHeight;
                        if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                            current = sec.id;
                        }
                    }
                });

                // Reset all active classes first
                if(navBeranda) navBeranda.classList.remove('active');
                sections.forEach(sec => {
                    if (sec.nav) sec.nav.classList.remove('active');
                });

                // Set active class based on scroll position
                if (current === '') {
                    if(navBeranda) navBeranda.classList.add('active');
                } else {
                    const activeSec = sections.find(s => s.id === current);
                    if (activeSec && activeSec.nav) {
                        activeSec.nav.classList.add('active');
                    }
                }
            });
        }

        // Notification Dropdown Logic
        const notifBtn = document.getElementById('notif-btn');
        const notifMenu = document.getElementById('notif-menu');
        const notifList = document.getElementById('notif-list');
        const notifBadge = document.getElementById('notif-badge');

        if (notifBtn && notifMenu) {
            // Toggle menu on click
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isVisible = notifMenu.style.display === 'block';
                notifMenu.style.display = isVisible ? 'none' : 'block';
                if (!isVisible) {
                    loadNotifikasi();
                }
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!notifMenu.contains(e.target) && e.target !== notifBtn) {
                    notifMenu.style.display = 'none';
                }
            });

            // Load notifications via AJAX
            window.loadNotifikasi = function() {
                fetch('{{ route("api.notifikasi") }}')
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            // Update Badge
                            if (res.unread_count > 0) {
                                notifBadge.style.display = 'inline-block';
                                notifBadge.innerText = res.unread_count > 9 ? '9+' : res.unread_count;
                            } else {
                                notifBadge.style.display = 'none';
                            }

                            // Populate List
                            notifList.innerHTML = '';
                            if (res.data.length === 0) {
                                notifList.innerHTML = `<div style="padding: 20px; text-align: center; color: var(--muted-foreground); font-weight: bold;">Belum ada notifikasi.</div>`;
                            } else {
                                res.data.forEach(n => {
                                    const bg = n.is_read == 0 ? 'background-color: rgba(255, 215, 0, 0.15);' : '';
                                    const item = document.createElement('div');
                                    item.style.cssText = `padding: 12px 15px; border-bottom: 2px solid var(--border); cursor: pointer; transition: background 0.2s; ${bg}`;
                                    item.onmouseover = () => item.style.backgroundColor = 'var(--muted)';
                                    item.onmouseout = () => item.style.backgroundColor = n.is_read == 0 ? 'rgba(255, 215, 0, 0.15)' : 'transparent';
                                    
                                    item.innerHTML = `
                                        <div style="font-weight: 900; font-size: 0.9rem; margin-bottom: 4px; color: var(--primary); display: flex; justify-content: space-between;">
                                            <span>${n.judul}</span>
                                            ${n.is_read == 0 ? '<span style="width: 8px; height: 8px; background-color: var(--primary); border-radius: 50%; display: inline-block;"></span>' : ''}
                                        </div>
                                        <div style="font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; line-height: 1.3;">${n.pesan}</div>
                                        <div style="font-size: 0.7rem; color: var(--muted-foreground); font-weight: bold;">${n.tgl_dibuat}</div>
                                    `;

                                    item.onclick = () => {
                                        fetch('{{ route("api.notifikasi") }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/x-www-form-urlencoded',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: `action=mark_read&id_notifikasi=${n.id_notifikasi}`
                                        }).then(() => {
                                            window.location.href = n.link;
                                        });
                                    };

                                    notifList.appendChild(item);
                                });
                            }
                        }
                    })
                    .catch(err => console.error("Gagal memuat notifikasi:", err));
            };

            window.markAllRead = function() {
                fetch('{{ route("api.notifikasi") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: `action=mark_read&id_notifikasi=all`
                }).then(() => {
                    loadNotifikasi();
                });
            };

            window.deleteAllNotif = function() {
                NeoConfirm('Hapus Notifikasi', 'Yakin ingin menghapus semua riwayat notifikasi?', () => {
                    fetch('{{ route("api.notifikasi") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: `action=delete&id_notifikasi=all`
                    }).then(() => {
                        loadNotifikasi();
                    });
                });
            };

            // Initial load & Poll every 10 seconds
            loadNotifikasi();
            setInterval(loadNotifikasi, 10000);
        }

        // Custom Logout Confirmation
        const logoutBtn = document.getElementById("logout-btn");
        if (logoutBtn) {
            logoutBtn.addEventListener("click", function(e) {
                e.preventDefault();
                const targetUrl = this.getAttribute("href");
                NeoConfirm("Konfirmasi Keluar", "Apakah Anda yakin ingin keluar dari sistem?", function() {
                    window.location.href = targetUrl;
                });
            });
        }
    });
</script>
