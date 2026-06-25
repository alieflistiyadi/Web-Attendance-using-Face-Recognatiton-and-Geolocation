@extends('layouts.admin.tabler')

@section('content')
<div class="container-xl mt-3">

    <h2>Jurusan {{ $kode_jurusan }}</h2>

    <div class="row mt-3">

        @foreach($kelas as $k)
        <div class="col-md-4">
            <a href="/siswa/{{ $kode_jurusan }}/{{ $k->kelas }}"
               class="card card-body text-center text-decoration-none">

                <h3>Kelas {{ $k->kelas }}</h3>
                <p>{{ $k->jumlah }} siswa</p>

            </a>
        </div>
        @endforeach

    </div>

</div>
@endsection