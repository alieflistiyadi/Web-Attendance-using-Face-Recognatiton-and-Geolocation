@extends('layouts.admin.tabler')

@section('content')
<div class="container-xl mt-3">

    <h2>Data Siswa</h2>

    <div class="row mt-3">

        @foreach($jurusan as $j)
        <div class="col-md-4">
            <a href="/siswa/{{ $j->kode_jurusan }}"
               class="card card-body text-center text-decoration-none">

                <h3>{{ $j->kode_jurusan }}</h3>
                <p>{{ $j->nama_jurusan }}</p>

            </a>
        </div>
        @endforeach

    </div>

</div>
@endsection