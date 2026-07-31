@extends('layouts.admin.tabler')

@section('content')

<div class="page-body">
    <div class="container-xl">

        <div class="card">

            <div class="card-header">
                <h3>Data Guru</h3>
            </div>

            <div class="card-body">
                <a href="#" class="btn btn-primary mb-3" id="btnTambahGuru">
                    Tambah Guru
                </a>

                <table class="table table-bordered">

                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th width="120">Aksi</th>
                    </tr>

                    @foreach($guru as $g)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $g->name }}</td>
                        <td>{{ $g->email }}</td>

                        <td>

                            <div class="btn-group">

                                <a href="#"
                                    class="btn btn-info btn-sm edit"
                                    id="{{ $g->id }}"
                                    style="width:38px;height:31px;">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415"/>
                                    </svg>

                                </a>

                                <form action="/guru/{{ $g->id }}/delete"
                                    method="POST"
                                    style="margin-left:5px">

                                    @csrf

                                    <button
                                        class="btn btn-danger btn-sm delete-confirm">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2">

                                            <path d="M4 7l16 0"/>
                                            <path d="M10 11l0 6"/>
                                            <path d="M14 11l0 6"/>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                        </svg>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @endforeach

                </table>

            </div>

        </div>

    </div>
</div>

@endsection

<div class="modal fade" id="modal-inputguru">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h3>Tambah Guru</h3>
            </div>

            <div class="modal-body">

                <form action="/guru/store" method="POST" id="formguru">

                    @csrf

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text"
                               name="name"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email"
                               name="email"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password"
                               name="password"
                               class="form-control">
                    </div>

                    <button class="btn btn-primary">
                        Simpan
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>

<div class="modal fade" id="modal-editguru">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h3>Edit Guru</h3>
            </div>

            <div class="modal-body" id="loadeditform">

            </div>

        </div>

    </div>
</div>

@push('myscript')

@if ($errors->any())
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '{{ $errors->first() }}'
});
</script>
@endif

@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '{{ session("success") }}'
});
</script>
@endif


<script>
$(function () {

    // Tambah Guru
    $("#btnTambahGuru").click(function () {
        $("#modal-inputguru").modal("show");
    });

    // Validasi Tambah Guru
    $("#formguru").submit(function () {

        var password = $("input[name='password']").val();

        var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/;

        if (!regex.test(password)) {

            Swal.fire({
                title: 'Password Tidak Valid',
                text: 'Password minimal 8 karakter dan harus mengandung huruf besar, huruf kecil, angka, serta simbol.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });

            return false;
        }

    });

    // Edit Guru
    $(".edit").click(function () {

        var id = $(this).attr("id");

        $.ajax({
            type: 'POST',
            url: '/guru/edit',
            cache: false,
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function(response) {
                $("#loadeditform").html(response);
            }
        });

        $("#modal-editguru").modal("show");
    });

    // ✅ Validasi Edit Guru
    $(document).on("submit", "#formeditguru", function () {

        var password = $("#password").val();

        // Kalau password kosong, biarkan submit
        if(password == ""){
            return true;
        }

        var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/;

        if(!regex.test(password)){

            Swal.fire({
                title: 'Password Tidak Valid',
                text: 'Password minimal 8 karakter dan harus mengandung huruf besar, huruf kecil, angka, serta simbol.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });

            return false;
        }

    });

    // Delete Guru
    $(".delete-confirm").click(function(e){

        e.preventDefault();

        var form = $(this).closest("form");

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data guru akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                form.submit();
            }
        });

    });

});
</script>
@endpush