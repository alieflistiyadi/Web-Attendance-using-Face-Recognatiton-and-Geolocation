<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Reset Password</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-white">

<div id="appCapsule" class="pt-0">

    <div class="login-form mt-1">

        <div class="section">
            <img src="{{ asset('assets/img/login/smksmart.png') }}"
                 class="form-image">
        </div>

        <div class="section mt-1">
            <h1>Reset Password</h1>
            <h4>Silakan buat password baru Anda.</h4>
        </div>

        <div class="section mt-1 mb-5">

            <form action="{{ route('reset.password.update') }}" method="POST">

                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Password Baru"
                            required>

                        <a href="javascript:;" class="text-muted toggle-password" data-target="password">
                            <ion-icon name="eye-outline"></ion-icon>
                        </a>
                    </div>
                </div>

                <div class="pw-requirement-box">
                    <div class="pw-requirement-title">
                        Password Anda Harus:
                    </div>

                    <div class="pw-req-item" data-rule="length">
                        <span class="pw-req-icon">
                            <ion-icon name="checkmark-outline"></ion-icon>
                        </span>
                        Minimal 8 karakter
                    </div>

                    <div class="pw-req-item" data-rule="number">
                        <span class="pw-req-icon">
                            <ion-icon name="checkmark-outline"></ion-icon>
                        </span>
                        Mengandung minimal 1 angka
                    </div>

                    <div class="pw-req-item" data-rule="case">
                        <span class="pw-req-icon">
                            <ion-icon name="checkmark-outline"></ion-icon>
                        </span>
                        Mengandung huruf besar dan huruf kecil
                    </div>

                    <div class="pw-req-item" data-rule="special">
                        <span class="pw-req-icon">
                            <ion-icon name="checkmark-outline"></ion-icon>
                        </span>
                        <div class="pw-req-text">
                            Mengandung minimal 1 karakter spesial
                            <small>contoh: ! @ # $ % & *</small>
                        </div>
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input
                            type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Konfirmasi Password"
                            required>
                        
                        <a href="javascript:;" class="text-muted toggle-password" data-target="password_confirmation">
                            <ion-icon name="eye-outline"></ion-icon>
                        </a>
                    </div>

                    <div id="pwMatchRow" class="pw-match-row" style="display:none;"></div>
                </div>


                <button
                    id="btnSubmit"
                    type="submit"
                    class="btn btn-primary btn-block btn-lg">

                    Simpan Password

                </button>

            </form>

        </div>

    </div>

</div>

<script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js') }}"></script>
<script type="module"
src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>

<script nomodule
src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

@if ($errors->any())

<script>

    Swal.fire({
        icon:'error',
        title:'Gagal',
        text:'{{ $errors->first() }}'
    });

</script>

@endif

@if(session('success'))

<script>

    Swal.fire({
        icon:'success',
        title:'Berhasil',
        text:'{{ session("success") }}'
    });

</script>

@endif

<script>
    const passwordBaru = document.getElementById('password');
    const passwordKonfirmasi = document.getElementById('password_confirmation');
    const formResetPassword = document.querySelector('form');
    const pwMatchRow = document.getElementById('pwMatchRow');
    const btnSubmit = document.getElementById('btnSubmit');

    const pwRules = {
        length: v => v.length >= 8,
        number: v => /[0-9]/.test(v),
        case: v => /[a-z]/.test(v) && /[A-Z]/.test(v),
        special: v => /[~!@#$%^&*()\-_+=\[\]{}\\:;"'<>,.?/]/.test(v)
    };

    function checkPasswordRequirements() {

        const val = passwordBaru.value;
        let allValid = val.length > 0;

        Object.keys(pwRules).forEach(rule => {

            const el = document.querySelector(
                '.pw-req-item[data-rule="' + rule + '"]'
            );

            const isValid = pwRules[rule](val);

            el.classList.toggle('valid', isValid);

            if (!isValid) allValid = false;

        });

        checkPasswordMatch();
        return allValid;
    }

    function checkPasswordMatch() {
        const val = passwordBaru.value;
        const confirm = passwordKonfirmasi.value;

        if(confirm.length===0){
            pwMatchRow.style.display = 'none';
            return false;
        }

        pwMatchRow.style.display = 'flex';

        if (val===confirm) {
            pwMatchRow.textContent = '✔ Password cocok';
            pwMatchRow.classList.remove('no-match');
            pwMatchRow.classList.add('match');
            return true;
        } else {
            pwMatchRow.textContent = '✘ Password tidak cocok';
            pwMatchRow.classList.remove('match');
            pwMatchRow.classList.add('no-match');
            return false;
        }
    }

    function updateSubmitButton(){

        const valid =
            checkPasswordRequirements() &&
            checkPasswordMatch();

        btnSubmit.disabled = !valid;

    }

    passwordBaru.addEventListener('input', updateSubmitButton);
    passwordKonfirmasi.addEventListener('input', updateSubmitButton);

    updateSubmitButton();

    // Validasi sebelum submit — hanya jika user memang mengisi password baru
    formResetPassword.addEventListener('submit', function(e){

        const requirementsOk = checkPasswordRequirements();
        const matchOk = checkPasswordMatch();

        if(!requirementsOk || !matchOk){

            e.preventDefault();

            Swal.fire({
                title:'Password Belum Valid',
                text:'Pastikan password memenuhi semua syarat dan konfirmasi password sama.',
                icon:'warning'
            });

        }

    });

    $('.toggle-password').click(function(){

        let target = $('#' + $(this).data('target'));

        if(target.attr('type') === 'password'){
            target.attr('type','text');
            $(this).find('ion-icon').attr('name','eye-off-outline');
        }else{
            target.attr('type','password');
            $(this).find('ion-icon').attr('name','eye-outline');
        }

    });
    </script>

</body>
</html>