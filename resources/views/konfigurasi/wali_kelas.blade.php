@extends('layouts.admin.tabler')

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Konfigurasi Wali Kelas
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <div class="row justify-content-center">
            <div class="col-xl-11">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">
                            Pengaturan Wali Kelas
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- SUCCESS --}}
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- WARNING --}}
                        @if(session('warning'))
                            <div class="alert alert-warning">
                                {{ session('warning') }}
                            </div>
                        @endif


                        {{-- TABLE --}}
                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle">

                                <thead class="table-light">

                                    <tr class="text-center">

                                        <th width="60">
                                            No
                                        </th>

                                        <th>
                                            Kelas
                                        </th>

                                        <th>
                                            Jurusan
                                        </th>

                                        <th>
                                            Wali Kelas
                                        </th>

                                        <th width="120">
                                            Aksi
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($kelas as $item)

                                        <tr>

                                            {{-- NO --}}
                                            <td class="text-center">
                                                {{ $loop->iteration }}
                                            </td>


                                            {{-- KELAS --}}
                                            <td>
                                                <strong>
                                                    {{ $item->nama_kelas }}
                                                </strong>
                                            </td>


                                            {{-- JURUSAN --}}
                                            <td>
                                                {{ $item->kode_jurusan }}
                                            </td>


                                            {{-- WALI KELAS --}}
                                            <td>

                                                @if($item->nama_wali_kelas)

                                                    <span class="text-dark">
                                                        {{ $item->nama_wali_kelas }}
                                                    </span>

                                                @else

                                                    <span class="text-muted">
                                                        Belum ditentukan
                                                    </span>

                                                @endif

                                            </td>


                                            {{-- AKSI --}}
                                            <td class="text-center">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalWaliKelas{{ $item->id }}"
                                                >

                                                    @if($item->wali_kelas_id)
                                                        <i class="fas fa-edit"></i>
                                                        Ubah
                                                    @else
                                                        <i class="fas fa-plus"></i>
                                                        Pilih
                                                    @endif

                                                </button>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td
                                                colspan="5"
                                                class="text-center text-muted"
                                            >
                                                Belum ada data kelas.

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
{{-- MODAL WALI KELAS --}}
{{-- ========================================================= --}}

@foreach($kelas as $item)

<div
    class="modal fade"
    id="modalWaliKelas{{ $item->id }}"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                action="{{ route('konfigurasi.update_wali_kelas') }}"
                method="POST"
            >

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        Atur Wali Kelas
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <div class="modal-body">

                    {{-- KELAS --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Kelas
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $item->nama_kelas }}"
                            readonly
                        >

                        <input
                            type="hidden"
                            name="kelas_id"
                            value="{{ $item->id }}"
                        >

                    </div>


                    {{-- WALI KELAS --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Wali Kelas
                        </label>

                        <select
                            name="wali_kelas_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Guru --
                            </option>

                            @foreach($guru as $g)

                                <option
                                    value="{{ $g->id }}"
                                    {{ $item->wali_kelas_id == $g->id ? 'selected' : '' }}
                                >
                                    {{ $g->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endforeach

@endsection