@if ($histori->isEmpty())
    <div class="alert alert-danger mt-2">
        <h4>Data tidak ditemukan</h4>
    </div>
@else
    <div class="mt-2">

        @foreach ($histori as $d)


            {{-- PRESENSI --}}
            @if($d->tipe == 'presensi')

                @php


                    $batas = strtotime('07:00:00');
                    $jamMasuk = strtotime($d->jam_in);

                    if ($jamMasuk <= $batas) {
                        $status = "Tepat Waktu";
                        $class = "status-success";
                    } else {
                        $selisih = floor(($jamMasuk - $batas) / 60);
                        $status = "Terlambat " . $selisih . " Menit";
                        $class = "status-warning";
                    }
                @endphp

                <div class="history-card">

                    <div class="history-header">

                        <img src="{{ asset('uploads/absensi/' . $d->foto_in) }}" class="history-photo">

                        <div class="history-date">
                            {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}
                        </div>

                    </div>

                    <div class="history-info">

                        <div class="info-box">
                            <div class="info-title">Jam Masuk</div>
                            <div class="info-value">{{ $d->jam_in }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-title">Jam Pulang</div>
                            <div class="info-value">
                                {{ $d->jam_out ?? '-' }}
                            </div>
                        </div>

                    </div>

                    <div class="status-box">
                        <span class="status-text {{ $class }}">
                            {{ $status }}
                        </span>
                    </div>

                </div>

                {{-- ALPA --}}
            @elseif($d->tipe == 'alpa')

                <div class="history-card">

                    <div class="history-header">

                        <div class="history-icon status-danger">
                            <ion-icon name="close-circle-outline"></ion-icon>
                        </div>

                        <div class="history-date">
                            {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}
                        </div>

                    </div>

                    <div class="history-note">
                        Tidak melakukan presensi dan tidak memiliki izin/sakit yang disetujui.
                    </div>

                    <div class="status-box">
                        <span class="status-text status-danger">
                            Alpa
                        </span>
                    </div>

                </div>



                {{-- IZIN --}}
            @elseif($d->tipe == 'izin')

                <div class="history-card">

                    <div class="history-header">

                        <div class="history-icon status-izin">
                            <ion-icon name="document-text-outline"></ion-icon>
                        </div>

                        <div class="history-date">
                            {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}
                        </div>

                    </div>

                    <div class="history-note">
                        Tidak melakukan presensi karena pengajuan izin telah disetujui.
                    </div>

                    <div class="status-box">
                        <span class="status-text status-izin">
                            Izin
                        </span>
                    </div>

                </div>

                {{-- SAKIT --}}
            @elseif($d->tipe == 'sakit')

                <div class="history-card">

                    <div class="history-header">

                        <div class="history-icon status-sakit">
                            <ion-icon name="medkit-outline"></ion-icon>
                        </div>

                        <div class="history-date">
                            {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}
                        </div>

                    </div>

                    <div class="history-note">
                        Tidak melakukan presensi karena pengajuan sakit telah disetujui.
                    </div>

                    <div class="status-box">
                        <span class="status-text status-sakit">
                            Sakit
                        </span>
                    </div>

                </div>

            @endif

        @endforeach

    </div>

@endif

ini kode gethistori.blade.php