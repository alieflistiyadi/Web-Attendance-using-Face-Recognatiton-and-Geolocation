<form action="{{ url('/guru/' . $guru->id . '/update') }}" method="POST" id="formeditguru">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 7a4 4 0 1 0 8 0"/>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                    </svg>
                </span>

                <input type="text"
                    name="name"
                    id="name"
                    class="form-control"
                    value="{{ $guru->name }}"
                    placeholder="Nama Guru">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 7l9 6l9-6"/>
                        <path d="M21 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7"/>
                    </svg>
                </span>

                <input type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    value="{{ $guru->email }}"
                    placeholder="Email">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                </span>

                <input type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Password Baru (Kosongkan jika tidak diubah)">
                    <small class="text-muted">
                    <br>
                    Jika diisi, password minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol.
                    </small>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <button class="btn btn-primary w-100">
                Simpan
            </button>
        </div>
    </div>
</form>

@push('myscript')
<script>
$("#formguru").submit(function(){

    var name = $("#name").val();
    var email = $("#email").val();

    if(name == ""){
        Swal.fire({
            title:'Warning!',
            text:'Nama Guru Harus Diisi',
            icon:'warning'
        });
        $("#name").focus();
        return false;
    }

    if(email == ""){
        Swal.fire({
            title:'Warning!',
            text:'Email Harus Diisi',
            icon:'warning'
        });
        $("#email").focus();
        return false;
    }

});
</script>
@endpush