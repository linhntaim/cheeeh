<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Verification</title>
</head>
<body>
<p>
    Dear {{ $x_email_to_name }},<br>
    <br>
    Please click here to submit your verification:<br>
    <a href="{{ $url_verify }}">{{ $url_verify }}</a><br>
    <br>
    Best regards,<br>
    From {{ $x_email_from_name }}
</p>
</body>
</html>
