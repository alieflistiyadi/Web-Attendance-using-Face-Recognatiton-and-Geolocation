@if ($histori->isEmpty())
    <div class="alert alert-danger mt-2">
        <h4>Data tidak ditemukan</h4>
    </div>
@else
    <ul class="listview image-listview mt-2">
        @foreach ($histori as $d)
            <li>
                <div class="item">
                    @php
                        $path = Storage::url('uploads/absensi/' . $d->foto_in);
                    @endphp
                    <img src="{{ url($path) }}" alt="image" class="image">
                    <div class="in">
                        <div>
                            <b>{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</b>
                        </div>
                        <span class="badge {{ $d->jam_in < '07:00' ? 'bg-success' : 'bg-danger' }}">
                            {{ $d->jam_in }}
                        </span>
                        <span class="badge bg-primary">{{ $d->jam_out ?? 'Belum Pulang' }}</span>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endif