<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — Everlytic Employee Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ev-dark: #1c2b3a;
            --ev-green: #6cbf3e;
            --ev-bg: #f4f5f7;
            --ev-text: #1a2533;
            --ev-text-secondary: #8ea4ba;
            --ev-border: #e0e6ec;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--ev-dark);
            color: var(--ev-text);
            font-size: 14px;
            line-height: 1.55;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px 36px;
            text-align: center;
        }

        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .login-logo .logo-mark {
            width: 44px;
            height: 44px;
            background: var(--ev-green);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .login-logo h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--ev-dark);
            letter-spacing: -0.3px;
            margin: 0;
        }

        .login-logo h1 span {
            color: var(--ev-green);
        }

        .login-subtitle {
            font-size: 13px;
            color: var(--ev-text-secondary);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .btn-azure {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px 24px;
            background: var(--ev-dark);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
        }

        .btn-azure:hover {
            background: #263d52;
            color: #ffffff;
        }

        .btn-azure svg {
            flex-shrink: 0;
        }

        .login-footer {
            margin-top: 24px;
            font-size: 11px;
            color: var(--ev-text-secondary);
        }

        @media (max-width: 480px) {
            .login-card { padding: 28px 20px; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        @if (session('error'))
            <div class="alert alert-danger" style="border-radius:10px;margin-bottom:16px;font-size:13px;">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

</body>
</html>
