@extends('layouts.admin.tabler')

@section('content')

    <div class="page-header d-print-none">
        <div class="container-xl">

            <div class="row g-2 align-items-center">

                <div class="col">

                    <h2 class="page-title">
                        Data Siswa
                    </h2>

                    <div class="text-muted mt-1">
                        {{ $penugasan->nama_kelas }}
                        —
                        {{ $penugasan->nama_mapel }}
                    </div>

                </div>

                <div class="col-auto">

                    <a href="{{ route('siswa.guru') }}" class="btn btn-secondary">

                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon">

                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />

                        </svg>

                        Kembali

                    </a>

                </div>

            </div>

        </div>
    </div>


    <div class="page-body">

        <div class="container-xl">

            <div class="card">

                {{-- ==========================================================
                HEADER
                =========================================================== --}}

                <div class="card-header">

                    <h3 class="card-title">

                        {{ $penugasan->nama_kelas }}

                        -

                        {{ $penugasan->nama_mapel }}

                    </h3>

                </div>


                <div class="card-body">


                    {{-- ==========================================================
                    ALERT ERROR
                    =========================================================== --}}

                    @if ($errors->any())

                        <div class="alert alert-danger">

                            <strong>
                                Terjadi kesalahan:
                            </strong>

                            <ul class="mb-0 mt-1">

                                @foreach ($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    {{-- ==========================================================
                    SUCCESS
                    =========================================================== --}}

                    @if (Session::get('success'))

                        <div class="alert alert-success">

                            {{ Session::get('success') }}

                        </div>

                    @endif


                    {{-- ==========================================================
                    WARNING
                    =========================================================== --}}

                    @if (Session::get('warning'))

                        <div class="alert alert-warning">

                            {{ Session::get('warning') }}

                        </div>

                    @endif


                    {{-- ==========================================================
                    HASIL IMPORT
                    =========================================================== --}}

                    @if (session('success_import'))

                        <div class="alert alert-success">

                            <strong>
                                Import berhasil!
                            </strong>

                            <br>

                            Berhasil:
                            {{ session('success_import') }}
                            data

                            <br>

                            Gagal:
                            {{ session('failed_import') ?? 0 }}
                            data

                        </div>

                    @endif


                    {{-- ==========================================================
                    ERROR IMPORT
                    =========================================================== --}}

                    @if (session('errors_import'))

                        <div class="alert alert-danger">

                            <strong>
                                Data yang gagal diimport:
                            </strong>

                            <ul class="mb-0">

                                @foreach (session('errors_import') as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    {{-- ==========================================================
                    BUTTON ACTION
                    =========================================================== --}}

                    <div class="row">

                        <div class="col-12">

                            {{-- TAMBAH DATA --}}

                            <a href="#" class="btn btn-primary" id="btnTambahSiswa">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">

                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />

                                </svg>

                                Tambah Data

                            </a>


                            {{-- IMPORT EXCEL --}}

                            <a href="#" class="btn btn-success" id="btnImportSiswa">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">

                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />

                                    <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v5" />

                                    <path d="M12 17h7" />

                                    <path d="M16 13l4 4l-4 4" />

                                    <path d="M5 17v2a2 2 0 0 0 2 2h3" />

                                </svg>

                                Import Excel

                            </a>


                            {{-- DOWNLOAD TEMPLATE --}}

                            <a href="{{ route('siswa.guru.template') }}" class="btn btn-secondary">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">

                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                    <path d="M12 5l0 14" />

                                    <path d="M5 12l7 7l7 -7" />

                                </svg>

                                Download Template

                            </a>

                        </div>

                    </div>


                    {{-- ==========================================================
                    INFO KELAS
                    =========================================================== --}}

                    <div class="alert alert-info mt-3">

                        <strong>
                            Informasi:
                        </strong>

                        Data siswa yang ditambahkan melalui halaman ini akan
                        otomatis ditempatkan pada kelas

                        <strong>
                            {{ $penugasan->nama_kelas }}
                        </strong>.

                        <br>

                        Mata pelajaran:

                        <strong>
                            {{ $penugasan->nama_mapel }}
                        </strong>.

                    </div>


                    {{-- ==========================================================
                    SEARCH
                    =========================================================== --}}

                    <div class="row mt-3">

                        <div class="col-12">

                            <form method="GET">

                                <div class="row">

                                    <div class="col-md-10">

                                        <input type="text" name="nama_lengkap" class="form-control"
                                            placeholder="Cari Nama Siswa" value="{{ request('nama_lengkap') }}">

                                    </div>


                                    <div class="col-md-2">

                                        <button type="submit" class="btn btn-primary w-100">

                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="icon">

                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                                <path d="M3 10a7 7 0 1 0 14 0a7 7 0 0 0 -14 0" />

                                                <path d="M21 21l-6 -6" />

                                            </svg>

                                            Cari

                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>


                    {{-- ==========================================================
                    TABLE
                    =========================================================== --}}

                    <div class="row mt-3">

                        <div class="col-12">

                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead class="table-light">

                                        <tr>

                                            <th class="fw-bold text-dark">
                                                No
                                            </th>

                                            <th class="fw-bold text-dark">
                                                NIS
                                            </th>

                                            <th class="fw-bold text-dark">
                                                Nama Lengkap
                                            </th>

                                            <th class="fw-bold text-dark">
                                                Kelas
                                            </th>

                                            <th class="fw-bold text-dark">
                                                Jurusan
                                            </th>

                                            <th class="fw-bold text-dark">
                                                No. HP
                                            </th>

                                            <th class="fw-bold text-dark">
                                                Aksi
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @forelse ($siswa as $d)

                                            <tr>

                                                <td>
                                                    {{ $loop->iteration + $siswa->firstItem() - 1 }}
                                                </td>

                                                <td>
                                                    {{ $d->nis }}
                                                </td>

                                                <td>
                                                    {{ $d->nama_lengkap }}
                                                </td>

                                                <td>
                                                    {{ $d->kelas }}
                                                </td>

                                                <td>
                                                    {{ $d->nama_jurusan ?? '-' }}
                                                </td>

                                                <td>
                                                    {{ $d->no_hp ?? '-' }}
                                                </td>

                                                <td>

                                                    <div class="btn-group">

                                                        {{-- EDIT --}}

                                                        <a href="#" class="edit btn btn-info btn-sm" nis="{{ $d->nis }}"
                                                            redirect_kelas="{{ $penugasan->nama_kelas }}" title="Edit">

                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon">

                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                                                <path
                                                                    d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />

                                                                <path
                                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" />

                                                                <path d="M16 5l3 3" />

                                                            </svg>

                                                        </a>


                                                        {{-- DELETE --}}

                                                        <form action="{{ route('siswa.guru.delete', $d->nis) }}" method="POST"
                                                            style="margin-left:5px">

                                                            @csrf

                                                            <input type="hidden" name="redirect_kelas"
                                                                value="{{ $penugasan->nama_kelas }}">

                                                            <button type="submit" class="btn btn-danger btn-sm delete-confirm"
                                                                title="Hapus">

                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" class="icon">

                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                                                    <path d="M4 7l16 0" />

                                                                    <path d="M10 11l0 6" />

                                                                    <path d="M14 11l0 6" />

                                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />

                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />

                                                                </svg>

                                                            </button>

                                                        </form>

                                                    </div>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="7" class="text-center text-muted py-5">

                                                    Tidak ada data siswa untuk kelas

                                                    <strong>
                                                        {{ $penugasan->nama_kelas }}
                                                    </strong>.

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>


                            {{ $siswa->links('vendor.pagination.bootstrap-5') }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ==========================================================
    MODAL TAMBAH SISWA
    =========================================================== --}}

    <div class="modal modal-blur fade" id="modal-inputsiswa" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">


                <div class="modal-header">

                    <h5 class="modal-title">

                        Tambah Data Siswa

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">

                    </button>

                </div>


                <div class="modal-body">

                    <form action="{{ route('siswa.guru.store') }}" method="POST" id="formSiswaGuru">

                        @csrf


                        {{-- PENUGASAN --}}

                        <input type="hidden" name="penugasan_id" value="{{ $penugasan->penugasan_id }}">


                        <input type="hidden" name="kelas_id" value="{{ $penugasan->kelas_id }}">


                        {{-- KELAS --}}

                        <input type="hidden" name="kelas" value="{{ $penugasan->nama_kelas }}">


                        {{-- KODE JURUSAN --}}

                        <input type="hidden" name="kode_jurusan" value="{{ $penugasan->kode_jurusan }}">


                        {{-- REDIRECT --}}

                        <input type="hidden" name="redirect_kelas" value="{{ $penugasan->nama_kelas }}">


                        {{-- NIS --}}

                        <div class="mb-3">

                            <label class="form-label">
                                NIS
                            </label>

                            <input type="text" name="nis" id="nisGuru" class="form-control" placeholder="Masukkan NIS"
                                required>

                        </div>


                        {{-- NAMA --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Nama Lengkap
                            </label>

                            <input type="text" name="nama_lengkap" id="namaGuru" class="form-control"
                                placeholder="Masukkan Nama Siswa" required>

                        </div>


                        {{-- KELAS --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Kelas
                            </label>

                            <input type="text" class="form-control" value="{{ $penugasan->nama_kelas }}" disabled>

                        </div>


                        {{-- MAPEL --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Mata Pelajaran
                            </label>

                            <input type="text" class="form-control" value="{{ $penugasan->nama_mapel }}" disabled>

                        </div>


                        {{-- JURUSAN --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Jurusan
                            </label>

                            <input type="text" class="form-control" value="{{ $penugasan->kode_jurusan }}" disabled>

                        </div>


                        {{-- NO HP --}}

                        <div class="mb-3">

                            <label class="form-label">
                                No. HP
                            </label>

                            <input type="text" name="no_hp" id="noHpGuru" class="form-control" placeholder="+628xxxxxxxxxx"
                                maxlength="18" required>

                        </div>


                        {{-- BUTTON --}}

                        <button type="submit" class="btn btn-primary w-100">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                <path d="M10 14l11 -11" />

                                <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />

                            </svg>

                            Simpan

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>



    {{-- ==========================================================
    MODAL IMPORT EXCEL
    =========================================================== --}}

    <div class="modal modal-blur fade" id="modal-import" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">


                <form action="{{ route('siswa.guru.import') }}" method="POST" enctype="multipart/form-data">

                    @csrf


                    {{-- PENUGASAN --}}

                    <input type="hidden" name="penugasan_id" value="{{ $penugasan->penugasan_id }}">


                    <input type="hidden" name="kelas_id" value="{{ $penugasan->kelas_id }}">


                    {{-- KELAS --}}

                    <input type="hidden" name="kelas" value="{{ $penugasan->nama_kelas }}">


                    {{-- JURUSAN --}}

                    <input type="hidden" name="kode_jurusan" value="{{ $penugasan->kode_jurusan }}">


                    <div class="modal-header">

                        <h5 class="modal-title">

                            Import Data Siswa

                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">

                        </button>

                    </div>


                    <div class="modal-body">


                        <div class="alert alert-info">

                            Data yang diimport akan dimasukkan ke kelas:

                            <strong>
                                {{ $penugasan->nama_kelas }}
                            </strong>

                            <br>

                            Mata Pelajaran:

                            <strong>
                                {{ $penugasan->nama_mapel }}
                            </strong>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">

                                File Excel

                            </label>

                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>

                        </div>


                        <div class="text-muted small">

                            Pastikan format Excel sesuai dengan template
                            yang telah disediakan.

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                            Batal

                        </button>


                        <button type="submit" class="btn btn-success">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />

                                <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v5" />

                                <path d="M12 17h7" />

                                <path d="M16 13l4 4l-4 4" />

                            </svg>

                            Import

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    {{-- ==========================================================
    MODAL EDIT
    =========================================================== --}}

    <div class="modal modal-blur fade" id="modal-editsiswa" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Edit Data Siswa
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">

                    </button>

                </div>


                <div class="modal-body" id="loadeditform">

                </div>

            </div>

        </div>

    </div>

@endsection



@push('myscript')

    <script>

        $(function () {


            /*
            |--------------------------------------------------------------------------
            | TOMBOL TAMBAH
            |--------------------------------------------------------------------------
            */

            $("#btnTambahSiswa").click(function (e) {

                e.preventDefault();

                $("#formSiswaGuru")[0].reset();

                $("#modal-inputsiswa").modal("show");

            });


            /*
            |--------------------------------------------------------------------------
            | TOMBOL IMPORT
            |--------------------------------------------------------------------------
            */

            $("#btnImportSiswa").click(function (e) {

                e.preventDefault();

                $("#modal-import").modal("show");

            });


            /*
            |--------------------------------------------------------------------------
            | FORMAT NOMOR HP
            |--------------------------------------------------------------------------
            */

            $("#noHpGuru").on("input", function () {

                var nomor = $(this).val();

                nomor = nomor.replace(/\D/g, "");


                if (nomor.startsWith("620")) {

                    nomor = "62" + nomor.substring(3);

                }


                if (nomor.startsWith("0")) {

                    nomor = "62" + nomor.substring(1);

                }


                if (
                    nomor !== "" &&
                    !nomor.startsWith("62")
                ) {

                    nomor = "62" + nomor;

                }


                if (nomor !== "") {

                    $(this).val("+" + nomor);

                }

            });


            /*
            |--------------------------------------------------------------------------
            | HANYA ANGKA UNTUK NO HP
            |--------------------------------------------------------------------------
            */

            $("#noHpGuru").keypress(function (e) {

                var char = String.fromCharCode(e.which);

                if (!/[0-9]/.test(char)) {

                    return false;

                }

            });


            /*
            |--------------------------------------------------------------------------
            | VALIDASI TAMBAH SISWA
            |--------------------------------------------------------------------------
            */

            $("#formSiswaGuru").submit(function (e) {

                var nis = $("#nisGuru").val().trim();

                var nama = $("#namaGuru").val().trim();

                var noHp = $("#noHpGuru").val().trim();


                if (nis === "") {

                    e.preventDefault();

                    Swal.fire({

                        title: "Warning!",

                        text: "NIS harus diisi.",

                        icon: "warning",

                        confirmButtonText: "OK"

                    });

                    return false;

                }


                if (nama === "") {

                    e.preventDefault();

                    Swal.fire({

                        title: "Warning!",

                        text: "Nama lengkap harus diisi.",

                        icon: "warning",

                        confirmButtonText: "OK"

                    });

                    return false;

                }


                if (noHp === "") {

                    e.preventDefault();

                    Swal.fire({

                        title: "Warning!",

                        text: "No. HP harus diisi.",

                        icon: "warning",

                        confirmButtonText: "OK"

                    });

                    return false;

                }


                var hp = noHp.replace(/\+/g, "");


                if (!hp.startsWith("62")) {

                    e.preventDefault();

                    Swal.fire({

                        title: "Warning!",

                        text: "Nomor HP harus menggunakan format +62.",

                        icon: "warning",

                        confirmButtonText: "OK"

                    });

                    return false;

                }


                var nomor = hp.substring(2);


                if (
                    nomor.length < 9 ||
                    nomor.length > 15
                ) {

                    e.preventDefault();

                    Swal.fire({

                        title: "Warning!",

                        text: "Nomor HP harus terdiri dari 9 sampai 15 digit.",

                        icon: "warning",

                        confirmButtonText: "OK"

                    });

                    return false;

                }


                return true;

            });


            /*
            |--------------------------------------------------------------------------
            | EDIT SISWA
            |--------------------------------------------------------------------------
            */

            $(".edit").click(function (e) {

                e.preventDefault();


                var nis = $(this).attr("nis");

                var redirectKelas =
                    $(this).attr("redirect_kelas");


                $.ajax({

                    type: "POST",

                    url: "{{ route('siswa.guru.edit') }}",

                    cache: false,

                    data: {

                        _token: "{{ csrf_token() }}",

                        nis: nis,

                        redirect_kelas: redirectKelas

                    },


                    success: function (response) {

                        $("#loadeditform").html(response);

                        $("#modal-editsiswa").modal("show");

                    },


                    error: function (xhr) {

                        Swal.fire({

                            title: "Error!",

                            text: "Gagal mengambil data siswa.",

                            icon: "error",

                            confirmButtonText: "OK"

                        });

                    }

                });

            });


            /*
            |--------------------------------------------------------------------------
            | DELETE SISWA
            |--------------------------------------------------------------------------
            */

            $(".delete-confirm").click(function (e) {

                e.preventDefault();


                var form =
                    $(this).closest("form");


                Swal.fire({

                    title: "Apakah Anda yakin?",

                    text: "Data siswa akan dihapus.",

                    icon: "warning",

                    showCancelButton: true,

                    confirmButtonColor: "#3085d6",

                    cancelButtonColor: "#d33",

                    confirmButtonText: "Ya, hapus!",

                    cancelButtonText: "Batal"

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });

        });

    </script>

@endpush