@if ($presensi->isEmpty())

    <tr>

        <td colspan="10" class="text-center text-muted py-5">

            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2">

                <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" />

                <path d="M16 3v4" />

                <path d="M8 3v4" />

                <path d="M4 11h16" />

            </svg>

            <div>
                Tidak ada siswa pada kelas yang dipilih.
            </div>

        </td>

    </tr>

@else

    @foreach ($presensi as $d)

        @php

            $foto_in = null;

            if (!empty($d->foto_in)) {

                $foto_in = asset(
                    'storage/uploads/absensi/' .
                    $d->foto_in
                );

            }


            $foto_out = null;

            if (!empty($d->foto_out)) {

                $foto_out = asset(
                    'storage/uploads/absensi/' .
                    $d->foto_out
                );

            }

        @endphp


        <tr>


            {{-- NO --}}

            <td>
                {{ $loop->iteration }}
            </td>


            {{-- NIS --}}

            <td>
                {{ $d->nis }}
            </td>


            {{-- NAMA --}}

            <td>
                {{ $d->nama_lengkap }}
            </td>


            {{-- JURUSAN --}}

            <td>
                {{ $d->nama_jurusan }}
            </td>


            {{-- JAM MASUK --}}

            <td>

                @if (!empty($d->jam_in))

                    <span class="badge bg-green-lt text-green">

                        {{ $d->jam_in }}

                    </span>

                @else

                    <span class="text-muted">
                        -
                    </span>

                @endif

            </td>


            {{-- FOTO MASUK --}}

            <td>

                @if ($foto_in)

                    <a href="{{ $foto_in }}" target="_blank" title="Lihat Foto Masuk">

                        <img src="{{ $foto_in }}" class="avatar" alt="Foto Masuk" style="
                                                        width:55px;
                                                        height:55px;
                                                        object-fit:cover;
                                                    ">

                    </a>

                @else

                    <span class="text-muted">
                        -
                    </span>

                @endif

            </td>


            {{-- JAM PULANG --}}

            <td>

                @if (!empty($d->jam_out))

                    <span class="badge bg-blue-lt text-blue">

                        {{ $d->jam_out }}

                    </span>

                @elseif (!empty($d->jam_in))

                    <span class="badge bg-danger-lt text-danger">

                        Belum Absen Pulang

                    </span>

                @else

                    <span class="text-muted">
                        -
                    </span>

                @endif

            </td>


            {{-- FOTO PULANG --}}

            <td>

                @if ($foto_out)

                    <a href="{{ $foto_out }}" target="_blank" title="Lihat Foto Pulang">

                        <img src="{{ $foto_out }}" class="avatar" alt="Foto Pulang" style="
                                                        width:55px;
                                                        height:55px;
                                                        object-fit:cover;
                                                    ">

                    </a>

                @else

                    <span class="text-muted">
                        -
                    </span>

                @endif

            </td>


            {{-- KETERANGAN --}}

            <td>

                @if ($d->status === 'Tepat Waktu')

                    <span class="badge bg-success">

                        Tepat Waktu

                    </span>


                @elseif ($d->status === 'Belum Absen')

                    <span class="badge bg-danger">

                        Belum Absen

                    </span>


                @elseif ($d->terlambat > 0)

                    <span class="badge bg-warning text-dark">

                        Terlambat
                        {{ $d->terlambat }}
                        Menit

                    </span>


                @else

                    <span class="badge bg-danger">

                        {{ $d->status ?? 'Belum Absen' }}

                    </span>

                @endif

            </td>


            {{-- AKSI --}}

            <td>

                @if (!empty($d->id))

                    <button type="button" class="btn btn-sm btn-primary" onclick="tampilkanPeta({{ $d->id }})">

                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                            <path d="M12 18.5l-3 -1.5l-6 3v-13l6 -3l6 3l6 -3v7.5" />

                            <path d="M9 4v13" />

                            <path d="M15 7v5.5" />

                            <path
                                d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />

                            <path d="M19 18v.01" />

                        </svg>

                        Lokasi

                    </button>

                @else

                    <span class="text-muted">
                        -
                    </span>

                @endif

            </td>


        </tr>

    @endforeach

@endif