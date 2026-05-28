<form action="{{ url('/siswa/'.$siswa->nis.'/update') }}" method="POST" id="formsiswa">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-barcode"> <path stroke="none" d="M0 0h24v24H0z" fill="none" /> <path d="M4 7v-1a2 2 0 0 1 2 -2h2" /> <path d="M4 17v1a2 2 0 0 0 2 2h2" /> <path d="M16 4h2a2 2 0 0 1 2 2v1" /> <path d="M16 20h2a2 2 0 0 0 2 -2v-1" /> <path d="M5 11h1v2h-1l0 -2" /> <path d="M10 11l0 2" /> <path d="M14 11h1v2h-1l0 -2" /> <path d="M19 11l0 2" /> </svg>
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