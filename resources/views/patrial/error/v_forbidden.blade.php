<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('dist/assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('dist/assets/images/favicon.ico') }}" type="image/x-icon">

    <!-- CSS Assets -->
    <link rel="stylesheet" href="{{ asset('dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/compiled/css/error.css') }}">

    <!-- Optional: Anti-cache header meta tag -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body>
    <div id="error">
        <div class="error-page container">
            <div class="col-md-8 col-12 offset-md-2">
                <div class="text-center">
                    <!-- Gambar Error 403 -->
                    <img class="img-error" src="{{ asset('dist/assets/compiled/svg/error-403.svg') }}" alt="Forbidden"
                        width="200">

                    <!-- Judul -->
                    <h1 class="error-title mt-4">Akses Ditolak</h1>

                    <!-- Deskripsi -->
                    <p class="fs-5 text-gray-600 mt-2">
                        Anda tidak memiliki izin untuk mengakses halaman ini.
                    </p>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                    <!-- Tombol Logout Aman -->
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="btn btn-lg btn-outline-primary mt-4">
                        Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
