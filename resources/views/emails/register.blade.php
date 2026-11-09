<!DOCTYPE html>
<html>
<head>
    <title>Welcome to {{ config('app.name') }}</title>
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
        .cta-box {
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
            Welcome to {{ config('app.name') }}
        </div>
        <div class="email-body">
            <p>Hello {{ $name }},</p>

            @if($userType === 'employee' || $userType === 'company')
                <p>We are excited to have you join our platform as a {{ ucfirst($userType) }}!</p>
                <p>To get started, please complete your data verification process to ensure your account is fully activated.</p>
                <div class="cta-box">
                    Log in to your account and submit the required information to get verified.
                </div>
            @else
                <p>Welcome to our platform! We are thrilled to have you onboard.</p>
                <p>Feel free to explore and make the most out of your experience with us.</p>
            @endif

            <p>If you have any questions or need assistance, our support team is here to help.</p>
            <p>Thanks,</p>
            <p>{{ config('app.name') }} Team</p>
        </div>
        <div class="email-footer">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
