<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password — E-Learning Programming</title>
</head>
<body style="font-family: Arial, sans-serif; background: #F4F4F0; color: #000; padding: 24px;">
    <div style="max-width: 540px; margin: 0 auto; background: #FFF; border: 3px solid #000; padding: 28px;">
        <h2 style="margin-top: 0;">Permintaan Reset Password</h2>
        <p>Halo <strong>{{ $nama }}</strong>,</p>
        <p>Kami menerima permintaan untuk mengatur ulang password akun E-Learning Programming Anda. Klik tombol di bawah ini untuk membuat password baru:</p>
        <p style="margin: 24px 0;">
            <a href="{{ $resetUrl }}" style="display: inline-block; background: #FFD700; color: #000; font-weight: bold; text-decoration: none; padding: 12px 20px; border: 2px solid #000;">Reset Password Sekarang</a>
        </p>
        <p style="font-size: 13px; color: #374151;">Tautan ini berlaku selama 60 menit. Jika Anda tidak meminta reset password, abaikan email ini.</p>
        <p style="font-size: 12px; color: #6B7280; word-break: break-all;">Atau salin tautan ini ke browser Anda: {{ $resetUrl }}</p>
    </div>
</body>
</html>
