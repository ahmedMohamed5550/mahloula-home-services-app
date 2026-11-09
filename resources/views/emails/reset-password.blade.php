<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .email-header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
            color: #333333;
        }
        .email-body p {
            line-height: 1.6;
            margin: 10px 0;
        }
        .token-box {
            background-color: #f9f9f9;
            border: 1px dashed #007bff;
            color: #007bff;
            font-weight: bold;
            padding: 10px;
            text-align: center;
            margin: 20px 0;
            border-radius: 4px;
        }
        .email-footer {
            background-color: #f4f4f4;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #666666;
        }
        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            Reset Your Password
        </div>
        <div class="email-body">
            <p>Hello,</p>
            <p>You are receiving this email because we received a password reset request for your account.</p>
            <p>Your password reset token is:</p>
            <div class="token-box" style="font-size: 14px;">
                {{ $token }}
            </div>
            <p>Please copy this token and use it to reset your password in the application.</p>
            <p>If you did not request a password reset, no further action is required.</p>
            <p>Thanks,</p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
