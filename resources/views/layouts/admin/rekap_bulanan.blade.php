@extends('layouts.admin.tabler')

@section('content')

<div class="container-xl mt-3">

    <h2>Rekap Kelas {{ $kelas }} ({{ $bulan }}/{{ $tahun }})</h2>

    <div class="card mb-3">
        <div class="card-body">

            <div class="row g-3 align-items-end">

                {{-- KELAS --}}
                <div class="col-md-4">
                    <label class="form-label">📚 Kelas</label>
                    <select class="form-select"
                        onchange="window.location.href='/panel/rekap/{{ $kode }}/'+this.value+'/{{ $bulan }}/{{ $tahun }}'">

                        @foreach($listKelas as $k)
                            <option value="{{ $k }}" {{ $k == $kelas ? 'selected' : '' }}>
                                Kelas {{ $k }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- BULAN --}}
                <div class="col-md-4">
                    <label class="form-label">📅 Bulan</label>
                    <select class="form-select"
                        onchange="window.location.href='/panel/rekap/{{ $kode }}/{{ $kelas }}/'+this.value+'/{{ $tahun }}'">

                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == $bulan ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor

                    </select>
                </div>

                {{-- TAHUN --}}
                <div class="col-md-4">
                    <label class="form-label">📆 Tahun</label>
                    <select class="form-select"
                        onchange="window.location.href='/panel/rekap/{{ $kode }}/{{ $kelas }}/{{ $bulan }}/'+this.value">

                        @for ($i = date('Y') - 2; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $i == $tahun ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor

                    </select>
                </div>

            </div>

        </div>
    </div>

    <div class="table-responsive mt-3">

        <table class="table table-bordered text-center">

            <thead>
                <tr>
                    <th>Nama</th>

                    @for($i=1; $i<=31; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
            </thead>

            <tbody>

                @foreach($data as $d)
                    <tr>
                        <td>{{ $d['nama'] }}</td>

                        @for($i=1; $i<=31; $i++)

                            @php
                                $tanggalSekarang = date('Y-m-d');
                                $tanggalLoop = $tahun.'-'.str_pad($bulan,2,'0',STR_PAD_LEFT).'-'.str_pad($i,2,'0',STR_PAD_LEFT);

                                $belumTerjadi = $tanggalLoop > $tanggalSekarang;

                                $val = $d[$i] ?? '';
                            @endphp

                            <td
                                @if($belumTerjadi)
                                    style="background:#f8f9fa;"
                                @else
                                    style="
                                        color:white;
                                        background:
                                        {{ $val == 'H' ? '#22c55e' :
                                        ($val == 'I' ? '#3b82f6' :
                                        ($val == 'S' ? '#f59e0b' :
                                        ($val == 'A' ? '#ef4444' : '#fff'))) }};
                                    "
                                @endif
                            >
                                @if($belumTerjadi)
                                    <span class="text-muted">-</span>
                                @else
                                    {{ $val }}
                                @endif
                            </td>

                        @endfor

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection