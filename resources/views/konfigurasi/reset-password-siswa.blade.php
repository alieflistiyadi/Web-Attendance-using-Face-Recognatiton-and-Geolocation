@extends('layouts.admin.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Ubah Password Siswa
                    </h2>
                    <div class="text-muted mt-1">
                        Daftar permintaan reset password dari siswa
                    </div>
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

                            @if (Session::get('success'))
                                <div class="alert alert-success">
                                    {{ Session::get('success') }}
                                </div>
                            @endif

                            @if (Session::get('warning'))
                                <div class="alert alert-warning">
                                    {{ Session::get('warning') }}
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>NIS</th>
                                            <th>Nama</th>
                                            <th>No HP</th>
                                            <th>Status</th>
                                            <th>Tanggal Ajuan</th>
                                            <th class="w-1">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($requests as $req)
                                            <tr>
                                                <td>{{ $req->nis }}</td>
                                                <td>{{ $req->siswa->nama_lengkap ?? '-' }}</td>
                                                <td>{{ $req->no_hp }}</td>
                                                <td>
                                                    @if ($req->status == 'pending')
                                                        <span class="badge bg-yellow-lt">Menunggu</span>
                                                    @elseif ($req->status == 'approved')
                                                        <span class="badge bg-green-lt">Disetujui</span>
                                                    @else
                                                        <span class="badge bg-red-lt">Ditolak</span>
                                                    @endif
                                                </td>
                                                <td>{{ $req->created_at->format('d-m-Y H:i') }}</td>
                                                <td>
                                                    @if ($req->status == 'pending')
                                                        <div class="btn-list flex-nowrap">
                                                            <form action="{{ route('admin.resetpassword.approve', $req->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success"
                                                                    onclick="return confirm('Reset password siswa ini ke password default?')">
                                                                    Approve
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('admin.resetpassword.reject', $req->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Tolak permintaan ini?')">
                                                                    Tolak
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Belum ada permintaan reset password
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

@endsection