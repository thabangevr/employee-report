@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="login-card">
        <div class="login-logo">
            <div class="logo-mark">
                <svg width="26" height="26" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 11 L11 4 L18 11 L11 18 Z" fill="white" opacity="0.9"/>
                    <circle cx="11" cy="11" r="3" fill="#1c2b3a"/>
                </svg>
            </div>
            <h1>Everlytic <span>Report</span></h1>
        </div>

        <p class="login-subtitle">
            Sign in with your company account to access the Employee Reporting portal.
        </p>

        <a href="{{ route('auth.azure.redirect') }}" class="btn-azure">
            <svg width="20" height="20" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="1" y="1" width="9" height="9" fill="#F25022"/>
                <rect x="11" y="1" width="9" height="9" fill="#7FBA00"/>
                <rect x="1" y="11" width="9" height="9" fill="#00A4EF"/>
                <rect x="11" y="11" width="9" height="9" fill="#FFB900"/>
            </svg>
            Sign in with Microsoft
        </a>

        <div class="login-footer">
            Secured with Azure Active Directory SSO
        </div>

        @if (app()->environment('local'))
            <div style="display:flex;gap:10px;margin-top:16px;">
                <a href="{{ route('dev.login') }}" style="flex:1;padding:10px 24px;background:var(--ev-green);color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;text-align:center;">
                    Dev Login (Manager)
                </a>
                <a href="{{ route('dev.login.ceo') }}" style="flex:1;padding:10px 24px;background:var(--ev-dark);color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;text-align:center;">
                    Dev Login (CEO)
                </a>
            </div>
        @endif
    </div>
@endsection
