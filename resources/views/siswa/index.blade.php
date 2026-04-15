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
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->nis }}</td>
                <td>{{ $d->nama_lengkap }}</td>
                <td>{{ $d->kelas }}</td>
                <td>{{ $d->no_hp }}</td>
                <td></td>
                <td>{{ $d->nama_jurusan }}</td>
                <td></td>
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

@endsection