<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
    <style>
        body { font-family: sans-serif; background:#f8f7f4; color:#2a1b18; }
        .container { max-width:600px;margin:auto;background:#fff;padding:2rem;border-radius:2rem; }
        a.button { display:inline-block;padding:1rem 2rem;background:#2a1b18;color:#fff;text-decoration:none;border-radius:1rem; }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Your NEXA Password</h2>
    <p>Hello,</p>
    <p>You requested a password reset. Click the button below to reset your password:</p>
    <p><a class="button" href="{{ $url }}">Reset Password</a></p>
    <p>If you did not request this, ignore this email.</p>
    <p>— NEXA.GLOBAL</p>
</div>
</body>
</html>
