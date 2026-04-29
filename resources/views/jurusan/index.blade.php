@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">
          Data Jurusan
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
            <div class="row">
              <div class="col-12">
                @if (Session::get('success'))
                  <div class="alert alert-success">
                    {{ Session::get('success')}}
                  </div>
                @endif

                @if (Session::get('warning'))
                  <div class="alert alert-warning">
                    {{ Session::get('warning')}}
                  </div>
                @endif
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <a href="#" class="btn btn-primary" id="btnTambahjurusan">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                  Tambah Data
                </a>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-12">
                <form action="/jurusan" method="GET">
              <div class="row">
                <div class="col-10">
                  <div class="form-group">
                    <input type="text" name="nama_jurusan" id="nama_jurusan" class="form-control" 
                    placeholder="Jurusan" value="{{ Request('nama_jurusan')}}">
                  </div>
                </div>
                <div class="col-2">
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                   Cari
                    </button>
                  </div>
                </div>
              </div>
            </form>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-12">
                <table class="table table-bordered">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Jurusan</th>
              <th>Nama Jurusan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($jurusan as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->kode_jurusan }}</td>
                    <td>{{ $d->nama_jurusan }}</td>
                    <td>
                        <div class="btn-group">
                    <a href="#" class="edit btn btn-info btn-sm" kode_jurusan="{{ $d->kode_jurusan }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                  </a>
                  <form action="/jurusan/{{ $d->kode_jurusan }}/delete" method="POST" style="margin-left: 5px"">
                    @csrf

                    <button type="submit" class="btn btn-danger btn-sm delete-confirm">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </button>
                  </form>
                  </div>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
        
          </div>
              </div>
            </div>
            
        </div>
        

      </div>
    </div>
  </div>
</div>
 <div class="modal modal-blur fade" id="modal-inputjurusan" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Data Jurusan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="{{ url('/jurusan/store') }}" method="POST" id="formjurusan">
              @csrf
              <div class="row">
                <div class="col-12">
                  <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                  <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-id-badge-2"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 12h3v4h-3l0 -4" /><path d="M10 6h-6a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-6" /><path d="M10 4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -3" /><path d="M14 16h2" /><path d="M14 12h4" /></svg>
                                </span>
                                <input type="text" value="" id="kode_jurusan" class="form-control" name="kode_jurusan" placeholder="Kode Jurusan">
                              </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                  <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-briefcase-2"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /></svg>
                                </span>
                                <input type="text" value="" id="nama_jurusan" class="form-control" name="nama_jurusan" placeholder="Nama Jurusan">
                              </div>
                </div>
              </div>
              
              <div class="row mt-2">
            <div class="col-12">
              <div class="form-group">
                <button class="btn btn-primary w-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 14l11 -11" /><path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" /></svg>
                  Simpan
                </button>
              </div>
            </div>
          </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal edit --}}
   <div class="modal modal-blur fade" id="modal-editjurusan" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Data Jurusan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="loadeditform">
            
          </div>
        </div>
      </div>
    </div> 
@endsection

@push('myscript')
  <script>
    $(function(){
        $("#btnTambahjurusan").click(function(){
            $("#modal-inputjurusan").modal("show");
        });

        $(".edit").click(function(){
          var kode_jurusan = $(this).attr("kode_jurusan");
          $.ajax({
            type: 'POST',
            url:'/jurusan/edit',
            cache: false,
            data: {
              _token: "{{ csrf_token() }}",
              kode_jurusan: kode_jurusan
            },
            success: function(response){
              $("#loadeditform").html(response);
            }

          });
            $("#modal-editjurusan").modal("show");
        });

        $(".delete-confirm").click(function(e){
          e.preventDefault();
          var form = $(this).closest("form");
          Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();
              Swal.fire(
                'Terhapus!',
                'Data siswa telah dihapus.',
                'success'
              )
            }
          });
        });

        $("#formsiswa").submit(function(){
          var nis = $("#formsiswa").find("#nis").val();
          var nama_lengkap = $("#formsiswa").find("#nama_lengkap").val();
          var kelas = $("#formsiswa").find("#kelas").val();
          var no_hp = $("#formsiswa").find("#no_hp").val();
          var kode_jurusan = $("#formsiswa").find("#kode_jurusan").val();
            if(nis==""){
              //alert('NIS Harus Diisi');
              Swal.fire({
                title: 'Warning!',
                text: 'NIS Harus Diisi',
                icon: 'warning',
                confirmButtonText: 'OK'
              }).then((result)=> {
                $("#nis").focus();
              });
              
              return false;
            }else if(nama_lengkap == ""){
              //alert('Nama Lengkap Harus Diisi');
              Swal.fire({
                title: 'Warning!',
                text: 'Nama Lengkap Harus Diisi',
                icon: 'warning',
                confirmButtonText: 'OK'
              }).then((result) => {
                $("#nama_lengkap").focus();
              });
              return false;
              
            }else if(kelas == ""){
              //alert('Jurusan Harus Diisi');
              Swal.fire({
                title: 'Warning!',
                text: 'Kelas Harus Diisi',
                icon: 'warning',
                confirmButtonText: 'OK'
              }).then((result)=> {
                $("#kelas").focus();
              });
              return false;

            }else if(no_hp == ""){
              //alert('No HP Harus Diisi');
              Swal.fire({
                title: 'Warning!',
                text: 'No HP Harus Diisi',
                icon: 'warning',
                confirmButtonText: 'OK'
              }).then((result)=> {
                $("#no_hp").focus();
              });
              return false;

            }else if(kode_jurusan == ""){
              //alert('Jurusan Harus Diisi');
              Swal.fire({
                title: 'Warning!',
                text: 'Jurusan Harus Dipilih',
                icon: 'warning',
                confirmButtonText: 'OK'
              }).then((result)=> {
                $("#kode_jurusan").focus();
              });
              return false;

            }
        });
    });
  </script>
@endpush