<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <style>
        /* خلفية الصفحة */
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* تصميم نموذج تسجيل الدخول */
        .login-container {
            background: #2a2e35;
            border-radius: 15px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 350px;
            text-align: center;
            color: #fff;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #ffffff;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 10px 30px 10px -12px;
            border: 1px solid #444;
            border-radius: 10px;
            outline: none;
            background-color: #333;
            color: #fff;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .login-container input[type="email"]:focus,
        .login-container input[type="password"]:focus {
            border-color: #4e8cff;
            box-shadow: 0 0 8px rgba(78, 140, 255, 0.3);
        }

        .login-container button {
            width: 100%;
            padding: 15px;
            background-color: #4e8cff;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .login-container button:hover {
            background-color: #3a6dd8;
            transform: scale(1.02);
        }

        .login-container .forgot-password {
            display: block;
            margin-top: 15px;
            color: #bbb;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .login-container .forgot-password:hover {
            color: #4e8cff;
        }
        .error-message {
        background-color: #ffdddd;
        color: #d8000c;
        border: 1px solid #d8000c;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 15px;
        font-size: 14px;
        text-align: right;
    }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    @if(session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <button type="submit">دخول</button>
    </form>
</div>

</body>
</html>
