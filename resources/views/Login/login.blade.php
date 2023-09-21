<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dtracker| Sign In</title>
    <!-- Favicon -->
    <!-- <link rel="stylesheet" href="{{ asset('css/loginadmin.css') }}"> -->
    <link rel="shortcut icon" href="{{asset('elegant/img/svg/logo.svg')}}" type="image/x-icon">
    <!-- Custom styles -->
    <link rel="stylesheet" href="{{asset('elegant/css/style.min.css')}}">
</head>

<body>
    <div class="layer"></div>
    <main class="page-center">
        <div class="logo-srs text-center">
            <img src="{{ asset('img/logo-SSS.png') }}" style="height: 100%; width: 50%">
        </div>
        <article class="sign-up">
            <h1 class="sign-up__title">Selamat Datang</h1>

            <p class="sign-up__subtitle">Silakan masukkan Nama atau Email dan Password yang ada miliki untuk mengakses portal <span style="color: #4CAF50">Dtracker</span></p>

            <form class="sign-up-form form" action="{{ route('getauthuserstxt') }}" method="post">
                @csrf
                <label class="form-label-wrapper">
                    <p class="form-label">Email</p>
                    <input class="form-input" type="text" name="email_or_nama_lengkap" placeholder="Masukan email atau nama" required>
                </label>
                <label class="form-label-wrapper">
                    <p class="form-label">Password</p>
                    <input class="form-input" type="password" placeholder="Masukan Password Anda" name="password" id="password" required>
                </label>

                <button class="form-btn primary-default-btn transparent-btn" type="submit">Sign in</button>
            </form>

        </article>
    </main>
    <script src="{{asset('elegant/plugins/chart.min.js')}}"></script>
    <!-- Icons library -->
    <script src="{{asset('elegant/plugins/feather.min.js')}}"></script>
    <!-- Custom scripts -->
    <script src="{{asset('elegant/js/script.js')}}"></script>
</body>

</html>