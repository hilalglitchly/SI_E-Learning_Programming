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
    <a href="{{ $is_logged_in ? route('dashboard') : route('home') }}" class="neo-navbar-brand" style="font-weight: 900; color: #000; display: flex; align-items: center; gap: 7px; text-decoration: none; line-height: 1.05;">
        <span class="neo-brand-code" style="font-size: 1.4rem; font-family: monospace; font-weight: 900;">&lt;/&gt;</span>
        <div style="display: flex; flex-direction: column;">
            <span class="neo-brand-title" style="font-size: 1.2rem; letter-spacing: 0.04em;">E-FORVM</span>
            <span class="neo-brand-sub" style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.08em;">MOBILE LEARNING</span>
        </div>
    </a>
    
    <ul id="neo-navbar-menu" class="neo-navbar-menu">
        <li>
            <a id="nav-beranda" href="{{ $is_logged_in ? route('dashboard') : route('home') }}" style="color: #000;" class="{{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                <i class='bx bxs-home' aria-hidden="true"></i> BERANDA
            </a>
        </li>
        
        @if ($role === 'murid')
            <li><a href="{{ route('tugas.index') }}" style="color: #000;" class="{{ request()->routeIs('tugas.index') ? 'active' : '' }}"><i class='bx bx-task' aria-hidden="true"></i> TUGAS</a></li>
            <li><a href="{{ route('enroll.index') }}" style="color: #000;" class="{{ request()->routeIs('enroll.*') ? 'active' : '' }}"><i class='bx bx-search' aria-hidden="true"></i> CARI KELAS</a></li>
            <li><a href="{{ route('langganan.index') }}" style="color: #000;" class="{{ request()->routeIs('langganan.*') ? 'active' : '' }}"><i class='bx bxs-crown' aria-hidden="true"></i> PRO</a></li>
        @elseif ($role === 'pengajar')
            <li><a href="{{ route('tugas.index') }}" style="color: #000;" class="{{ request()->routeIs('tugas.index') || request()->routeIs('penilaian.*') ? 'active' : '' }}"><i class='bx bx-task' aria-hidden="true"></i> NILAI TUGAS</a></li>
        @elseif ($role === 'admin')
            <li><a href="{{ route('admin.users.create') }}" style="color: #000;" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class='bx bxs-user-plus' aria-hidden="true"></i> TAMBAH USER</a></li>
            <li><a href="{{ route('admin.langganan.index') }}" style="color: #000;" class="{{ request()->routeIs('admin.langganan.*') ? 'active' : '' }}"><i class='bx bx-credit-card' aria-hidden="true"></i> PEMBAYARAN</a></li>
            <li><a href="{{ route('laporan.index') }}" style="color: #000;" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}"><i class='bx bxs-report' aria-hidden="true"></i> LAPORAN</a></li>
        @elseif ($role === 'pimpinan')
            <li><a href="{{ route('laporan.index') }}" style="color: #000;" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}"><i class='bx bxs-report' aria-hidden="true"></i> LAPORAN</a></li>
        @endif
        
        <li><a href="{{ route('forum.index') }}" style="color: #000;" class="{{ request()->routeIs('forum.*') ? 'active' : '' }}"><i class='bx bx-message-rounded-dots' aria-hidden="true"></i> FORUM</a></li>

        @if ($is_logged_in)
            <li><a href="{{ route('profil.index') }}" style="color: #000;" class="{{ request()->routeIs('profil.*') ? 'active' : '' }}"><i class='bx bxs-user-circle' aria-hidden="true"></i> AKUN</a></li>
        @else
            <li><a id="nav-katalog" href="{{ route('home') }}#katalog" style="color: #000;"><i class='bx bx-search' aria-hidden="true"></i> KATALOG</a></li>
            <li><a id="nav-paket" href="{{ route('home') }}#paket" style="color: #000;"><i class='bx bxs-crown' aria-hidden="true"></i> HARGA</a></li>
            <li><a id="nav-faq" href="{{ route('home') }}#faq" style="color: #000;"><i class='bx bx-question-mark' aria-hidden="true"></i> FAQ</a></li>
            <li><a id="nav-kontak" href="{{ route('home') }}#kontak" style="color: #000;"><i class='bx bxs-contact' aria-hidden="true"></i> KONTAK</a></li>
            <li class="neo-mobile-drawer-auth">
                <a href="{{ route('login') }}" class="neo-drawer-btn-login"><i class='bx bx-log-in' aria-hidden="true"></i> MASUK</a>
                <a href="{{ route('register') }}" class="neo-drawer-btn-register"><i class='bx bx-user-plus' aria-hidden="true"></i> DAFTAR GRATIS</a>
            </li>
        @endif
    </ul>

    <div class="neo-navbar-user">
        <button type="button" id="pwa-install-btn" title="Install Aplikasi E-FORVM" aria-label="Install Aplikasi E-FORVM" class="neo-navbar-btn js-pwa-install-btn brutal-hover" style="display: none; height: 42px; padding: 0 10px; align-items: center; justify-content: center; gap: 5px; font-size: 0.78rem; font-weight: 900; text-transform: uppercase; background-color: #16A34A; border: 3px solid #000; box-shadow: 3px 3px 0px #000; color: #FFF; cursor: pointer; white-space: nowrap;">
            <i class='bx bx-download' style="font-size: 1.1rem;" aria-hidden="true"></i>
            <span class="neo-btn-label">INSTALL</span>
        </button>

        <button type="button" id="nav-mobile-toggle" aria-label="Buka menu navigasi" class="neo-nav-toggle {{ $is_logged_in ? 'neo-nav-toggle--hide-mobile' : '' }} brutal-hover" aria-expanded="false" aria-controls="neo-navbar-menu">
            <i class='bx bx-menu' id="nav-mobile-icon" aria-hidden="true"></i>
            <span class="neo-btn-label">MENU</span>
        </button>

        <button type="button" title="Ganti Tema" aria-label="Ganti tema gelap atau terang" id="theme-toggle" onclick="toggleDarkMode()" class="neo-navbar-btn brutal-hover" style="width: 42px; height: 42px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1.15rem; line-height: 1; background-color: #FFF; border: 3px solid #000; box-shadow: 3px 3px 0px #000; color: #000; cursor: pointer;">
            <i class='bx bxs-moon' id='theme-icon' aria-hidden="true"></i>
        </button>
        
        @if ($is_logged_in)
            <!-- NOTIFICATION DROPDOWN -->
            <div class="neo-notif-dropdown" style="position: relative; display: inline-flex;">
                <button type="button" id="notif-btn" title="Notifikasi" aria-label="Buka notifikasi" class="neo-navbar-btn brutal-hover" style="width: 42px; height: 42px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1.15rem; line-height: 1; background-color: #FFF; border: 3px solid #000; box-shadow: 3px 3px 0px #000; color: #000; position: relative; cursor: pointer;">
                    <i class='bx bxs-bell' aria-hidden="true"></i>
                    <span id="notif-badge" style="display: none; position: absolute; top: -8px; right: -8px; background-color: #DC2626; color: #FFF; font-size: 0.7rem; font-weight: 900; padding: 2px 6px; border: 2px solid #000; border-radius: 50%; box-shadow: 1px 1px 0px #000;">0</span>
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

            <div class="neo-user-dropdown" style="position: relative; display: inline-flex;">
                <div class="neo-user-badge" role="button" tabindex="0" aria-expanded="false" aria-label="Menu akun pengguna" style="cursor: pointer; height: 42px; box-sizing: border-box; display: flex; flex-direction: column; align-items: flex-start; justify-content: center; line-height: 1.15; background-color: #FFF; padding: 0 12px; border: 3px solid #000; box-shadow: 3px 3px 0px #000; transition: transform 0.1s, box-shadow 0.1s, background-color 0.2s;">
                    <strong class="neo-user-name" style="font-size: 0.85rem; color: #000; white-space: nowrap;">{{ strtoupper($username) }}</strong>
                    <span class="neo-user-role" style="font-size: 0.75rem; color: #000; font-weight: 600; display: flex; align-items: center; gap: 3px; white-space: nowrap;">
                        {{ $user->role ?? 'Guest' }} <i class='bx bxs-chevron-down' style="font-size: 1.05em;"></i>
                    </span>
                </div>
                
                <div class="neo-dropdown-menu" style="position: absolute; top: 100%; right: 0; z-index: 1001; min-width: 150px; padding-top: 5px; display: none;">
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <a href="{{ route('profil.index') }}" class="brutal-hover" style="background-color: #FFF; color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px #000; padding: 8px 14px; font-weight: 900; text-transform: uppercase; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; box-sizing: border-box; font-size: 0.84rem;">
                            <i class='bx bxs-user-circle' aria-hidden="true"></i> PROFIL SAYA
                        </a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" id="logout-btn" class="neo-logout-btn brutal-hover" style="background-color: var(--primary); color: #FFF; border: 3px solid #000; box-shadow: 4px 4px 0px #000; padding: 8px 16px; font-weight: bold; text-transform: uppercase; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; box-sizing: border-box; cursor: pointer;">
                                <i class='bx bx-log-out' aria-hidden="true"></i> LOG OUT
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="neo-nav-login-btn brutal-hover" style="height: 42px; box-sizing: border-box; display: inline-flex; align-items: center; padding: 0 14px; font-weight: 900; background-color: #FFF; color: #000; border: 3px solid #000; box-shadow: 3px 3px 0px #000; text-decoration: none; text-transform: uppercase; font-size: 0.85rem; white-space: nowrap;">Masuk</a>
            <a href="{{ route('register') }}" class="neo-nav-register-desktop brutal-hover" style="height: 42px; box-sizing: border-box; display: inline-flex; align-items: center; padding: 0 14px; font-weight: 900; background-color: #DC2626; color: #FFF; border: 3px solid #000; box-shadow: 3px 3px 0px #000; text-decoration: none; text-transform: uppercase; font-size: 0.85rem; white-space: nowrap;">Daftar</a>
        @endif
    </div>
</nav>

<script src="{{ asset('assets/js/neo-alert.js') }}"></script>
<script>
    function syncThemeControls(isDark) {
        const icon = document.getElementById("theme-icon");
        if (icon) {
            icon.className = isDark ? "bx bxs-sun" : "bx bxs-moon";
        }
        const profilIcon = document.getElementById("profil-theme-icon");
        if (profilIcon) {
            profilIcon.className = isDark ? "bx bxs-sun" : "bx bxs-moon";
        }
        const profilLabel = document.getElementById("profil-theme-label");
        if (profilLabel) {
            profilLabel.textContent = isDark ? "Mode Terang" : "Mode Gelap";
        }
    }

    function toggleDarkMode() {
        const isDark = document.documentElement.classList.toggle("dark-mode");
        localStorage.setItem("theme", isDark ? "dark" : "light");
        syncThemeControls(isDark);
    }

    document.addEventListener("DOMContentLoaded", () => {
        const isDark = document.documentElement.classList.contains("dark-mode");
        syncThemeControls(isDark);

        // Hover + Tap Listener for User Dropdown (mobile touch-friendly)
        const userDropdown = document.querySelector(".neo-user-dropdown");
        const dropdownMenu = document.querySelector(".neo-dropdown-menu");
        const userBadge = document.querySelector(".neo-user-badge");
        let dropdownTimeout;

        if (userDropdown && dropdownMenu && userBadge) {
            const setUserMenu = (open) => {
                dropdownMenu.style.display = open ? "block" : "none";
                userBadge.setAttribute("aria-expanded", open ? "true" : "false");
                userBadge.style.transform = open ? "translate(2px, 2px)" : "none";
                userBadge.style.boxShadow = open ? "1px 1px 0px #000" : "3px 3px 0px #000";
                userBadge.style.backgroundColor = open ? "#FFD700" : "#FFF";
            };

            userBadge.addEventListener("click", (e) => {
                e.stopPropagation();
                clearTimeout(dropdownTimeout);
                setUserMenu(dropdownMenu.style.display !== "block");
            });

            userBadge.addEventListener("keydown", (e) => {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    setUserMenu(dropdownMenu.style.display !== "block");
                }
            });

            if (window.matchMedia && window.matchMedia("(hover: hover)").matches) {
                userDropdown.addEventListener("mouseenter", () => {
                    clearTimeout(dropdownTimeout);
                    setUserMenu(true);
                });

                userDropdown.addEventListener("mouseleave", () => {
                    dropdownTimeout = setTimeout(() => setUserMenu(false), 300);
                });
            }

            document.addEventListener("click", (e) => {
                if (dropdownMenu.style.display === "block" && !userDropdown.contains(e.target)) {
                    setUserMenu(false);
                }
            });
        }

        // Mobile Collapsible Navigation Menu
        const mobileToggle = document.getElementById("nav-mobile-toggle");
        const mobileMenu = document.getElementById("neo-navbar-menu");
        const mobileIcon = document.getElementById("nav-mobile-icon");
        if (mobileToggle && mobileMenu) {
            const setMenuOpen = (open) => {
                mobileMenu.classList.toggle("is-open", open);
                mobileToggle.setAttribute("aria-expanded", open ? "true" : "false");
                if (mobileIcon) {
                    mobileIcon.className = open ? "bx bx-x" : "bx bx-menu";
                }
            };

            mobileToggle.addEventListener("click", (e) => {
                e.stopPropagation();
                setMenuOpen(!mobileMenu.classList.contains("is-open"));
            });

            mobileMenu.querySelectorAll("a").forEach((link) => {
                link.addEventListener("click", () => setMenuOpen(false));
            });

            document.addEventListener("click", (e) => {
                if (mobileMenu.classList.contains("is-open") && !mobileMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
                    setMenuOpen(false);
                }
            });

            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && mobileMenu.classList.contains("is-open")) {
                    setMenuOpen(false);
                    mobileToggle.focus();
                }
            });
        }

        // ScrollSpy for Landing Page Navigation
        const sections = [
            { id: 'katalog', nav: document.getElementById('nav-katalog') },
            { id: 'paket', nav: document.getElementById('nav-paket') },
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
                            const badgeText = res.unread_count > 9 ? '9+' : String(res.unread_count);
                            // Update Desktop & Mobile Bottom-Nav Badges
                            [notifBadge, document.getElementById('mobile-nav-notif-badge')].forEach((b) => {
                                if (!b) return;
                                if (res.unread_count > 0) {
                                    b.style.display = 'inline-flex';
                                    b.innerText = badgeText;
                                } else {
                                    b.style.display = 'none';
                                }
                            });

                            const targets = [notifList, document.getElementById('profil-notif-list')].filter(Boolean);
                            targets.forEach((container) => {
                                container.innerHTML = '';
                                if (res.data.length === 0) {
                                    container.innerHTML = `<div style="padding: 20px; text-align: center; color: var(--muted-foreground); font-weight: bold;">Belum ada notifikasi.</div>`;
                                    return;
                                }
                                res.data.forEach(n => {
                                    const bg = n.is_read == 0 ? 'background-color: rgba(255, 215, 0, 0.15);' : '';
                                    const item = document.createElement('div');
                                    item.style.cssText = `padding: 12px 15px; border-bottom: 2px solid var(--border); cursor: pointer; transition: background 0.2s; ${bg}`;
                                    item.onmouseover = () => item.style.backgroundColor = 'var(--muted)';
                                    item.onmouseout = () => item.style.backgroundColor = n.is_read == 0 ? 'rgba(255, 215, 0, 0.15)' : 'transparent';

                                    const header = document.createElement('div');
                                    header.style.cssText = 'font-weight: 900; font-size: 0.9rem; margin-bottom: 4px; color: var(--primary); display: flex; justify-content: space-between; align-items: center; gap: 8px;';
                                    const titleSpan = document.createElement('span');
                                    titleSpan.textContent = n.judul ?? '';
                                    header.appendChild(titleSpan);
                                    if (n.is_read == 0) {
                                        const dot = document.createElement('span');
                                        dot.style.cssText = 'width: 8px; height: 8px; flex-shrink: 0; background-color: var(--primary); border-radius: 50%; display: inline-block;';
                                        header.appendChild(dot);
                                    }

                                    const body = document.createElement('div');
                                    body.style.cssText = 'font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; line-height: 1.35;';
                                    body.textContent = n.pesan ?? '';

                                    const timeEl = document.createElement('div');
                                    timeEl.style.cssText = 'font-size: 0.72rem; color: var(--muted-foreground); font-weight: bold;';
                                    timeEl.textContent = n.tgl_dibuat ?? '';

                                    item.append(header, body, timeEl);

                                    item.onclick = () => {
                                        fetch('{{ route("api.notifikasi") }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/x-www-form-urlencoded',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: `action=mark_read&id_notifikasi=${encodeURIComponent(n.id_notifikasi)}`
                                        }).then(() => {
                                            if (typeof n.link === 'string' && n.link) {
                                                try {
                                                    const parsed = new URL(n.link, window.location.origin);
                                                    if (parsed.origin === window.location.origin && (parsed.protocol === 'http:' || parsed.protocol === 'https:')) {
                                                        window.location.href = parsed.href;
                                                    }
                                                } catch (e) {}
                                            }
                                        });
                                    };

                                    container.appendChild(item);
                                });
                            });
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

            // Initial load & Poll every 60 seconds only while tab is visible
            loadNotifikasi();
            setInterval(() => {
                if (!document.hidden) {
                    loadNotifikasi();
                }
            }, 60000);
        }

        // Custom Logout Confirmation
        const logoutForm = document.getElementById("logout-form");
        if (logoutForm) {
            logoutForm.addEventListener("submit", function(e) {
                e.preventDefault();
                NeoConfirm("Konfirmasi Keluar", "Apakah Anda yakin ingin keluar dari sistem?", () => {
                    logoutForm.submit();
                });
            });
        }
    });
</script>
