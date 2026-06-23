@extends('layouts.admin.tabler')

@section('content')

<div class="page-header">
    <div class="container-xl">
        <h2 class="page-title">
            Pilih Jurusan
        </h2>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <div class="row">

            @foreach($jurusan as $j)

            <div class="col-md-3 mb-3">
                <a href="/attendance/izinsakit/{{ $j->kode_jurusan }}"
                    class="card text-center text-decoration-none">

                    <div class="card-body">
                        <h3>{{ $j->kode_jurusan }}</h3>
                        <p>{{ $j->nama_jurusan }}</p>
                    </div>

                </a>
            </div>

            @endforeach

        </div>

    </div>
</div>

@endsection