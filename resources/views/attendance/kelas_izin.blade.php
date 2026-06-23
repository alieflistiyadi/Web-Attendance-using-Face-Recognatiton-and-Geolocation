@extends('layouts.admin.tabler')

@section('content')

<div class="page-header">
    <div class="container-xl">
        <h2 class="page-title">
            Pilih Kelas
        </h2>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <div class="row">

            @foreach($kelas as $k)

            <div class="col-md-3 mb-3">

                <a href="/attendance/izinsakit/{{ $kode_jurusan }}/{{ $k->kelas }}"
                    class="card text-center text-decoration-none">

                    <div class="card-body">
                        <h3>{{ $k->kelas }}</h3>
                    </div>

                </a>

            </div>

            @endforeach

        </div>

    </div>
</div>

@endsection