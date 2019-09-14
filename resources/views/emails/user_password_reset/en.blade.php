<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset password</title>
</head>
<body>
<p>
    Dear {{ $x_email_to_name }},<br>
    <br>
    Please click here to reset your password:<br>
    <a href="{{ $url_reset_password }}">{{ $url_reset_password }}</a><br>
    <br>
    If you do not request to reset the password, please ignore this content.<br>
    <br>
    Best regards,<br>
    From {{ $x_email_from_name }}
</p>
</body>
</html>
