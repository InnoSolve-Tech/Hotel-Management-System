<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="hotelio-auth-page">
    <main class="hotelio-login">
        <section class="hotelio-login__brand" aria-label="Hot-L welcome">
            <div class="hotelio-login__brand-content">
                <img src="{{ asset('uploads/logo.jpg') }}" alt="Hot-L logo" class="hotelio-login__logo">
                <p class="hotelio-login__eyebrow">Hotel operations platform</p>
                <h1>Hot-L</h1>
                <h2>Welcome back</h2>
                <p>
                    Run rooms, guests, bookings, and hotel workflows from one streamlined workspace.
                </p>
            </div>
        </section>

        <section class="hotelio-login__panel" aria-label="Sign in">
            <div class="hotelio-login__card">
                <div class="hotelio-login__header">
                    <p class="hotelio-login__eyebrow">Secure access</p>
                    <h2>Sign in to your account</h2>
                    <p>Use your administrator-provided credentials to continue.</p>
                </div>

                <form method="post" action="{{ route('login') }}" novalidate>
                    @csrf

                    <div class="hotelio-field">
                        <label for="email" class="form-label">Email address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="admin@hot-l.test"
                            autocomplete="email"
                            autofocus
                            required
                        >
                        @error('email')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="hotelio-field">
                        <label for="password" class="form-label">Password</label>
                        <input
                            id="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                        @error('password')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="hotelio-login__meta">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn bg-navy hotelio-login__submit">Sign in</button>
                </form>
            </div>
        </section>
    </main>

    <script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
