@extends('layouts.attendance')
@section('header')

    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Web Attendance</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <style>
        .webcam-capture {
            position: relative;
            width: 100%;
            height: 300px;
            background: black;
            border-radius: 15px;
            overflow: hidden;
        }

        .webcam-capture video,
        .webcam-capture canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #map {
            height: 200px;
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="{{ asset('assets/js/face-api.min.js') }}"></script>

@endsection

@section('content')
    <div class="row" style="margin-top: 70px;">
        <div class="col">
            <input type="hidden" id="lokasi">
            <div class="webcam-capture">
                <video id="video" autoplay muted playsinline></video>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col">
            @if($cek > 0)
                <button class="btn btn-danger btn-block" id="takeabsen">
                    <ion-icon name="camera-outline"></ion-icon> Absen Pulang
                </button>
            @else
                <button class="btn btn-primary btn-block" id="takeabsen">
                    <ion-icon name="camera-outline"></ion-icon> Absen Masuk
                </button>
            @endif
        </div>
    </div>

    <div class="row mt-2">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>

    <audio id="notifikasi_in">
        <source src="{{ asset('assets/sound/notifikasi_in.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_out">
        <source src="{{ asset('assets/sound/notifikasi_out.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_radius">
        <source src="{{ asset('assets/sound/notifikasi_radius.mp3') }}" type="audio/mpeg">
    </audio>
@endsection

@push('myscript')
<script>
    let faceReady = false;
    let lastDetection = null;
    const video = document.getElementById('video');

    var notifikasi_in = document.getElementById('notifikasi_in');
    var notifikasi_out = document.getElementById('notifikasi_out');
    var notifikasi_radius = document.getElementById('notifikasi_radius');

    // =====================
    // LOAD MODEL + KAMERA
    // =====================
    async function loadFaceAPI() {
        const MODEL_URL = '/models';
        try {
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            faceReady = true;
            console.log("✅ Face API Ready");
        } catch (e) {
            console.error("❌ Model gagal load:", e);
        }
    }

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { width: 640, height: 480, facingMode: "user" },
                audio: false
            });
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                video.play();
                console.log("✅ Kamera aktif");
            };
        } catch (err) {
            console.error("❌ Kamera error:", err);
            alert("Kamera tidak bisa diakses! Izinkan permission kamera.");
        }
    }

    // Jalankan otomatis saat halaman load
    window.addEventListener('load', async () => {
        await loadFaceAPI();
        await startCamera();
    });

    // =====================
    // FACE DETECTION
    // =====================
    video.addEventListener('play', () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        document.querySelector('.webcam-capture').append(canvas);

        const displaySize = {
            width: video.videoWidth,
            height: video.videoHeight
        };

        canvas.width = displaySize.width;
        canvas.height = displaySize.height;
        faceapi.matchDimensions(canvas, displaySize);

        setInterval(async () => {
            if (!faceReady) return;

            const detections = await faceapi.detectAllFaces(
                video,
                new faceapi.TinyFaceDetectorOptions({
                    inputSize: 416,
                    scoreThreshold: 0.3
                })
            )
            .withFaceLandmarks()
            .withFaceDescriptors();

            const resized = faceapi.resizeResults(detections, displaySize);

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            faceapi.draw.drawDetections(canvas, resized);

            if (resized.length > 0) {
                lastDetection = resized[0];
            }
        }, 300);
    });

    // =====================
    // GEOLOCATION
    // =====================
    var lokasi = document.getElementById('lokasi');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        lokasi.value = position.coords.latitude + ',' + position.coords.longitude;

        var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);

        L.circle([-6.269107996706856, 106.91735464750927], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 10
        }).addTo(map);
    }

    function errorCallback() {}

    // =====================
    // ABSEN
    // =====================
    $('#takeabsen').click(function () {
        if (!faceReady) {
            Swal.fire({ title: 'Error!', text: 'Model wajah belum siap, tunggu sebentar.', icon: 'error' });
            return;
        }

        if (!lastDetection) {
            Swal.fire({ title: 'Error!', text: 'Wajah tidak terdeteksi!', icon: 'error' });
            return;
        }

        // Ambil snapshot dari video
        const snapCanvas = document.createElement('canvas');
        snapCanvas.width = video.videoWidth;
        snapCanvas.height = video.videoHeight;
        const ctx = snapCanvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        const image = snapCanvas.toDataURL('image/jpeg', 0.8);

        var lokasiVal = $('#lokasi').val();

        $.ajax({
            url: '/attendance/store',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                image: image,
                lokasi: lokasiVal
            },
            cache: false,
            success: function (respond) {
                var status = respond.split("|");
                if (status[0] == "success") {
                    if (status[2] == "in") {
                        notifikasi_in.play();
                    } else {
                        notifikasi_out.play();
                    }
                    Swal.fire({
                        title: 'Success!',
                        text: status[1],
                        icon: 'success',
                    });
                    setTimeout("location.href='/dashboard'", 3000);
                } else {
                    if (status[2] == "radius") {
                        notifikasi_radius.play();
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: status[1],
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            }
        });
    });
</script>
@endpush