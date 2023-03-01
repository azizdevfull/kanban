<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email Address</title>
</head>
<body>
    <p>Hi {{ $user->name }},</p>

    <p>Please click the button below to verify your email address:</p>

    <a href="{{ route('verification.verify', ['email' => $user->email]) }}">Verify Email</a>

    <p>If you did not request this verification, please ignore this email.</p>
</body>
</html>
