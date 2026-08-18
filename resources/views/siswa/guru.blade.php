@extends('layouts.admin.tabler')

@section('content')

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row align-items-center">

                <div class="col">
                    <h2 class="page-title">
                        Data Siswa
                    </h2>

                    <div class="text-muted mt-1">
                        Pilih kelas dan mata pelajaran yang Anda ajar.
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="page-body">
        <div class="container-xl">

            @if($kelasList->isEmpty())

                <div class="card">
                    <div class="card-body text-center py-5">

                        <div class="text-muted">
                            Anda belum memiliki penugasan kelas dan
                            mata pelajaran.
                        </div>

                    </div>
                </div>

            @else

                @foreach($kelasList as $kelas)

                    <div class="card mb-3">

                        <div class="card-header">

                            <h3 class="card-title">

                                {{ $kelas->nama_kelas }}

                            </h3>

                        </div>


                        <div class="card-body">

                            <div class="row">

                                @foreach(
                                                $penugasanByKelas[$kelas->kelas_id] ?? []
                                                as $penugasan
                                            )

                                            <div class="col-md-4 mb-3">

                                                <a href="{{ route(
                                        'siswa.guru.kelas',
                                        [
                                            'kelasId' => $kelas->kelas_id,
                                            'penugasanId' => $penugasan['penugasan_id']
                                        ]
                                    ) }}" class="card card-link card-link-pop h-100">

                                                    <div class="card-body">

                                                        <div class="d-flex align-items-center">

                                                            <span class="avatar bg-primary text-white me-3">

                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                                                    <path d="M12 3l8 4l-8 4l-8 -4l8 -4" />

                                                                    <path d="M4 10v6c0 2 4 4 8 4s8 -2 8 -4v-6" />
                                                                </svg>

                                                            </span>


                                                            <div>

                                                                <div class="fw-bold">
                                                                    {{ $penugasan['nama_mapel'] }}
                                                                </div>

                                                                <div class="text-muted">
                                                                    {{ $kelas->nama_kelas }}
                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </a>

                                            </div>

                                @endforeach

                            </div>

                        </div>

                    </div>

                @endforeach

            @endif

        </div>
    </div>

@endsection