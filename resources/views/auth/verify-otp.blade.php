<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Verifikasi OTP</title>

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
                    <h1>Verifikasi OTP</h1>
                    <h4>Masukkan kode OTP yang telah dikirim ke WhatsApp Anda.</h4>
                </div>
                <div class="section mt-1 mb-5">
                    <form id="verifyOtpForm">
                        @csrf
                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="otp"
                                    name="otp"
                                    maxlength="6"
                                    placeholder="Masukkan 6 Digit OTP"
                                    required>
                            </div>
                        </div>
                        <button
                            type="button"
                            id="btnVerifyOtp"
                            class="btn btn-primary btn-block btn-lg">
                            Verifikasi OTP
                        </button>
                        <div class="text-center mt-3">
                            <a href="#" id="btnResendOtp">
                                Kirim Ulang OTP
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js') }}"></script>

        <script>
        $('#btnVerifyOtp').click(function () {
            let otp = $('#otp').val();
            if (otp.length != 6) {
                Swal.fire({
                    icon:'warning',
                    title:'Perhatian',
                    text:'Masukkan 6 digit kode OTP.'
                });
                return;
            }
            $.ajax({
                url:"{{ route('verify.otp.submit') }}",
                type:"POST",
                data:{
                    _token:"{{ csrf_token() }}",
                    otp:otp
                },
                beforeSend:function(){
                    $('#btnVerifyOtp')
                        .prop('disabled',true)
                        .text('Memverifikasi...');
                },
                success:function(res){
                    Swal.fire({
                        icon:'success',
                        title:'Berhasil',
                        text:'OTP berhasil diverifikasi.'
                    }).then(function(){
                        window.location=res.redirect;
                    });
                },
                error:function(xhr){
                    let message='Terjadi kesalahan.';
                    if(xhr.responseJSON){
                        message=xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon:'error',
                        title:'Gagal',
                        text:message
                    });
                },
                complete:function(){
                    $('#btnVerifyOtp')
                        .prop('disabled',false)
                        .text('Verifikasi OTP');
                }
            });
        });
        </script>

        <script>
        $('#btnResendOtp').click(function(e){
            e.preventDefault();
            $.ajax({
                url:"{{ route('otp.resend') }}",
                type:"POST",
                data:{
                    _token:"{{ csrf_token() }}"
                },
                beforeSend:function(){
                    $('#btnResendOtp').text('Mengirim...');
                },
                success:function(res){
                    Swal.fire({
                        icon:'success',
                        title:'Berhasil',
                        text:res.message
                    });
                },
                error:function(xhr){
                    let message='Gagal mengirim ulang OTP.';
                    if(xhr.responseJSON){
                        message=xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon:'error',
                        title:'Gagal',
                        text:message
                    });
                },
                complete:function(){
                    $('#btnResendOtp').text('Kirim Ulang OTP');
                }
            });
        });
        </script>
    </body>
</html>