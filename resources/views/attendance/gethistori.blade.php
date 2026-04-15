@if ($histori->isEmpety())
<div>
    <div class="alert alert-danger">
        <h4>Data tidak ditemukan</h4>
    </div>
</div>

@endif

@foreach ($histori as $d)
    <ui class="listview image-listview">
        <li>
            <div class="item">
                @php
                    $path = Storage::url('uploads/absensi/' . $d->foto_in);
                @endphp
                <img src="{{ url($path) }}" alt="image" class="image">
                <div class="in">
                    <div>
                        <b>{{ date("d-m-y", strtotime($d->tgl_presensi)) }}</b><br>
                        {{-- <small class="text-muted"> {{ $d->jabatan }} </small>--}}
                    </div>
                    <span class="badge">{{ $d->jam_in < "07:00" ? "bg-success" : "bg-danger" }}>
                        {{ $d->jam_in }}
                    </span>
                    <span class="badge bg-primary">{{ $d->jam_out }}</span>
                </div>
            </div>
        </li>
    </ui>
@endforeach