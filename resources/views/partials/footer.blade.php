    <footer style="background-color: #212126; color: #FFF; padding: 60px 20px 30px 20px; border-top: 6px solid #000;">
        <div style="max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px;">
            
            <!-- Kolom 1: Brand & Deskripsi -->
            <div>
                <div style="font-size: 2rem; font-weight: 900; color: var(--secondary); margin-bottom: 15px; display: flex; align-items: center; gap: 8px; line-height: 1.1;">
                    <span style="font-family: monospace; font-size: 2.2rem;">&lt;/&gt;</span>
                    <div style="display: flex; flex-direction: column;">
                        <span>E-LEARNING</span>
                        <span>PROGRAMMING</span>
                    </div>
                </div>
                <p style="font-size: 1rem; color: #D1D5DB; line-height: 1.6; font-weight: 500; margin-bottom: 20px;">
                    Platform belajar koding interaktif dengan pendekatan praktis. Bangun portofolio nyata dan raih karir impianmu di industri teknologi.
                </p>
                <div style="display: flex; gap: 15px;">
                    <a href="https://www.instagram.com/hilal_s28" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px; background-color: #FFF; color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px var(--primary); font-size: 1.5rem; text-decoration: none; transition: transform 0.1s;"><i class='bx bxl-instagram'></i></a>
                    <a href="https://github.com/hilalglitchly" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px; background-color: #FFF; color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px var(--accent); font-size: 1.5rem; text-decoration: none; transition: transform 0.1s;"><i class='bx bxl-github'></i></a>
                    <a href="https://youtube.com/@hilal_k?si=R1T3G98B7C280_1M" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px; background-color: #FFF; color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px #10B981; font-size: 1.5rem; text-decoration: none; transition: transform 0.1s;"><i class='bx bxl-youtube'></i></a>
                </div>
            </div>

            <!-- Kolom 2: Navigasi Cepat -->
            <div>
                <h4 style="font-size: 1.3rem; font-weight: 900; text-transform: uppercase; margin-bottom: 20px; color: #FFF; border-bottom: 3px solid var(--primary); display: inline-block; padding-bottom: 5px;">Navigasi</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; font-weight: 700;">
                    <li><a href="{{ route('home') }}" style="color: #D1D5DB; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='#D1D5DB'">Beranda</a></li>
                    <li><a href="#katalog" style="color: #D1D5DB; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='#D1D5DB'">Katalog Kelas</a></li>
                    <li><a href="#faq" style="color: #D1D5DB; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='#D1D5DB'">FAQ</a></li>
                    <li><a href="{{ route('login') }}" style="color: #D1D5DB; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='#D1D5DB'">Masuk / Daftar</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Kategori Populer -->
            <div>
                <h4 style="font-size: 1.3rem; font-weight: 900; text-transform: uppercase; margin-bottom: 20px; color: #FFF; border-bottom: 3px solid var(--accent); display: inline-block; padding-bottom: 5px;">Alur Belajar</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; font-weight: 700;">
                    <li style="color: #D1D5DB;">Frontend Web Developer</li>
                    <li style="color: #D1D5DB;">Backend API Engineer</li>
                    <li style="color: #D1D5DB;">Mobile App (Kotlin)</li>
                    <li style="color: #D1D5DB;">Data Science & AI</li>
                    <li style="color: #D1D5DB;">Cybersecurity Dasar</li>
                </ul>
            </div>

            <!-- Kolom 4: Hubungi Kami -->
            <div>
                <h4 style="font-size: 1.3rem; font-weight: 900; text-transform: uppercase; margin-bottom: 20px; color: #FFF; border-bottom: 3px solid #10B981; display: inline-block; padding-bottom: 5px;">Kantor Pusat</h4>
                <p style="color: #D1D5DB; font-weight: 600; line-height: 1.6; margin-bottom: 10px;">
                    <i class='bx bxs-map' style="color: var(--primary);"></i> Gedung Inovasi Digital Lt. 4<br>Jl. Teknologi Raya No. 404, Jakarta Selatan
                </p>
                <p style="color: #D1D5DB; font-weight: 600; margin-bottom: 10px;">
                    <i class='bx bxs-envelope' style="color: var(--secondary);"></i> support@elearning.local
                </p>
                <p style="color: #D1D5DB; font-weight: 600;">
                    <i class='bx bxs-phone' style="color: #10B981;"></i> +62 812-3456-7890
                </p>
            </div>

        </div>

        <!-- Copyright Bottom -->
        <div style="max-width: 1400px; margin: 0 auto; padding-top: 25px; border-top: 2px solid #374151; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 15px; font-weight: 700; color: #9CA3AF; font-size: 0.9rem;">
            <div>&copy; {{ date('Y') }} Sistem Informasi E-Learning Programming. Hak Cipta Dilindungi.</div>
            <div style="display: flex; gap: 20px;">
                <a href="#" style="color: #9CA3AF; text-decoration: none;">Syarat & Ketentuan</a>
                <a href="#" style="color: #9CA3AF; text-decoration: none;">Kebijakan Privasi</a>
            </div>
        </div>
    </footer>

