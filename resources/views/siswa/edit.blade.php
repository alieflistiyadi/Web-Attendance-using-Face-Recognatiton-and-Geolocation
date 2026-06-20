<form action="{{ url('/siswa/'.$siswa->nis.'/update') }}" method="POST" id="formsiswa">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-id-badge-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 12h3v4h-3l0 -4" />
                        <path d="M10 6h-6a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-6" />
                        <path d="M10 4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -3" />
                        <path d="M14 16h2" />
                        <path d="M14 12h4" />
                    </svg>
                </span>

                <input type="text"
                    readonly
                    value="{{ $siswa->nis }}"
                    id="nis"
                    class="form-control"
                    name="nis"
                    placeholder="NIS">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user"> <path stroke="none" d="M0 0h24v24H0z" fill="none" /> <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /> <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /> </svg>
                </span>

                <input type="text"
                    value="{{ $siswa->nama_lengkap }}"
                    id="nama_lengkap"
                    class="form-control"
                    name="nama_lengkap"
                    placeholder="Nama Lengkap">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-briefcase-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9" />
                        <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                </span>

                <input type="text"
                    value="{{ $siswa->kelas }}"
                    id="kelas"
                    class="form-control"
                    name="kelas"
                    placeholder="Kelas">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                    </svg>
                </span>

                <input type="text"
                    value="{{ $siswa->no_hp }}"
                    id="no_hp"
                    class="form-control"
                    name="no_hp"
                    placeholder="Nomor HP">
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-12">
        <div class="mb-3">
            <label class="form-label">Jurusan</label>

            <select name="kode_jurusan" id="kode_jurusan" class="form-select">
                <option value="">Pilih Jurusan</option>

                @foreach ($jurusan as $d)
                    <option value="{{ $d->kode_jurusan }}"
                        {{ $siswa->kode_jurusan == $d->kode_jurusan ? 'selected' : '' }}>
                        {{ $d->nama_jurusan }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

    <div class="row mt-2">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>