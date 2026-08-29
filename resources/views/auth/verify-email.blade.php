@extends('layouts.auth')

@section('content')
    <div class="text-center">
        <div class="mb-6">
            <i class="fas fa-envelope-circle-check text-6xl text-polysphere-500"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Email</h2>
        <p class="text-gray-600 mb-4">
            We've sent a verification link to <strong>{{ Auth::user()->email }}</strong>.
            Please check your inbox and click the link to verify your email address.
        </p>

        <p class="text-sm text-gray-500 mb-6">
            If you didn't receive the email, you can request a new one below.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="btn-lift w-full flex justify-center py-3 px-4 bg-gradient-to-r from-polysphere-600 to-polysphere-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-paper-plane mr-2"></i> Resend Verification Email
            </button>
        </form>

        {{-- <div class="mt-4">
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="text-sm text-polysphere-600 hover:underline">
                Sign out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div> --}}
    </div>
@endsection