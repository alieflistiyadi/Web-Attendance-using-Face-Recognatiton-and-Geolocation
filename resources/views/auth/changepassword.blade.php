<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>Ganti Password | Admin E-Attendance SMK SMART</title>
    <link href="{{ asset('tabler/dist/css/tabler.min.css?1674944402')}}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/tabler-vendors.min.css?1674944402')}}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/demo.min.css?1674944402')}}" rel="stylesheet" />
</head>

<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <img src="{{ asset('/static/logo.svg')}}" height="36" alt="">
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-2">Ganti Password Anda</h2>
                    <p class="text-muted text-center mb-4">
                        Password Anda baru saja direset oleh Superadmin. Untuk keamanan, silakan buat password baru
                        sebelum melanjutkan.
                    </p>

                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form action="{{ route('ubah-password-wajib.update') }}" method="post" autocomplete="off"
                        novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Password baru"
                                autocomplete="new-password">
                            <small class="text-muted">Minimal 8 karakter, mengandung huruf besar, huruf kecil, angka,
                                dan simbol.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Ulangi password baru" autocomplete="new-password">
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Simpan Password Baru</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('tabler/dist/js/tabler.min.js?1674944402')}}" defer></script>
    <script src="{{ asset('tabler/dist/js/demo.min.js?1674944402')}}" defer></script>
</body>

</html>