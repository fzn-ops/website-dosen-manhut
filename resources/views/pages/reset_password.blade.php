<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px;">
    <div style="max-w-md; margin: 0 auto; background: white; padding: 30px; border-radius: 8px;">
        <h2 style="color: #183669;">Halo!</h2>
        <p>Anda menerima email ini karena kami menerima permintaan pengaturan ulang kata sandi untuk akun Anda.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetLink }}" style="background-color: #183669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Reset Password
            </a>
        </div>

        <p>Tautan reset password ini akan kedaluwarsa dalam 60 menit.</p>
        <p>Jika Anda tidak meminta reset password, abaikan saja email ini.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin-top: 30px;">
        <p style="font-size: 12px; color: #999;">Jika tombol di atas tidak berfungsi, copy-paste URL berikut ke browser Anda:<br>
        <a href="{{ $resetLink }}">{{ $resetLink }}</a></p>
    </div>
</body>
</html>