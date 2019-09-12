<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Đăng ký</title>
</head>
<body>
<p>
    Xin chào {{ $x_email_to_name }},<br>
    <br>
    Cảm ơn bạn đã đăng ký làm thành viên tại <a href="{{ $app_url }}">{{ $app_name }}</a>.<br>
    <br>
    Dưới đây là thông tin tài khoản của bạn:<br>
    - Địa chỉ thư điện tử: {{ $x_email_to }}<br>
    - Tên người dùng: {{ $user_name }}<br>
    - Mật khẩu: {{ $user_password }}<br>
    <br>
    Hãy lưu giữ chúng một cách an toàn.<br>
    <br>
    Trân trọng,<br>
    {{ $x_email_from_name }}
</p>
</body>
</html>
