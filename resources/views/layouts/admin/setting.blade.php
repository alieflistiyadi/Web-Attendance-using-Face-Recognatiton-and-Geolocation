@extends('layouts.admin.tabler')

@section('content')

<div class="container-xl mt-3">

    <h2>Pengaturan Akun Admin</h2>

    <div class="card mt-3">
        <div class="card-body">

            <form action="/panel/setting/update" method="POST">
                @csrf

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ $admin->name }}">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ $admin->email }}">
                </div>

                <div class="mb-3">
                    <label>Password (Jikalau ingin diubah)</label>

                    <input type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror">

                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <small class="text-muted">
                        Password minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol.
                    </small>
                </div>

                <button class="btn btn-primary">
                    Simpan
                </button>

            </form>

        </div>
    </div>

</div>

@endsection