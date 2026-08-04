<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>Lupa Password | Admin E-Attendance SMK SMART</title>
    <link href="{{ asset('tabler/dist/css/tabler.min.css?1674944402')}}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/tabler-vendors.min.css?1674944402')}}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/demo.min.css?1674944402')}}" rel="stylesheet" />
</head>

<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="/panel" class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('/static/logo.svg')}}" height="36" alt="">
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-2">Lupa Password</h2>
                    <p class="text-muted text-center mb-4">
                        Masukkan email akun Anda. Permintaan akan diteruskan ke Superadmin untuk disetujui.
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning">{{ session('warning') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form action="{{ route('forgot-password-admin.submit') }}" method="post" autocomplete="off"
                        novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email anda @gmail.com"
                                autocomplete="off">
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Kirim Permintaan</button>
                        </div>
                        <span class="form-label d-flex justify-content-center mt-3">
                            <a href="/panel">Kembali ke halaman login</a>
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('tabler/dist/js/tabler.min.js?1674944402')}}" defer></script>
    <script src="{{ asset('tabler/dist/js/demo.min.js?1674944402')}}" defer></script>
</body>

</html>