<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>dtracker</title>
    <!-- Include Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media (max-width: 767px) {
            .login-box {
                width: 80%;
                max-width: 400px;
                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>
    < <div class="container-fluid">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="login-box col-md-8">
                <div class="logo-srs text-center">
                    <img src="{{ asset('img/logo-SSS.png') }}" style="height: 100%; width: 50%">
                </div>
                <div class="text-center mt-4">
                    <p class="text-secondary" style="margin: 0 0 30px;
                        font-style: normal;
                        font-size: 14px;
                        font-family: Arial, Helvetica, sans-serif;
                        font-weight: 600 ; color: #1e1e1f">
                        Silakan masukkan Nama atau Email dan Password yang ada miliki untuk mengakses portal <span style="color: #4CAF50">Sidak TPH</span>
                    </p>
                </div>
                <form class="mt-4" action="{{ route('getauthuserstxt') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <input type="text" class="form-control" name="email_or_nama_lengkap" required id="email_or_nama_lengkap" autofocus value="{{ old('email_or_nama_lengkap') }}" placeholder="Email / Nama">
                        @error('error')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" id="password" required placeholder="Password">
                        @error('error')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="text-end"> <!-- Right-align the button -->
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                    <div class="logo-srs text-center mt-3">
                        <img src="{{ asset('img/logo-srs.png') }}" style="height: 80%; width: 15%">
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Include Bootstrap 5 JavaScript (popper.js and bootstrap.js) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</body>

</html>