@extends('layouts.admin.tabler')
@section('content')

    <?php
    function selisihKelas($jam_masuk, $jam_keluar)
    {
        list($h, $m, $s) = explode(":", $jam_masuk);
        $dtAwal = mktime($h, $m, $s, "1", "1", "1");
        list($h, $m, $s) = explode(":", $jam_keluar);
        $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
        $dtSelisih = $dtAkhir - $dtAwal;
        $totalmenit = $dtSelisih / 60;
        $jam = explode(".", $totalmenit / 60);
        $sisamenit = ($totalmenit / 60) - $jam[0];
        $sisamenit2 = $sisamenit * 60;
        return $jam[0] . ":" . round($sisamenit2);
    }
                    ?>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Pemantauan Kehadiran - Kelas {{ $kelas }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">

                                <div class="col-md-6">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" />
                                                <path d="M16 3l0 4" />
                                                <path d="M8 3l0 4" />
                                                <path d="M4 11l16 0" />
                                                <path d="M8 15h2v2h-2l0 -2" />
                                            </svg>
                                        </span>

                                        <input type="text" id="tanggal" class="form-control" value="{{ date('d-m-Y') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <select class="form-select" id="kode_jurusan">
                                        <option value="">Semua Jurusan</option>

                                        @foreach($jurusan as $d)
                                            <option value="{{ $d->kode_jurusan }}">
                                                {{ $d->nama_jurusan }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="fw-bold text-dark" style="font-size:13px;">No.</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">NIS</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Nama Siswa</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Jurusan</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Jam Masuk</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Foto Masuk</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Jam Pulang</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Foto Pulang</th>
                                                <th class="fw-bold text-dark" style="font-size:13px;">Keterangan</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody id="loadattendance">
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">
                                                    Pilih tanggal untuk melihat data
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Peta --}}
        <div class="modal modal-blur fade" id="modal-tampilkanpeta" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lokasi Presensi Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="loadpeta"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const kelas = "{{ $kelas }}";

            // Load data hari ini otomatis saat halaman dibuka
            loadData("{{ date('d-m-Y') }}");

            if (typeof flatpickr !== "undefined") {
                flatpickr("#tanggal", {
                    dateFormat: "d-m-Y",
                    locale: "id",
                    defaultDate: "today",
                    onChange: function (selectedDates, dateStr) {
                        loadData(dateStr);
                    }
                });
            }

            function loadData(dateStr) {
                $.ajax({
                    type: 'POST',
                    url: '/getattendancekelas',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggal: dateStr,
                        kelas: kelas
                    },
                    beforeSend: function () {
                        $("#loadattendance").html(
                            "<tr><td colspan='10' class='text-center'>Loading...</td></tr>"
                        );
                    },
                    cache: false,
                    success: function (respond) {
                        $("#loadattendance").html(respond);
                    }
                });
            }
        });
    </script>
@endpush