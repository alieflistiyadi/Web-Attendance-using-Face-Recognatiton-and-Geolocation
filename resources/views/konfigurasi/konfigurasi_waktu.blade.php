@extends('layouts.admin.tabler')

@section('content')

    <div class="page-header d-print-none">
        <div class="container-xl">
            <h2 class="page-title">
                Konfigurasi Waktu Absensi
            </h2>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            <div class="row justify-content-center">

                <div class="col-md-8">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Pengaturan Jadwal Absensi
                            </h3>
                        </div>

                        <div class="card-body">

                            @if(Session::get('success'))
                                <div class="alert alert-success">
                                    {{ Session::get('success') }}
                                </div>
                            @endif

                            <form action="/konfigurasi/updatewaktu" method="POST">

                                @csrf

                                <h4 class="mb-3 text-primary">
                                    Jam Masuk
                                </h4>

                                <div class="row">

                                    <div class="col-md-4">

                                        <label>Jam Mulai</label>

                                        <input type="time" name="jam_mulai_masuk" class="form-control"
                                            value="{{ $waktu->jam_mulai_masuk }}">

                                    </div>

                                    <div class="col-md-4">

                                        <label>Batas Telat</label>

                                        <input type="time" name="batas_telat" class="form-control"
                                            value="{{ $waktu->batas_telat }}">

                                    </div>

                                    <div class="col-md-4">

                                        <label>Jam Tutup Masuk</label>

                                        <input type="time" name="batas_masuk" class="form-control"
                                            value="{{ $waktu->batas_masuk }}">

                                    </div>

                                </div>

                                <hr>

                                <h4 class="mb-3 text-success">
                                    Jam Pulang
                                </h4>

                                <div class="row">

                                    <div class="col-md-6">

                                        <label>Jam Mulai Pulang</label>

                                        <input type="time" name="jam_mulai_pulang" class="form-control"
                                            value="{{ $waktu->jam_mulai_pulang }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label>Jam Tutup Pulang</label>

                                        <input type="time" name="batas_pulang" class="form-control"
                                            value="{{ $waktu->batas_pulang }}">

                                    </div>

                                </div>

                                <div class="mt-4">

                                    <button class="btn btn-primary w-100">

                                        Update Konfigurasi

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection