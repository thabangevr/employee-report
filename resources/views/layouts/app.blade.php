<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employee Report') — Everlytic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --ev-dark: #1c2b3a;
            --ev-green: #6cbf3e;
            --ev-bg: #f4f5f7;
            --ev-white: #ffffff;
            --ev-text: #1a2533;
            --ev-text-secondary: #8ea4ba;
            --ev-text-body: #2a3b4d;
            --ev-text-heading: #3a5068;
            --ev-border: #e0e6ec;
            --ev-border-light: #f0f3f6;
            --ev-green-value: #2e7d18;
            --ev-green-bg: #eaf6e2;
            --ev-amber: #e8a020;
            --ev-amber-value: #92620a;
            --ev-amber-bg: #fef3dd;
            --ev-red: #c83232;
            --ev-red-bg: #fde8e8;
            --ev-highlight-bg: #f0fae8;
            --ev-highlight-border: #b8e09c;
            --ev-tag-bg: #eef4fb;
            --ev-tag-text: #3a6285;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--ev-bg);
            color: var(--ev-text);
            font-size: 14px;
            line-height: 1.55;
            margin: 0;
            padding: 0;
        }

        .site-header {
            background: var(--ev-dark);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .logo-wordmark {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-mark {
            width: 36px;
            height: 36px;
            background: var(--ev-green);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .site-header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
            margin: 0;
        }

        .site-header h1 span {
            color: var(--ev-green);
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--ev-text-secondary);
            font-size: 13px;
        }

        .header-user strong {
            color: #c8d8e8;
        }

        .tab-bar {
            background: var(--ev-dark);
            padding: 0 32px;
            display: flex;
            gap: 0;
            border-bottom: 2px solid #243547;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 14px 24px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ev-text-secondary);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: color 0.15s, border-color 0.15s;
            letter-spacing: 0.01em;
            text-decoration: none;
        }

        .tab-btn:hover { color: #c8d8e8; }
        .tab-btn.active { color: var(--ev-green); border-bottom-color: var(--ev-green); }

        .main-content {
            padding: 28px 32px 48px;
            max-width: 1280px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--ev-green);
            margin-bottom: 14px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--ev-border);
        }

        .ev-card {
            background: var(--ev-white);
            border: 1px solid var(--ev-border);
            border-radius: 10px;
            padding: 18px 20px;
        }

        .pill {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        .pill-green { background: var(--ev-green-bg); color: var(--ev-green-value); }
        .pill-amber { background: var(--ev-amber-bg); color: var(--ev-amber-value); }
        .pill-blocker { background: var(--ev-red-bg); color: var(--ev-red); }

        .btn-ev-primary {
            background: var(--ev-green);
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-ev-primary:hover {
            background: #5eaa34;
            color: #fff;
        }

        .btn-ev-outline {
            background: transparent;
            color: var(--ev-text-secondary);
            border: 1px solid var(--ev-border);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }

        .btn-ev-outline:hover {
            border-color: var(--ev-green);
            color: var(--ev-green);
        }

        @media (max-width: 700px) {
            .main-content { padding: 16px; }
            .site-header { padding: 14px 16px; }
            .tab-bar { padding: 0 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <header class="site-header">
        <div class="logo-wordmark">
            <div class="logo-mark">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 11 L11 4 L18 11 L11 18 Z" fill="white" opacity="0.9"/>
                    <circle cx="11" cy="11" r="3" fill="#1c2b3a"/>
                </svg>
            </div>
            <h1>Everlytic <span>Employee Report</span></h1>
        </div>
        @auth
            <div class="header-user">
                <strong>{{ Auth::user()->name }}</strong>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-ev-outline">Logout</button>
                </form>
            </div>
        @endauth
    </header>

    @hasSection('tabs')
        <nav class="tab-bar">
            @yield('tabs')
        </nav>
    @endif

    <div class="main-content">
        @if (session('error'))
            <div class="alert alert-danger" style="border-radius:10px;margin-bottom:20px;">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" style="border-radius:10px;margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
