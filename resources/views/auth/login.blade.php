@extends('layouts.auth')

@section('title', 'Duralux || Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card border-0 shadow-lg mt-5 overflow-hidden" style="background: rgba(17, 24, 39, 0.8); backdrop-filter: blur(10px); border-radius: 20px;">
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <a href="#" class="d-block">
                        <img src="{{ asset('assets/images/logo-full.png') }}" alt="" class="img-fluid" style="max-height: 40px;">
                    </a>
                </div>

                <form action="#" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-white-50">Email manzili</label>
                        <div class="input-group input-group-lg bg-dark border-0 rounded-3 overflow-hidden">
                            <span class="input-group-text bg-transparent border-0 text-muted">
                                <i class="feather-mail"></i>
                            </span>
                            <input type="email" class="form-control bg-transparent border-0 text-white shadow-none ps-0" placeholder="example@mail.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label text-white-50">Parol</label>
                            <a href="#" class="text-primary small text-decoration-none">Parolni unutdingizmi?</a>
                        </div>
                        <div class="input-group input-group-lg bg-dark border-0 rounded-3 overflow-hidden">
                            <span class="input-group-text bg-transparent border-0 text-muted">
                                <i class="feather-lock"></i>
                            </span>
                            <input type="password" class="form-control bg-transparent border-0 text-white shadow-none ps-0" placeholder="••••••••" required>
                        </div>
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

    .btn-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }

    .form-control:focus {
        background-color: #1a2234 !important;
        color: white;
    }

    .input-group-text i {
        font-size: 1.1rem;
    }

    .ripple {
        position: relative;
        overflow: hidden;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeIn 0.8s ease-out;
    }
</style>
@endsection