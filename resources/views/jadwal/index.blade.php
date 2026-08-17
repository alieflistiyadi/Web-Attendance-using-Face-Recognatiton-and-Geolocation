@extends('layouts.admin.tabler')

@section('content')

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row align-items-center">

                <div class="col">
                    <div class="page-pretitle">
                        Konfigurasi
                    </div>

                    <h2 class="page-title">
                        Jadwal Pelajaran
                    </h2>
                </div>

                <div class="col-auto ms-auto">
                    <div class="btn-list">

                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPenugasan">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon">

                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>

                            </svg>

                            Tambah Penugasan
                        </button>

                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalJadwal">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon">

                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>

                            </svg>

                            Tambah Jadwal
                        </button>

                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="page-body">

        <div class="container-xl">

            {{-- ALERT --}}
            @if(session('success'))

                <div class="alert alert-success alert-dismissible" role="alert">

                    <div>
                        {{ session('success') }}
                    </div>

                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close">
                    </a>

                </div>

            @endif


            @if(session('error'))

                <div class="alert alert-danger alert-dismissible" role="alert">

                    <div>
                        {{ session('error') }}
                    </div>

                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close">
                    </a>

                </div>

            @endif


            {{-- TAB --}}
            <div class="card">

                <div class="card-header">

                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">

                        <li class="nav-item">

                            <a href="#tab-penugasan" class="nav-link active" data-bs-toggle="tab">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">

                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4"></path>
                                    <path d="M16 11l2 2l4 -4"></path>

                                </svg>

                                Guru & Mata Pelajaran

                            </a>

                        </li>

                        <li class="nav-item">

                            <a href="#tab-jadwal" class="nav-link" data-bs-toggle="tab">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">

                                    <path d="M4 5h16"></path>
                                    <path d="M4 10h16"></path>
                                    <path d="M4 15h16"></path>
                                    <path d="M4 20h16"></path>

                                </svg>

                                Jadwal Pelajaran

                            </a>

                        </li>

                    </ul>

                </div>


                <div class="card-body">

                    <div class="tab-content">

                        {{-- ================================================= --}}
                        {{-- TAB PENUGASAN --}}
                        {{-- ================================================= --}}

                        <div class="tab-pane active show" id="tab-penugasan">

                            <div class="d-flex justify-content-between mb-3">

                                <div>

                                    <h3 class="card-title mb-1">
                                        Penugasan Guru & Mata Pelajaran
                                    </h3>

                                    <div class="text-muted">
                                        Hubungkan guru, mata pelajaran dan kelas.
                                    </div>

                                </div>

                            </div>


                            <div class="table-responsive">

                                <table class="table table-vcenter card-table">

                                    <thead>

                                        <tr>

                                            <th width="50">
                                                #
                                            </th>

                                            <th>
                                                Guru
                                            </th>

                                            <th>
                                                Mata Pelajaran
                                            </th>

                                            <th>
                                                Kelas
                                            </th>

                                            <th class="text-end">
                                                Aksi
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @forelse($penugasan as $item)

                                            <tr>

                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>

                                                <td>

                                                    <div class="fw-bold">
                                                        {{ $item->guru->name }}
                                                    </div>

                                                    <div class="text-muted small">
                                                        {{ $item->guru->email }}
                                                    </div>

                                                </td>


                                                <td>

                                                    <div class="fw-bold">
                                                        {{ $item->mataPelajaran->nama_mapel }}
                                                    </div>

                                                    <div class="text-muted small">
                                                        {{ $item->mataPelajaran->kode_mapel }}
                                                    </div>

                                                </td>


                                                <td>

                                                    <span class="badge bg-blue-lt">

                                                        {{ $item->kelas->tingkat }}
                                                        -
                                                        {{ $item->kelas->nama_kelas }}

                                                        @if($item->kelas->kode_jurusan)
                                                            {{ $item->kelas->kode_jurusan }}
                                                        @endif

                                                    </span>

                                                </td>


                                                <td class="text-end">

                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditPenugasan{{ $item->id }}">

                                                        Edit

                                                    </button>


                                                    <form action="{{ route('jadwal.penugasan.delete', $item->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Hapus penugasan ini?')">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button class="btn btn-sm btn-danger">

                                                            Hapus

                                                        </button>

                                                    </form>

                                                </td>

                                            </tr>


                                            {{-- MODAL EDIT PENUGASAN --}}

                                            <div class="modal modal-blur fade" id="modalEditPenugasan{{ $item->id }}"
                                                tabindex="-1">

                                                <div class="modal-dialog modal-dialog-centered">

                                                    <div class="modal-content">

                                                        <form method="POST"
                                                            action="{{ route('jadwal.penugasan.update', $item->id) }}">

                                                            @csrf
                                                            @method('PUT')

                                                            <div class="modal-header">

                                                                <h5 class="modal-title">
                                                                    Edit Penugasan
                                                                </h5>

                                                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                                </button>

                                                            </div>


                                                            <div class="modal-body">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Guru
                                                                    </label>

                                                                    <select name="guru_id" class="form-select" required>

                                                                        @foreach($guru as $g)

                                                                            <option value="{{ $g->id }}"
                                                                                @selected($g->id == $item->guru_id)>

                                                                                {{ $g->name }}

                                                                            </option>

                                                                        @endforeach

                                                                    </select>

                                                                </div>


                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Mata Pelajaran
                                                                    </label>

                                                                    <select name="mata_pelajaran_id" class="form-select"
                                                                        required>

                                                                        @foreach($mapel as $m)

                                                                            <option value="{{ $m->id }}"
                                                                                @selected($m->id == $item->mata_pelajaran_id)>

                                                                                {{ $m->kode_mapel }}
                                                                                -
                                                                                {{ $m->nama_mapel }}

                                                                            </option>

                                                                        @endforeach

                                                                    </select>

                                                                </div>


                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Kelas
                                                                    </label>

                                                                    <select name="kelas_id" class="form-select" required>

                                                                        @foreach($kelas as $k)

                                                                            <option value="{{ $k->id }}"
                                                                                @selected($k->id == $item->kelas_id)>

                                                                                {{ $k->tingkat }}
                                                                                -
                                                                                {{ $k->nama_kelas }}
                                                                                -
                                                                                {{ $k->kode_jurusan }}

                                                                            </option>

                                                                        @endforeach

                                                                    </select>

                                                                </div>

                                                            </div>


                                                            <div class="modal-footer">

                                                                <button type="button" class="btn me-auto"
                                                                    data-bs-dismiss="modal">

                                                                    Batal

                                                                </button>

                                                                <button type="submit" class="btn btn-primary">

                                                                    Simpan

                                                                </button>

                                                            </div>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        @empty

                                            <tr>

                                                <td colspan="5" class="text-center text-muted py-5">

                                                    Belum ada penugasan guru.

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- TAB JADWAL --}}
                        {{-- ================================================= --}}

                        <div class="tab-pane" id="tab-jadwal">

                            <div class="d-flex justify-content-between mb-3">

                                <div>

                                    <h3 class="card-title mb-1">
                                        Jadwal Pelajaran
                                    </h3>

                                    <div class="text-muted">
                                        Atur hari, jam pelajaran dan waktu absensi.
                                    </div>

                                </div>

                            </div>


                            <div class="table-responsive">

                                <table class="table table-vcenter card-table">

                                    <thead>

                                        <tr>

                                            <th>
                                                Hari
                                            </th>

                                            <th>
                                                Jam
                                            </th>

                                            <th>
                                                Mata Pelajaran
                                            </th>

                                            <th>
                                                Guru
                                            </th>

                                            <th>
                                                Kelas
                                            </th>

                                            <th>
                                                Absensi
                                            </th>

                                            <th class="text-end">
                                                Aksi
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @forelse($jadwal as $item)

                                            <tr>

                                                <td>

                                                    @php
                                                        $hari = [
                                                            1 => 'Senin',
                                                            2 => 'Selasa',
                                                            3 => 'Rabu',
                                                            4 => 'Kamis',
                                                            5 => 'Jumat',
                                                        ];
                                                    @endphp

                                                    <span class="badge bg-green-lt">

                                                        {{ $hari[$item->hari] }}

                                                    </span>

                                                </td>


                                                <td>

                                                    <strong>
                                                        {{ substr($item->jam_mulai, 0, 5) }}
                                                    </strong>

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                    <strong>
                                                        {{ substr($item->jam_selesai, 0, 5) }}
                                                    </strong>

                                                </td>


                                                <td>

                                                    {{ $item->penugasan->mataPelajaran->nama_mapel }}

                                                </td>


                                                <td>

                                                    {{ $item->penugasan->guru->name }}

                                                </td>


                                                <td>

                                                    <span class="badge bg-blue-lt">

                                                        {{ $item->penugasan->kelas->tingkat }}
                                                        -
                                                        {{ $item->penugasan->kelas->nama_kelas }}
                                                        -
                                                        {{ $item->penugasan->kelas->kode_jurusan }}

                                                    </span>

                                                </td>


                                                <td>

                                                    <div class="small">

                                                        Mulai:
                                                        <strong>
                                                            {{ substr($item->jam_mulai_absen, 0, 5) }}
                                                        </strong>

                                                    </div>

                                                    <div class="small text-danger">

                                                        Telat:
                                                        <strong>
                                                            {{ substr($item->batas_telat, 0, 5) }}
                                                        </strong>

                                                    </div>

                                                </td>


                                                <td class="text-end">

                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditJadwal{{ $item->id }}">

                                                        Edit

                                                    </button>


                                                    <form action="{{ route('jadwal.delete', $item->id) }}" method="POST"
                                                        class="d-inline" onsubmit="return confirm('Hapus jadwal ini?')">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button class="btn btn-sm btn-danger">

                                                            Hapus

                                                        </button>

                                                    </form>

                                                </td>

                                            </tr>


                                            {{-- MODAL EDIT JADWAL --}}

                                            <div class="modal modal-blur fade" id="modalEditJadwal{{ $item->id }}"
                                                tabindex="-1">

                                                <div class="modal-dialog modal-dialog-centered">

                                                    <div class="modal-content">

                                                        <form method="POST" action="{{ route('jadwal.update', $item->id) }}">

                                                            @csrf
                                                            @method('PUT')

                                                            <div class="modal-header">

                                                                <h5 class="modal-title">
                                                                    Edit Jadwal
                                                                </h5>

                                                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                                </button>

                                                            </div>


                                                            <div class="modal-body">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Penugasan
                                                                    </label>

                                                                    <select name="penugasan_id" class="form-select" required>

                                                                        @foreach($penugasan as $p)

                                                                            <option value="{{ $p->id }}"
                                                                                @selected($p->id == $item->penugasan_id)>

                                                                                {{ $p->kelas->tingkat }} -
                                                                                {{ $p->kelas->nama_kelas }} -
                                                                                {{ $p->kelas->kode_jurusan }}
                                                                                |
                                                                                {{ $p->mataPelajaran->nama_mapel }}
                                                                                |
                                                                                {{ $p->guru->name }}

                                                                            </option>

                                                                        @endforeach

                                                                    </select>

                                                                </div>


                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Hari
                                                                    </label>

                                                                    <select name="hari" class="form-select" required>

                                                                        @foreach($hari as $key => $nama)

                                                                            <option value="{{ $key }}" @selected($item->hari == $key)>

                                                                                {{ $nama }}

                                                                            </option>

                                                                        @endforeach

                                                                    </select>

                                                                </div>


                                                                <div class="row">

                                                                    <div class="col-md-6 mb-3">

                                                                        <label class="form-label">
                                                                            Jam Mulai
                                                                        </label>

                                                                        <input type="time" name="jam_mulai" class="form-control"
                                                                            value="{{ substr($item->jam_mulai, 0, 5) }}"
                                                                            required>

                                                                    </div>


                                                                    <div class="col-md-6 mb-3">

                                                                        <label class="form-label">
                                                                            Jam Selesai
                                                                        </label>

                                                                        <input type="time" name="jam_selesai"
                                                                            class="form-control"
                                                                            value="{{ substr($item->jam_selesai, 0, 5) }}"
                                                                            required>

                                                                    </div>

                                                                </div>


                                                                <div class="row">

                                                                    <div class="col-md-6 mb-3">

                                                                        <label class="form-label">
                                                                            Mulai Absen
                                                                        </label>

                                                                        <input type="time" name="jam_mulai_absen"
                                                                            class="form-control"
                                                                            value="{{ substr($item->jam_mulai_absen, 0, 5) }}"
                                                                            required>

                                                                    </div>


                                                                    <div class="col-md-6 mb-3">

                                                                        <label class="form-label">
                                                                            Batas Telat
                                                                        </label>

                                                                        <input type="time" name="batas_telat"
                                                                            class="form-control"
                                                                            value="{{ substr($item->batas_telat, 0, 5) }}"
                                                                            required>

                                                                    </div>

                                                                </div>

                                                            </div>


                                                            <div class="modal-footer">

                                                                <button type="button" class="btn me-auto"
                                                                    data-bs-dismiss="modal">

                                                                    Batal

                                                                </button>

                                                                <button type="submit" class="btn btn-primary">

                                                                    Simpan

                                                                </button>

                                                            </div>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        @empty

                                            <tr>

                                                <td colspan="7" class="text-center text-muted py-5">

                                                    Belum ada jadwal pelajaran.

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH PENUGASAN --}}
    {{-- ========================================================= --}}

    <div class="modal modal-blur fade" id="modalPenugasan" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" action="{{ route('jadwal.penugasan.store') }}">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Tambah Penugasan Guru
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div class="alert alert-info">

                            Tentukan guru yang mengajar mata pelajaran pada kelas tertentu.

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Guru
                            </label>

                            <select name="guru_id" class="form-select" required>

                                <option value="">
                                    -- Pilih Guru --
                                </option>

                                @foreach($guru as $g)

                                    <option value="{{ $g->id }}">

                                        {{ $g->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Mata Pelajaran
                            </label>

                            <select name="mata_pelajaran_id" class="form-select" required>

                                <option value="">
                                    -- Pilih Mata Pelajaran --
                                </option>

                                @foreach($mapel as $m)

                                    <option value="{{ $m->id }}">

                                        {{ $m->kode_mapel }}
                                        -
                                        {{ $m->nama_mapel }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Kelas
                            </label>

                            <select name="kelas_id" class="form-select" required>

                                <option value="">
                                    -- Pilih Kelas --
                                </option>

                                @foreach($kelas as $k)

                                    <option value="{{ $k->id }}">

                                        {{ $k->nama_kelas }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit" class="btn btn-primary">

                            Simpan Penugasan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH JADWAL --}}
    {{-- ========================================================= --}}

    <div class="modal modal-blur fade" id="modalJadwal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" action="{{ route('jadwal.store') }}">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Tambah Jadwal Pelajaran
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">
                                Kelas - Mata Pelajaran - Guru
                            </label>

                            <select name="penugasan_id" class="form-select" required>

                                <option value="">
                                    -- Pilih Penugasan --
                                </option>

                                @foreach($penugasan as $p)

                                    <option value="{{ $p->id }}">

                                        {{ $p->kelas->tingkat }}
                                        -
                                        {{ $p->kelas->nama_kelas }}
                                        -
                                        {{ $p->kelas->kode_jurusan }}

                                        |

                                        {{ $p->mataPelajaran->nama_mapel }}

                                        |

                                        {{ $p->guru->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Hari
                            </label>

                            <select name="hari" class="form-select" required>

                                <option value="">
                                    -- Pilih Hari --
                                </option>

                                <option value="1">
                                    Senin
                                </option>

                                <option value="2">
                                    Selasa
                                </option>

                                <option value="3">
                                    Rabu
                                </option>

                                <option value="4">
                                    Kamis
                                </option>

                                <option value="5">
                                    Jumat
                                </option>

                            </select>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Jam Mulai
                                </label>

                                <input type="time" name="jam_mulai" class="form-control" required>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Jam Selesai
                                </label>

                                <input type="time" name="jam_selesai" class="form-control" required>

                            </div>

                        </div>


                        <hr>


                        <h4>
                            Pengaturan Absensi
                        </h4>

                        <div class="text-muted mb-3">

                            Waktu ini akan digunakan sistem untuk menentukan apakah siswa dapat absen dan apakah siswa
                            terlambat.

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Mulai Absen
                                </label>

                                <input type="time" name="jam_mulai_absen" class="form-control" required>

                                <small class="form-hint">
                                    Siswa mulai boleh melakukan absensi.
                                </small>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Batas Telat
                                </label>

                                <input type="time" name="batas_telat" class="form-control" required>

                                <small class="form-hint">
                                    Setelah waktu ini status menjadi terlambat.
                                </small>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit" class="btn btn-success">

                            Simpan Jadwal

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection

<!-- ini kode jadwal pelajaran index.blade.php -->