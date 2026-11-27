<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email — E-FORVM</title>
</head>
<body style="font-family: Arial, sans-serif; background: #F4F4F0; color: #000; padding: 24px;">
    <div style="max-width: 540px; margin: 0 auto; background: #FFF; border: 3px solid #000; padding: 28px;">
        <h2 style="margin-top: 0;">Verifikasi Alamat Email Anda</h2>
        <p>Halo <strong>{{ $nama }}</strong>,</p>
        <p>Terima kasih telah mendaftar di E-FORVM. Klik tombol di bawah ini untuk memverifikasi alamat email akun Anda:</p>
        <p style="margin: 24px 0;">
            <a href="{{ $verifyUrl }}" style="display: inline-block; background: #0066FF; color: #FFF; font-weight: bold; text-decoration: none; padding: 12px 20px; border: 2px solid #000;">Verifikasi Email</a>
        </p>
        <p style="font-size: 13px; color: #374151;">Tautan verifikasi ini berlaku selama 60 menit.</p>
        <p style="font-size: 12px; color: #6B7280; word-break: break-all;">Atau salin tautan ini ke browser Anda: {{ $verifyUrl }}</p>
    </div>
</body>
</html>
