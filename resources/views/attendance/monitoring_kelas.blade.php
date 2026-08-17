@extends('layouts.admin.tabler')

@section('content')

    @php
        $tingkatRomawi = [
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];
    @endphp

    <div class="page-header d-print-none">
        <div class="container-xl">

            <div class="row g-2 align-items-center">

                <div class="col">

                    <h2 class="page-title">
                        Pemantauan Kehadiran -
                        Kelas {{ $tingkatNama }}
                    </h2>

                    <div class="text-muted mt-1">
                        Menampilkan kehadiran berdasarkan kelas dan mata pelajaran yang
                        ditugaskan kepada guru yang sedang login.
                    </div>

                </div>

            </div>

        </div>
    </div>


    <div class="page-body">

        <div class="container-xl">

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        Data Kehadiran Siswa
                    </h3>

                </div>


                <div class="card-body">

                    {{-- ================================================= --}}
                    {{-- FILTER --}}
                    {{-- ================================================= --}}

                    <div class="row mb-4">


                        {{-- TANGGAL --}}
                        <div class="col-md-4 mb-3 mb-md-0">

                            <label class="form-label">
                                Tanggal
                            </label>

                            <div class="input-icon">

                                <span class="input-icon-addon">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icon-tabler-calendar">

                                        <path stroke="none"
                                            d="M0 0h24v24H0z"
                                            fill="none" />

                                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />

                                        <path d="M16 3v4" />

                                        <path d="M8 3v4" />

                                        <path d="M4 11h16" />

                                    </svg>

                                </span>

                                <input type="text"
                                    id="tanggal"
                                    class="form-control"
                                    value="{{ date('d-m-Y') }}"
                                    autocomplete="off">

                            </div>

                        </div>


                        {{-- KELAS --}}
                        <div class="col-md-4 mb-3 mb-md-0">

                            <label class="form-label">
                                Kelas
                            </label>

                            <select
                                class="form-select"
                                id="kelas_id">

                                <option value="">
                                    Pilih Kelas
                                </option>

                                @foreach ($kelasList as $item)

                                    <option value="{{ $item->kelas_id }}">

                                        {{ $item->nama_kelas }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- MATA PELAJARAN --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Mata Pelajaran
                            </label>

                            <select
                                class="form-select"
                                id="penugasan_id"
                                disabled>

                                <option value="">
                                    Pilih kelas terlebih dahulu
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- INFO --}}
                    <div
                        id="info-filter"
                        class="alert alert-primary d-none">

                    </div>


                    {{-- ================================================= --}}
                    {{-- TABLE --}}
                    {{-- ================================================= --}}

                    <div class="table-responsive">

                        <table class="table table-striped table-hover table-vcenter">

                            <thead>

                                <tr>

                                    <th class="fw-bold text-dark">
                                        No.
                                    </th>

                                    <th class="fw-bold text-dark">
                                        NIS
                                    </th>

                                    <th class="fw-bold text-dark">
                                        Nama Siswa
                                    </th>

                                    <th class="fw-bold text-dark">
                                        Jurusan
                                    </th>

                                    <th class="fw-bold text-dark">
                                        Jam Masuk
                                    </th>

                                    <th class="fw-bold text-dark">
                                        Foto Masuk
                                    </th>

                                    <th class="fw-bold text-dark">
                                        Jam Pulang
                                    </th>

                                    <th class="fw-bold text-dark">
                                        Foto Pulang
                                    </th>

                                    <th class="fw-bold text-dark">
                                        Keterangan
                                    </th>

                                    <th class="fw-bold text-dark">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="loadattendance">

                                <tr>

                                    <td
                                        colspan="10"
                                        class="text-center text-muted py-5">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="40"
                                            height="40"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="mb-2">

                                            <path stroke="none"
                                                d="M0 0h24v24H0z"
                                                fill="none" />

                                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" />

                                            <path d="M16 3v4" />

                                            <path d="M8 3v4" />

                                            <path d="M4 11h16" />

                                        </svg>

                                        <div>
                                            Silakan pilih kelas dan mata pelajaran.
                                        </div>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================= --}}
    {{-- MODAL PETA --}}
    {{-- ================================================= --}}

    <div class="modal modal-blur fade"
        id="modal-tampilkanpeta"
        tabindex="-1"
        role="dialog"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Lokasi Presensi Siswa
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div
                    class="modal-body"
                    id="loadpeta">

                    <div class="text-center py-4">

                        <div
                            class="spinner-border"
                            role="status">
                        </div>

                        <div class="mt-2">
                            Memuat lokasi...
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


@push('myscript')

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        const tanggalInput =
            $("#tanggal");

        const kelasInput =
            $("#kelas_id");

        const penugasanInput =
            $("#penugasan_id");

        const loadAttendance =
            $("#loadattendance");

        const infoFilter =
            $("#info-filter");


        /*
        |--------------------------------------------------------------------------
        | DATA PENUGASAN GURU
        |--------------------------------------------------------------------------
        */

        const penugasanByKelas =
            @json($penugasanByKelas);


        /*
        |--------------------------------------------------------------------------
        | LOAD MATA PELAJARAN BERDASARKAN KELAS
        |--------------------------------------------------------------------------
        */

        kelasInput.on(
            "change",
            function () {

                const kelasId =
                    $(this).val();


                penugasanInput
                    .empty()
                    .prop(
                        "disabled",
                        true
                    );


                infoFilter
                    .addClass("d-none")
                    .html("");


                loadAttendance.html(`
                    <tr>
                        <td colspan="10"
                            class="text-center text-muted py-5">

                            Silakan pilih mata pelajaran.

                        </td>
                    </tr>
                `);


                if (!kelasId) {

                    penugasanInput.append(`
                        <option value="">
                            Pilih kelas terlebih dahulu
                        </option>
                    `);

                    return;
                }


                const data =
                    penugasanByKelas[kelasId];


                if (
                    !data ||
                    data.length === 0
                ) {

                    penugasanInput.append(`
                        <option value="">
                            Tidak ada mata pelajaran
                        </option>
                    `);

                    return;
                }


                penugasanInput.append(`
                    <option value="">
                        Pilih Mata Pelajaran
                    </option>
                `);


                data.forEach(
                    function (item) {

                        penugasanInput.append(`
                            <option value="${item.penugasan_id}">
                                ${item.nama_mapel}
                            </option>
                        `);

                    }
                );


                penugasanInput.prop(
                    "disabled",
                    false
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | LOAD DATA ATTENDANCE
        |--------------------------------------------------------------------------
        */

        function loadData() {

            const tanggal =
                tanggalInput.val();

            const kelasId =
                kelasInput.val();

            const penugasanId =
                penugasanInput.val();


            if (
                !tanggal ||
                !kelasId ||
                !penugasanId
            ) {

                return;
            }


            /*
            | Ambil nama kelas
            */

            const namaKelas =
                kelasInput
                    .find("option:selected")
                    .text()
                    .trim();


            /*
            | Ambil nama mapel
            */

            const namaMapel =
                penugasanInput
                    .find("option:selected")
                    .text()
                    .trim();


            infoFilter
                .removeClass("d-none")
                .html(
                    `<strong>${namaKelas}</strong>
                     &nbsp; - &nbsp;
                     ${namaMapel}`
                );


            $.ajax({

                type: "POST",

                url:
                    "{{ url('/getattendancekelas') }}",

                data: {

                    _token:
                        "{{ csrf_token() }}",

                    tanggal:
                        tanggal,

                    kelas_id:
                        kelasId,

                    penugasan_id:
                        penugasanId

                },


                beforeSend:
                    function () {

                        loadAttendance.html(`
                            <tr>

                                <td
                                    colspan="10"
                                    class="text-center py-5">

                                    <div
                                        class="spinner-border spinner-border-sm me-2"
                                        role="status">
                                    </div>

                                    Memuat data kehadiran...

                                </td>

                            </tr>
                        `);

                    },


                success:
                    function (response) {

                        loadAttendance.html(
                            response
                        );

                    },


                error:
                    function (xhr) {

                        console.error(xhr);


                        let message =
                            "Terjadi kesalahan saat mengambil data kehadiran.";


                        if (
                            xhr.responseJSON &&
                            xhr.responseJSON.message
                        ) {

                            message =
                                xhr.responseJSON.message;

                        }


                        loadAttendance.html(`
                            <tr>

                                <td
                                    colspan="10"
                                    class="text-center text-danger py-5">

                                    ${message}

                                </td>

                            </tr>
                        `);

                    }

            });

        }


        /*
        |--------------------------------------------------------------------------
        | FLATPICKR
        |--------------------------------------------------------------------------
        */

        if (
            typeof flatpickr !==
            "undefined"
        ) {

            flatpickr(
                "#tanggal",
                {

                    dateFormat:
                        "d-m-Y",

                    locale:
                        "id",

                    defaultDate:
                        "today",

                    allowInput:
                        true,


                    onChange:
                        function () {

                            loadData();

                        }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | GANTI MATA PELAJARAN
        |--------------------------------------------------------------------------
        */

        penugasanInput.on(
            "change",
            function () {

                loadData();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | FUNGSI TAMPILKAN PETA
        |--------------------------------------------------------------------------
        */

        window.tampilkanPeta =
            function (id) {


                if (!id) {

                    return;

                }


                $("#loadpeta").html(`
                    <div class="text-center py-4">

                        <div
                            class="spinner-border"
                            role="status">
                        </div>

                        <div class="mt-2">
                            Memuat lokasi...
                        </div>

                    </div>
                `);


                const modalElement =
                    document.getElementById(
                        "modal-tampilkanpeta"
                    );


                const modal =
                    bootstrap.Modal
                        .getOrCreateInstance(
                            modalElement
                        );


                modal.show();


                $.ajax({

                    type: "POST",

                    url:
                        "{{ url('/tampilkanpeta') }}",

                    data: {

                        _token:
                            "{{ csrf_token() }}",

                        id:
                            id

                    },


                    success:
                        function (response) {

                            $("#loadpeta")
                                .html(
                                    response
                                );

                        },


                    error:
                        function () {

                            $("#loadpeta")
                                .html(`
                                    <div
                                        class="alert alert-danger">

                                        Gagal memuat lokasi
                                        presensi.

                                    </div>
                                `);

                        }

                });

            };


    });

</script>

@endpush