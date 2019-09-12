<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác thực</title>
</head>
<body>
<p>
    Xìn chào {{ $x_email_to_name }}<br>
    <br>
    Hãy bấm vào đường dẫn sau để xác thực tài khoản của bạn:<br>
    <a href="{{ $url_verify }}">{{ $url_verify }}</a><br>
    <br>
    Trân trọng,<br>
    {{ $x_email_from_name }}
</p>
</body>
</html>
