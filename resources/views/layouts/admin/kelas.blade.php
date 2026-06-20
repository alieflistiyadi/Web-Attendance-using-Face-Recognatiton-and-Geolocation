@extends('layouts.admin.tabler')

@section('content')

<div class="container-xl mt-3">

    <h2>Kelas Jurusan {{ $kode }}</h2>

    <div class="row mt-3">

        @foreach($kelas as $k)
        <div class="col-md-3">
            <a href="/panel/rekap/6/2026/{{ $k->kelas }}"
               class="card card-body text-center">
                <h4>{{ $k->kelas }}</h4>
            </a>
        </div>
        @endforeach

    </div>

</div>

@endsection