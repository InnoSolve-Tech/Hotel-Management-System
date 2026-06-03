<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="hotelio-auth-page">
    <main class="d-flex align-items-center justify-content-center min-vh-100 px-3">
        <section class="card shadow-sm" style="max-width: 32rem; width: 100%;">
            <div class="card-body text-center">
                <h1 class="h4 mb-3">Registration is disabled</h1>
                <p class="mb-4">Please sign in with an administrator-provided account.</p>
                <a href="{{ route('login') }}" class="btn bg-navy w-100">Back to sign in</a>
            </div>
        </section>
    </main>
</body>
</html>
