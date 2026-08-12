@extends('layouts.admin.tabler')

@section('content')

<div class="page-body">
    <div class="container-xl">

        <div class="card">

            <div class="card-header">
                <h3>Permintaan Reset Password</h3>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Tanggal Permintaan</th>
                        <th width="140">Aksi</th>
                    </tr>

                    @forelse($requests as $r)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $r->name }}</td>
                        <td>{{ $r->email }}</td>
                        <td>
                            @if($r->role === 'superadmin')
                            <span class="badge bg-purple text-white">Superadmin</span>
                            @else
                            <span class="badge bg-blue text-white">Guru</span>
                            @endif
                        </td>
                        <td>
                            @if($r->status === 'pending')
                            <span class="badge bg-yellow text-dark">Menunggu</span>
                            @else
                            <span class="badge bg-green text-white">Disetujui</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}</td>
                        <td>
                            @   if($r->status === 'pending')
                            <form action="{{ url('/panel/reset-requests/' . $r->id . '/approve') }}" method="POST"
                                class="approve-confirm">
                                @csrf
                                <button class="btn btn-success btn-sm">
                                    Setujui
                                </button>
                            </form>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada permintaan reset password</td>
                    </tr>

                    @endforelse

                </table>

                {{ $requests->links() }}

            </div>

        </div>

    </div>
</div>

@endsection

@push('myscript')

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session("success") }}'
    });
</script>
@endif

@if(session('warning'))
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Perhatian',
        text: '{{ session("warning") }}'
    });
</script>
@endif

<script>
    $(function () {
        $(".approve-confirm").submit(function (e) {
            e.preventDefault();

            var form = this;

            Swal.fire({
                title: 'Setujui permintaan ini?',
                text: "Password akan direset ke default dan guru wajib menggantinya saat login.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush