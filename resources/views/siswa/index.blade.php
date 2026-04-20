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
                $path = Storage::url('uploads/absensi/'.$d ->foto)
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
                <td></td>
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

@endsection