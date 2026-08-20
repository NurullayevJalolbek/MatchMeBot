@extends('admin.layouts.auth')

@section('title', 'Duralux || Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card border-0 shadow-lg mt-5 overflow-hidden auth-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <a href="{{ route('login') }}" class="d-block">
                        <img src="{{ asset('assets/images/logo-full.png') }}" alt="Logo" class="img-fluid" style="max-height: 40px;">
                    </a>
                    <p class="text-white-50 small mt-2">Administrator paneliga kirish</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3 border-0 mb-4 rounded-3 text-start" style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; font-size: 0.88rem;">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-white-50">Foydalanuvchi nomi (Username)</label>
                        <div class="input-group input-group-lg custom-input-group rounded-3 overflow-hidden">
                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                <i class="feather-user"></i>
                            </span>
                            <input type="text" name="username" value="{{ old('username') }}" class="form-control bg-transparent border-0 text-white shadow-none ps-2" placeholder="admin" required autofocus autocomplete="username">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-white-50 mb-0">Parol</label>
                        </div>
                        <div class="input-group input-group-lg custom-input-group rounded-3 overflow-hidden">
                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                <i class="feather-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control bg-transparent border-0 text-white shadow-none ps-2" placeholder="••••••••" required autocomplete="current-password">
                        </div>
                    </div>

                    <div class="mb-4 form-check text-start">
                        <input type="checkbox" name="remember" class="form-check-input bg-dark border-secondary" id="rememberMe">
                        <label class="form-check-label text-white-50 small" for="rememberMe">Eslab qolish</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 mb-2 ripple">
                        Kirish
                    </button>
                </form>
            </div>

            <div class="card-footer bg-dark-50 border-0 p-4 text-center">
                <p class="text-white-50 mb-0 small">Created by Nurullayev</p>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted small">&copy; {{ date('Y') }} Duralux Admin Panel. All rights reserved.</p>
        </div>
    </div>
</div>

<style>
    body {
        background: radial-gradient(circle at top right, #1e293b, #0f172a);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .auth-minimal-wrapper {
        width: 100%;
    }

    .auth-card {
        background: rgba(17, 24, 39, 0.9) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6) !important;
    }

    .custom-input-group {
        background-color: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        transition: all 0.2s ease;
    }

    .custom-input-group:focus-within {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
        background-color: #1a2234 !important;
    }

    .custom-input-group .form-control {
        background-color: transparent !important;
        color: #ffffff !important;
        font-size: 0.95rem;
    }

    .custom-input-group .form-control::placeholder {
        color: #64748b !important;
        opacity: 1;
    }

    /* Fix Browser Autofill White/Light Background Bug */
    .custom-input-group input:-webkit-autofill,
    .custom-input-group input:-webkit-autofill:hover, 
    .custom-input-group input:-webkit-autofill:focus,
    .custom-input-group input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px #1e293b inset !important;
        -webkit-text-fill-color: #ffffff !important;
        caret-color: #ffffff !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;
        border: none !important;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3) !important;
    }

    .input-group-text i {
        font-size: 1.1rem;
        color: #94a3b8;
    }

    .ripple {
        position: relative;
        overflow: hidden;
    }
</style>
@endsection