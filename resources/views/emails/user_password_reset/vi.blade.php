<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Thiết lập lại mật khẩu</title>
</head>
<body>
<p>
    Xin chào {{ $x_email_to_name }},<br>
    <br>
    Vui lòng bấm vào đường dẫn sau để thiết lập lại mật khẩu của bạn:<br>
    <a href="{{ $url_reset_password }}">{{ $url_reset_password }}</a><br>
    <br>
    Nếu bạn không phải là người đưa ra yêu cầu thiết lập lại mật khẩu, hãy bỏ qua nội dung thư này.<br>
    <br>
    Trân trọng,<br>
    {{ $x_email_from_name }}
</p>
</body>
</html>
