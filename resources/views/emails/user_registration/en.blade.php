<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Registration</title>
</head>
<body>
<p>
    Dear {{ $x_email_to_name }},<br>
    <br>
    Thank you for registering at <a href="{{ $app_url }}">{{ $app_name }}</a>.<br>
    <br>
    Here is your account:<br>
    - Email address: {{ $x_email_to }}<br>
    - Username: {{ $user_name }}<br>
    - Password: {{ $user_password }}<br>
    <br>
    Please keep the information above privately.<br>
    <br>
    Best regards,<br>
    From {{ $x_email_from_name }}
</p>
</body>
</html>
