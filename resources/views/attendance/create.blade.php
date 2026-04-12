@extends('layouts.attendance')

@section('header')

<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Web Attendance</div>
</div>

<style>
    .webcam-capture {
        position: relative;
        width: 100%;
        height: 300px;
        background: black;
        border-radius: 15px;
        overflow: hidden;
    }

    video, canvas {
        position: absolute;
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

<div class="row mt-5">
    <div class="col">
        <input type="hidden" id="lokasi">

        <div class="webcam-capture">
            <video id="video" autoplay muted playsinline></video>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <button class="btn btn-primary btn-block" id="startCamera">
            🔓 Aktifkan Kamera
        </button>
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <button class="btn btn-success btn-block" id="takeabsen">
            📸 Ambil Absen
        </button>
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>

@endsection


@push('myscript')
<script>

let faceReady = false;
let lastDetection = null;
const video = document.getElementById('video');

// =====================
// LOAD MODEL
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

// =====================
// START CAMERA (FIXED)
// =====================
async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: 640,
                height: 480,
                facingMode: "user"
            },
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

// =====================
// BUTTON START CAMERA
// =====================
document.getElementById('startCamera').addEventListener('click', async () => {
    await loadFaceAPI();
    await startCamera();
});


video.addEventListener('play', () => {

    const canvas = faceapi.createCanvasFromMedia(video);
    document.querySelector('.webcam-capture').append(canvas);

    // 🔥 FIX SIZE (WAJIB)
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
                inputSize: 416,       // 🔥 lebih akurat
                scoreThreshold: 0.3   // 🔥 lebih sensitif
            })
        )
        .withFaceLandmarks()
        .withFaceDescriptors();

        console.log("Detections:", detections); // 🔥 DEBUG

        const resized = faceapi.resizeResults(detections, displaySize);

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // 🟢 KOTAK HIJAU
        faceapi.draw.drawDetections(canvas, resized);

        if (resized.length > 0) {
            lastDetection = resized[0];
        }

    }, 300); // 🔥 jangan terlalu cepat
});zz

// =====================
// ABSEN
// =====================
$('#takeabsen').click(function () {

    if (!faceReady) {
        alert("Model belum siap!");
        return;
    }

    if (!lastDetection) {
        alert("Wajah tidak terdeteksi!");
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    const image = canvas.toDataURL('image/jpeg');

    $.ajax({
        url: '/attendance/store',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            image: image,
            face_descriptor: JSON.stringify(Array.from(lastDetection.descriptor))
        },

        success: function (respond) {
            alert("Absen berhasil");
            window.location.href = '/dashboard';
        }
    });

});


// =====================
// GEOLOCATION
// =====================
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {

        var map = L.map('map').setView(
            [position.coords.latitude, position.coords.longitude], 18
        );

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);

    });
}

</script>
@endpush