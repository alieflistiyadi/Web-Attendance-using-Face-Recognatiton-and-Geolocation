@extends('layouts.admin.tabler')

@section('content')

<div class="container-xl mt-3">

    <h2>Dashboard Jurusan</h2>

    <div class="row mt-3">

        <div class="col-md-4">
            <a href="/panel/jurusan/TJKT" class="card card-body text-center">
                <h3>TKJT</h3>
                <p>Teknik Jaringan Komputer & Telekomunikasi</p>
            </a>
        </div>

        <div class="col-md-4">
            <a href="/panel/jurusan/MP" class="card card-body text-center">
                <h3>MP</h3>
                <p>Manajemen Perkantoran</p>
            </a>
        </div>

        <div class="col-md-4">
            <a href="/panel/jurusan/TM" class="card card-body text-center">
                <h3>TM</h3>
                <p>Teknik Mesin</p>
            </a>
        </div>

    </div>

</div>

@endsection