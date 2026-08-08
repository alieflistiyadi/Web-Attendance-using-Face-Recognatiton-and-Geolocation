<form action="{{ url('/siswa/' . $siswa->nis . '/update') }}" method="POST" id="formsiswa">
    @csrf
    <input type="hidden" name="redirect_kelas" value="{{ $redirect_kelas ?? '' }}">
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-id-badge-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 12h3v4h-3l0 -4" />
                        <path d="M10 6h-6a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-6" />
                        <path d="M10 4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -3" />
                        <path d="M14 16h2" />
                        <path d="M14 12h4" />
                    </svg>
                </span>
                <style>
                    #kelas {
                        padding-left: 40px;
                        height: 38px;
                    }
                </style>
                <input type="text" readonly value="{{ $siswa->nis }}" id="nis" class="form-control" name="nis"
                    placeholder="NIS">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                </span>

                <input type="text" value="{{ $siswa->nama_lengkap }}" id="nama_lengkap" class="form-control"
                    name="nama_lengkap" placeholder="Nama Lengkap">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <label class="form-label">Kelas</label>
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-briefcase-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9" />
                            <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                        </svg>
                    </span>
                <select name="kelas" id="kelas" class="form-select">
                    <option value="">Pilih Kelas</option>
                    <optgroup label="Kelas 10">
                        <option value="10-1"
                            {{ $siswa->kelas == '10-1' ? 'selected' : '' }}>
                            Kelas 10-1
                        </option>
                        <option value="10-2"
                            {{ $siswa->kelas == '10-2' ? 'selected' : '' }}>
                            Kelas 10-2
                        </option>
                        <option value="10-3"
                            {{ $siswa->kelas == '10-3' ? 'selected' : '' }}>
                            Kelas 10-3
                        </option>
                    </optgroup>
                    <optgroup label="Kelas 11">
                        <option value="11-1"
                            {{ $siswa->kelas == '11-1' ? 'selected' : '' }}>
                            Kelas 11-1
                        </option>
                        <option value="11-2"
                            {{ $siswa->kelas == '11-2' ? 'selected' : '' }}>
                            Kelas 11-2
                        </option>
                        <option value="11-3"
                            {{ $siswa->kelas == '11-3' ? 'selected' : '' }}>
                            Kelas 11-3
                        </option>
                    </optgroup>
                    <optgroup label="Kelas 12">
                        <option value="12-1"
                            {{ $siswa->kelas == '12-1' ? 'selected' : '' }}>
                            Kelas 12-1
                        </option>
                        <option value="12-2"
                            {{ $siswa->kelas == '12-2' ? 'selected' : '' }}>
                            Kelas 12-2
                        </option>
                        <option value="12-3"
                            {{ $siswa->kelas == '12-3' ? 'selected' : '' }}>
                            Kelas 12-3
                        </option>
                    </optgroup>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                    </svg>
                </span>

                <input type="text"
                value="{{ $siswa->no_hp }}"
                id="no_hp"
                class="form-control"
                name="no_hp"
                placeholder="+62xxxxxxxxxxx"
                maxlength="15">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <label class="form-label">Jurusan</label>
                <input type="text"
                    id="nama_jurusan"
                    class="form-control"
                    value=""
                    placeholder="Jurusan otomatis mengikuti kelas"
                    readonly>
                <input type="hidden"
                    name="kode_jurusan"
                    id="kode_jurusan"
                    value="{{ $siswa->kode_jurusan }}">
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
<script>
$(function () {

    // Kalau kosong, isi +62
    if ($("#no_hp").val().trim() == "") {
        $("#no_hp").val("+62");
    }

    // Format nomor HP
    $("#no_hp").on("input", function () {

        var nomor = $(this).val();

        nomor = nomor.replace(/\D/g, '');

        if (nomor.startsWith("0")) {
            nomor = "62" + nomor.substring(1);
        } else if (!nomor.startsWith("62")) {
            nomor = "62" + nomor;
        }

        nomor = nomor.replace(/^620+/, "62");

        $(this).val("+" + nomor);

    });

    // Tidak boleh hapus +62
    $("#no_hp").on("keydown", function(e){

        if($(this).val().length <= 3 &&
           (e.key == "Backspace" || e.key == "Delete")){
            e.preventDefault();
        }

    });

    // MAPPING KELAS -> JURUSAN

    var mappingJurusan = {

        "10-1": {
            kode: "MP",
            nama: "Manajemen Perkantoran"
        },

        "10-2": {
            kode: "TJKT",
            nama: "Teknik Jaringan Komputer dan Telekomunikasi"
        },

        "10-3": {
            kode: "TM",
            nama: "Teknik Mesin"
        },

        "11-1": {
            kode: "MP",
            nama: "Manajemen Perkantoran"
        },

        "11-2": {
            kode: "TJKT",
            nama: "Teknik Jaringan Komputer dan Telekomunikasi"
        },

        "11-3": {
            kode: "TM",
            nama: "Teknik Mesin"
        },

        "12-1": {
            kode: "MP",
            nama: "Manajemen Perkantoran"
        },

        "12-2": {
            kode: "TJKT",
            nama: "Teknik Jaringan Komputer dan Telekomunikasi"
        },

        "12-3": {
            kode: "TM",
            nama: "Teknik Mesin"
        }

    };


    // Ketika kelas berubah
    $("#kelas").on("change", function () {

        var kelas = $(this).val();

        if (mappingJurusan[kelas]) {

            $("#nama_jurusan").val(
                mappingJurusan[kelas].nama
            );

            $("#kode_jurusan").val(
                mappingJurusan[kelas].kode
            );

        } else {

            $("#nama_jurusan").val("");

            $("#kode_jurusan").val("");

        }

    });


    // Jalankan otomatis saat form Edit dibuka
    $("#kelas").trigger("change");

    // Validasi submit
    $("#formsiswa").submit(function(){

        var hp = $("#no_hp").val();
        var nomor = hp.replace("+62", "");

        if(nomor.length < 9 || nomor.length > 15){

            Swal.fire({
                title: 'Warning!',
                text: 'Nomor HP harus terdiri dari 9 sampai 15 digit.',
                icon: 'warning'
            });

            return false;
        }

    });

});
</script>