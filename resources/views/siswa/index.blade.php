@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">
          Data Siswa
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
                <a href="#" class="btn btn-primary" id="btnTambahsiswa">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                  Tambah Data
                </a>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-12">
                <form action="/siswa" method="GET">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" 
                    placeholder="Nama Siswa" value="{{ Request('nama_lengkap')}}">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <select name="kode_jurusan" id="kode_jurusan" class="form-select">
                      <option value="">Jurusan</option>
                      @foreach ($jurusan as $d)
                          <option {{ Request('kode_jurusan')==$d->kode_jurusan ? 'selected' : ''}} value="{{ $d->kode_jurusan }}">{{ $d->nama_jurusan}}</option>
                      @endforeach
                    </select>
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
              <th>NIS</th>
              <th>Nama Lengkap</th>
              <th>Kelas</th>
              <th>No. Hp</th>
              <th>Foto</th>
              <th>Jurusan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($siswa as $d)
            @php
                $path = Storage::url('uploads/siswa/'.$d ->foto)
            @endphp
              <tr>
                <td>{{ $loop->iteration + $siswa->firstItem() -1}}</td>
                <td>{{ $d->nis }}</td>
                <td>{{ $d->nama_lengkap }}</td>
                <td>{{ $d->kelas }}</td>
                <td>{{ $d->no_hp }}</td>
                <td>
                  @if (empty($d->foto))
                      <img src="{{ asset('assets/img/nophoto.png')}}" class="avatar" alt="">
                      @else
                      <img src="{{ url($path)}}" class="avatar" alt="">
                  @endif
                 
                </td>
                <td>{{ $d->nama_jurusan }}</td>
                <td>
                  <div class="btn-group">
                    <a href="#" class="edit btn btn-info btn-sm" nis="{{ $d->nis }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                  </a>
                  <form action="/siswa/{{ $d->nis }}/delete" method="POST" style="margin-left: 5px"">
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
        {{ $siswa->links('vendor.pagination.bootstrap-5') }}
          </div>
              </div>
            </div>
            
        </div>
        

      </div>
    </div>
  </div>
</div>
 <div class="modal modal-blur fade" id="modal-inputsiswa" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Data Siswa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="{{ url('/siswa/store') }}" method="POST" id="formsiswa" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-12">
                  <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                  <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-id-badge-2"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 12h3v4h-3l0 -4" /><path d="M10 6h-6a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-6" /><path d="M10 4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -3" /><path d="M14 16h2" /><path d="M14 12h4" /></svg>
                                </span>
                                <input type="text" value="" id="nis" class="form-control" name="nis" placeholder="NIS">
                              </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                  <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                                </span>
                                <input type="text" value="" id="nama_lengkap" class="form-control" name="nama_lengkap" placeholder="Nama Siswa">
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
                                <input type="text" value="" id="kelas" class="form-control" name="kelas" placeholder="Kelas">
                              </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                  <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                                </span>
                                <input type="text" value="" id="no_hp" class="form-control" name="no_hp" placeholder="No HP">
                              </div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-12">
                            <input type="file" name="foto" class="form-control">
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-12">
                  <select name="kode_jurusan" id="kode_jurusan" class="form-select">
                      <option value="">Jurusan</option>
                      @foreach ($jurusan as $d)
                          <option {{ Request('kode_jurusan')==$d->kode_jurusan ? 'selected' : ''}} value="{{ $d->kode_jurusan }}">{{ $d->nama_jurusan}}</option>
                      @endforeach
                    </select>
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
   <div class="modal modal-blur fade" id="modal-editsiswa" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Data Siswa</h5>
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
        $("#btnTambahsiswa").click(function(){
            $("#modal-inputsiswa").modal("show");
        });

        $(".edit").click(function(){
          var nis = $(this).attr("nis");
          $.ajax({
            type: 'POST',
            url:'/siswa/edit',
            cache: false,
            data: {
              _token: "{{ csrf_token() }}",
              nis: nis
            },
            success: function(response){
              $("#loadeditform").html(response);
            }

          });
            $("#modal-editsiswa").modal("show");
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