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

                <div class="col-xl-11">

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

                            <form action="{{ route('konfigurasi.updatewaktu') }}" method="POST">

                                @csrf

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover align-middle">

                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th class="fw-bold text-dark" style="font-size:13px;">Hari</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Jam Mulai Masuk (WIB)</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Batas Telat (WIB)</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Jam Tutup Masuk (WIB)</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Jam Mulai Pulang (WIB)</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Jam Tutup Pulang (WIB)</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach($waktu as $item)

                                            <tr>

                                                <td class="fw-bold fs-4 text-center align-middle">

                                                    @php
                                                        $namaHari = [
                                                            1 => 'Senin',
                                                            2 => 'Selasa',
                                                            3 => 'Rabu',
                                                            4 => 'Kamis',
                                                            5 => 'Jumat'
                                                        ];
                                                    @endphp

                                                    {{ $namaHari[$item->hari] }}

                                                    <input type="hidden" name="id[]" value="{{ $item->id }}">
                                                    <input type="hidden" name="hari[]" value="{{ $item->hari }}">

                                                </td>

                                                <td>
                                                    <input type="time"
                                                        class="form-control"
                                                        name="jam_mulai_masuk[]"
                                                        value="{{ $item->jam_mulai_masuk }}">
                                                </td>

                                                <td>
                                                    <input type="time"
                                                        class="form-control"
                                                        name="batas_telat[]"
                                                        value="{{ $item->batas_telat }}">
                                                </td>

                                                <td>
                                                    <input type="time"
                                                        class="form-control"
                                                        name="batas_masuk[]"
                                                        value="{{ $item->batas_masuk }}">
                                                </td>

                                                <td>
                                                    <input type="time"
                                                        class="form-control"
                                                        name="jam_mulai_pulang[]"
                                                        value="{{ $item->jam_mulai_pulang }}">
                                                </td>

                                                <td>
                                                    <input type="time"
                                                        class="form-control"
                                                        name="batas_pulang[]"
                                                        value="{{ $item->batas_pulang }}">
                                                </td>

                                            </tr>

                                            @endforeach


                                        </tbody>

                                    </table>

                                </div>

                                <div class="alert alert-info mt-3 mb-0">
                                    <strong>Informasi:</strong>
                                    Seluruh waktu menggunakan zona waktu <strong>WIB (UTC+7)</strong>.
                                    Konfigurasi hanya berlaku untuk hari <strong>Senin–Jumat</strong>,
                                    sedangkan <strong>Sabtu dan Minggu</strong> otomatis dianggap sebagai hari libur.
                                </div>

                                <button class="btn btn-primary w-100 mt-3">
                                    Simpan Konfigurasi
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection