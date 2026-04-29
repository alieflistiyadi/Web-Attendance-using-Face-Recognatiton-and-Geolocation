<form action="/jurusan/{{ $jurusan->kode_jurusan }}/update" method="POST">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-id-badge-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 12h3v4h-3l0 -4" />
                        <path d="M10 6h-6a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-6" />
                    </svg>
                </span>

                <input type="text"
                    id="kode_jurusan"
                    class="form-control"
                    name="kode_jurusan"
                    value="{{ $jurusan->kode_jurusan }}"
                    readonly>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-briefcase-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9" />
                    </svg>
                </span>

                <input type="text"
                    id="nama_jurusan"
                    class="form-control"
                    name="nama_jurusan"
                    value="{{ $jurusan->nama_jurusan }}">
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <button class="btn btn-primary w-100">
                Update
            </button>
        </div>
    </div>
</form>