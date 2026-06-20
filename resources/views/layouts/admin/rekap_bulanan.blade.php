@extends('layouts.admin.tabler')

@section('content')

<div class="container-xl mt-3">

    <h2>Rekap Kelas {{ $kelas }} ({{ $bulan }}/{{ $tahun }})</h2>

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
                            @php $val = $d[$i] ?? ''; @endphp

                            <td
                                style="
                                    color:white;
                                    background:
                                    {{ $val == 'H' ? '#22c55e' :
                                       ($val == 'I' ? '#3b82f6' :
                                       ($val == 'S' ? '#f59e0b' :
                                       ($val == 'A' ? '#ef4444' : '#fff'))) }};
                                "
                            >
                                {{ $val }}
                            </td>

                        @endfor
                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection