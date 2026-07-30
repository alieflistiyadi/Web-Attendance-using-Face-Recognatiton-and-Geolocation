<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Reset Password</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-white">

<div id="appCapsule" class="pt-0">

    <div class="login-form mt-1">

        <div class="section">
            <img src="{{ asset('assets/img/login/smksmart.png') }}"
                 class="form-image">
        </div>

        <div class="section mt-1">
            <h1>Reset Password</h1>
            <h4>Silakan buat password baru Anda.</h4>
        </div>

        <div class="section mt-1 mb-5">

            <form action="{{ route('reset.password.update') }}" method="POST">

                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            placeholder="Password Baru"
                            required>
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input
                            type="password"
                            class="form-control"
                            name="password_confirmation"
                            placeholder="Konfirmasi Password"
                            required>
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-block btn-lg">

                    Simpan Password

                </button>

            </form>

        </div>

    </div>

</div>

<script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js') }}"></script>

@if ($errors->any())

<script>

    Swal.fire({
        icon:'error',
        title:'Gagal',
        text:'{{ $errors->first() }}'
    });

</script>

@endif

@if(session('success'))

<script>

    Swal.fire({
        icon:'success',
        title:'Berhasil',
        text:'{{ session("success") }}'
    });

</script>

@endif

</body>
</html>