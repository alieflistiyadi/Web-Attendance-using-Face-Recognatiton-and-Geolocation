<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>Lupa Password - E-Attendance SMK SMART</title>
    <link rel="icon" type="image/png" href=" {{ asset('assets/img/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href={{ asset('assets/img/icon/192x192.png') }}>
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>
    <link rel="manifest" href="__manifest.json">
</head>

<body class="bg-white">

    <!-- loader -->
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <!-- * loader -->


    <!-- App Capsule -->
    <div id="appCapsule" class="pt-0">

        <div class="login-form mt-1">
            <div class="section">
                <img src="{{ asset('assets/img/login/smksmart.png') }}" alt="image" class="form-image">
            </div>
            <div class="section mt-1">
                <h1>Lupa Password</h1>
                <h4>Masukkan NIS untuk menerima kode OTP melalui WhatsApp yang terdaftar</h4>
            </div>
            <div class="section mt-1 mb-5">

                @if (session('success'))
                    <div class="alert alert-outline-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-outline-warning">
                        {{ session('warning') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-outline-danger">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="forgotPasswordForm">
                    @csrf
                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="text" name="nis" value="{{ old('nis') }}" class="form-control" id="nis"
                                placeholder="NIS">
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    <div class="form-button-group">
                        <button
                            type="button"
                            id="btnCheckNis"
                            class="btn btn-primary btn-block btn-lg">
                            Kirim OTP
                        </button>
                    </div>
                </form>

                <div class="form-links mt-2 text-center">
                    <div><a href="{{ route('login') }}" class="text-muted">Kembali ke Login</a></div>
                </div>
            </div>
        </div>


    </div>
    <!-- * App Capsule -->



    <!-- ///////////// Js Files ////////////////////  -->
    <!-- Jquery -->
    <script src={{ asset('assets/js/lib/jquery-3.4.1.min.js') }}></script>
    <!-- Bootstrap-->
    <script src={{ asset('assets/js/lib/popper.min.js') }}></script>
    <script src={{ asset('assets/js/lib/bootstrap.min.js') }}></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>
    <!-- Owl Carousel -->
    <script src={{ asset('assets/js/plugins/owl-carousel/owl.carousel.min.js') }}></script>
    <!-- jQuery Circle Progress -->
    <script src={{ asset('assets/js/plugins/jquery-circle-progress/circle-progress.min.js') }}></script>
    <!-- Base Js File -->
    <script src={{ asset('assets/js/base.js') }}></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Forgot Password Script -->
    <script>

    $("#btnCheckNis").click(function(){

        let nis = $("#nis").val();

        if(nis=="")
        {
            Swal.fire({
                icon:'warning',
                title:'Perhatian',
                text:'Silakan masukkan NIS terlebih dahulu.'
            });

            return;
        }

        $.ajax({

            url:"{{ route('forgot.password.check') }}",

            type:"POST",

            data:{
                _token:"{{ csrf_token() }}",
                nis:nis
            },

            success:function(res){

                if(!res.success)
                {
                    Swal.fire({
                        icon:'error',
                        title:'Gagal',
                        text:res.message
                    });

                    return;
                }

                Swal.fire({

                    title:'Konfirmasi OTP',

                    html:`
                    <p>Kode OTP akan dikirim ke WhatsApp berikut</p>

                    <h3>${res.phone}</h3>

                    <p>Pastikan nomor tersebut masih aktif.</p>

                    <small style="color:red">
                    Nomor sudah tidak aktif?<br>
                    Hubungi admin sekolah untuk memperbarui nomor WhatsApp Anda.
                    </small>
                    `,

                    icon:'question',

                    showCancelButton:true,

                    confirmButtonText:'Kirim OTP',

                    cancelButtonText:'Batal'

                }).then((result)=>{

                    if(result.isConfirmed){

                        $.ajax({

                            url:"{{ route('forgot.password.sendotp') }}",

                            type:"POST",

                            data:{
                                _token:"{{ csrf_token() }}",
                                nis:nis
                            },

                            success:function(res){

                                if(!res.success){
                                    Swal.fire({
                                        icon:'error',
                                        title:'Gagal',
                                        text:res.message
                                    });
                                    return;
                                }

                                Swal.fire({

                                    icon:'success',

                                    title:'OTP Berhasil Dikirim',

                                    text:'Silakan cek WhatsApp Anda.'

                                }).then(()=>{

                                    window.location = res.redirect;

                                });

                            },

                            error:function(xhr){

                                Swal.fire({
                                    icon:'error',
                                    title:'Terjadi Kesalahan',
                                    text:'Tidak dapat mengirim OTP.'
                                });

                                console.log(xhr.responseText);

                            }

                        });

                    }

                });

            }

        });

    });

    </script>
</body>

</html>
<!-- ini kode forgot-password.blade.php -->