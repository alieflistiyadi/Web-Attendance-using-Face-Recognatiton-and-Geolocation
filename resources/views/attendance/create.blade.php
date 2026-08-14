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
            transition: height .25s ease;
        }

        /* ✅ Saat Tahap Liveness aktif, kotak dibesarkan agar konten tidak numpuk/terpotong */
        .webcam-capture.stage-liveness-active {
            height: 460px;
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

        /* ✅ Foto hasil liveness (menggantikan live webcam begitu verifikasi selesai) */
        .liveness-result-photo {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            z-index: 6;
            background: #000;
        }

        /* Saat foto hasil liveness ditampilkan, live video + canvas overlay + elemen
                                                                                debug disembunyikan agar user cuma lihat foto diam, bukan live feed */
        .webcam-capture.show-result-photo video,
        .webcam-capture.show-result-photo canvas.face-canvas,
        .webcam-capture.show-result-photo #face-status,
        .webcam-capture.show-result-photo #earDebugText,
        .webcam-capture.show-result-photo #antiSpoofDebugPreview {
            display: none;
        }

        #map {
            height: 200px;
        }

        /* Status wajah di pojok kiri atas webcam */
        #face-status {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 20px;
            z-index: 10;
        }

        /* ✅ Debug: angka EAR real-time (bisa dihapus kalau sudah tidak perlu tuning lagi) */
        .ear-debug {
            position: absolute;
            bottom: 8px;
            left: 8px;
            right: 8px;
            z-index: 15;
            background: rgba(0, 0, 0, 0.55);
            color: #7CFC00;
            font-size: 10px;
            font-family: monospace;
            padding: 3px 6px;
            border-radius: 6px;
            word-break: break-all;
            pointer-events: none;
        }

        /* ══════════════════════════════ */
        /* ✅ LIVENESS DETECTION          */
        /* ══════════════════════════════ */

        /* ── Tahap 1 (liveness): Liveness circle + ring ── */
        .liveness-overlay {
            position: absolute;
            inset: 0;
            background: #000;
            z-index: 30;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px 0;
        }

        .liveness-circle-wrap {
            position: relative;
            width: 180px;
            height: 180px;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .liveness-circle-wrap video.video-circle-clone {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 180px;
            height: 180px;
            transform: translate(-50%, -50%) scaleX(-1);
            border-radius: 50%;
            object-fit: cover;
        }

        .liveness-ring {
            position: absolute;
            inset: 0;
            width: 180px;
            height: 180px;
            transform: rotate(-90deg);
        }

        .ring-track {
            fill: none;
            stroke: rgba(255, 255, 255, 0.25);
            stroke-width: 6;
        }

        .ring-fill {
            fill: none;
            stroke: #42a5f5;
            stroke-width: 6;
            stroke-linecap: round;
            transition: stroke-dashoffset .5s cubic-bezier(.4, 0, .2, 1), stroke .5s ease;
        }

        .ring-fill.phase-move {
            stroke: #ffffff;
        }

        .ring-fill.done {
            stroke: #00e676;
        }

        /* ── Pulsa lembut di sekeliling lingkaran saat menunggu aksi ── */
        .liveness-circle-wrap.pulsing {
            animation: circlePulse 1.8s ease-out infinite;
            border-radius: 50%;
        }

        @keyframes circlePulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.45);
            }

            70% {
                box-shadow: 0 0 0 16px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        /* ── Badge centang animasi saat liveness selesai ── */
        .liveness-check-badge {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 200, 83, 0.15);
            border-radius: 50%;
            opacity: 0;
            transform: scale(.5);
            transition: opacity .3s ease, transform .35s cubic-bezier(.34, 1.56, .64, 1);
            pointer-events: none;
        }

        .liveness-check-badge.show {
            opacity: 1;
            transform: scale(1);
        }

        .liveness-check-badge ion-icon {
            font-size: 64px;
            color: #00e676;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.4));
        }

        /* ── Indikator kedip mata (tampil sebelum tahap gerakan kepala) ── */
        .blink-indicator {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .blink-indicator ion-icon {
            font-size: 46px;
            color: rgba(255, 255, 255, 0.85);
            animation: blinkPulse 1.6s ease-in-out infinite;
        }

        @keyframes blinkPulse {

            0%,
            100% {
                transform: scaleY(1);
                opacity: 0.85;
            }

            50% {
                transform: scaleY(0.15);
                opacity: 1;
            }
        }

        .liveness-instruction {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            padding: 0 24px;
            line-height: 1.4;
        }

        .liveness-hint {
            color: rgba(255, 255, 255, 0.6);
            font-size: 11.5px;
            margin-top: 6px;
        }

        /* ── Checklist liveness: gerakan kepala + kedipan mata ── */
        .liveness-checklist {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 12px;
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
            font-weight: 600;
            transition: color .2s ease;
        }

        .check-item ion-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .check-item.done {
            color: #00e676;
        }

        /* ── Badge sukses liveness ── */
        .liveness-success-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #e8f5e9;
            color: #00c853;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 12px;
            border-radius: 10px;
            margin-top: 10px;
        }

        /* ── ✅ Status auto-submit absensi (menggantikan tombol manual) ── */
        .auto-attendance-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #e3f2fd;
            color: #1565c0;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 14px;
            border-radius: 10px;
        }

        .auto-attendance-status ion-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .auto-attendance-status.error {
            background: #ffebee;
            color: #c62828;
        }

        /* Spinner kecil untuk ion-icon saat status "memproses" */
        .auto-attendance-status.processing ion-icon {
            animation: autoStatusSpin 1s linear infinite;
        }

        @keyframes autoStatusSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* ── Tahap Blocked: belum daftar wajah ── */
        .blocked-overlay {
            position: absolute;
            inset: 0;
            background: rgba(20, 20, 20, 0.96);
            z-index: 40;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 24px;
        }

        .blocked-overlay ion-icon {
            font-size: 42px;
            color: #ff9800;
            margin-bottom: 10px;
        }

        .blocked-overlay .blocked-title {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .blocked-overlay .blocked-desc {
            color: rgba(255, 255, 255, 0.75);
            font-size: 12.5px;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .blocked-overlay a.btn-daftar-wajah {
            background: #1a73e8;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none;
        }

        /* ── Peringatan wajah tidak sesuai selama liveness ── */
        .liveness-instruction.mismatch {
            color: #ff5252;
        }

        .liveness-hint.mismatch {
            color: #ff8a80;
            font-weight: 600;
        }

        /* ✅ OPSI 4: Peringatan terdeteksi layar/rekaman (moire/FFT anti-spoofing) */
        .liveness-instruction.screen-warning {
            color: #ffab40;
        }

        .liveness-hint.screen-warning {
            color: #ffd180;
            font-weight: 600;
        }

        /* ✅ BARU: overlay jeda (cooldown) setelah foto/layar terdeteksi, sebelum mengulang dari awal */
        .liveness-cooldown-overlay {
            position: absolute;
            inset: 0;
            background: rgba(20, 8, 0, 0.9);
            z-index: 35;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 24px;
        }

        .liveness-cooldown-overlay ion-icon {
            font-size: 46px;
            color: #ffab40;
            margin-bottom: 10px;
        }

        .liveness-cooldown-overlay .cooldown-title {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .liveness-cooldown-overlay .cooldown-desc {
            color: rgba(255, 255, 255, 0.75);
            font-size: 12.5px;
            line-height: 1.6;
        }

        /* ══════════════════════════════ */
        /* ✅ ANTI-SPOOFING TAMBAHAN       */
        /* (Opsi 1: challenge arah acak,   */
        /*  Opsi 2: senyum,                */
        /*  Opsi 3: konsistensi geometri)  */
        /* ══════════════════════════════ */

        /* Opsi 1: panah arah untuk instruksi geleng kepala terarah (acak tiap sesi) */
        .direction-arrow {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 25;
        }

        .direction-arrow ion-icon {
            font-size: 54px;
            color: #ffffff;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, .5));
            animation: arrowNudgeRight 1.2s ease-in-out infinite;
        }

        .direction-arrow.arrow-left ion-icon {
            animation-name: arrowNudgeLeft;
        }

        @keyframes arrowNudgeRight {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(10px);
            }
        }

        @keyframes arrowNudgeLeft {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(-10px);
            }
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="{{ asset('assets/js/face-api.min.js') }}"></script>
    {{-- ✅ OPSI 5: onnxruntime-web untuk menjalankan model anti-spoofing terlatih (MiniFASNetV2) langsung di browser --}}
    <script src="https://cdn.jsdelivr.net/npm/onnxruntime-web@1.19.2/dist/ort.min.js"></script>

@endsection

@section('content')

    <div class="row" style="margin-top: 70px;">
        <div class="col">
            <input type="hidden" id="lokasi">
            <input type="hidden" id="jadwal_id" value="{{ $jadwalAktif->id ?? '' }}">

            @if(!$libur && $jadwalAktif)

                        <div class="mt-2">
                            @if($modeAbsensi === 'in')
                                <span class="badge bg-primary">
                                    <ion-icon name="log-in-outline"></ion-icon>
                                    Absen Masuk
                                </span>
                            @elseif($modeAbsensi === 'out')
                                <span class="badge bg-danger">
                                    <ion-icon name="log-out-outline"></ion-icon>
                                    Absen Pulang
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                </div>
            @endif

    <div class="webcam-capture" id="webcamCapture">
        <video id="video" autoplay muted playsinline></video>

        {{-- ✅ Foto hasil liveness — ditampilkan menggantikan live webcam
        begitu tahap liveness selesai (lihat captureAndShowResultPhoto() di JS) --}}
        <img id="livenessResultPhoto" class="liveness-result-photo" alt="Foto absensi">

        {{-- Status kecil di pojok --}}
        <div id="face-status">⏳ Memuat model...</div>

        {{-- ✅ Debug: angka EAR/Smile real-time untuk membantu menyetel threshold.
        Hapus/hilangkan div ini kalau sudah tidak dibutuhkan lagi di production. --}}
        <div id="earDebugText" class="ear-debug"></div>

        {{-- ✅ DEBUG SEMENTARA: preview 80x80 crop yang dikirim ke model anti-spoofing.
        Ini untuk mengecek apakah crop-nya benar (wajah penuh di tengah) atau salah
        (kepotong/kosong/salah posisi). Hapus <div> ini kalau sudah tidak perlu lagi. --}}
            <div id="antiSpoofDebugPreview"
                style="position:absolute; top:8px; right:8px; z-index:20; border:2px solid #ffab40; border-radius:4px; overflow:hidden; width:80px; height:80px; background:#000;">
            </div>

            {{-- ✅ TAHAP LIVENESS (kedip -> gerakan kepala terarah), langsung tampil tanpa tahap framing
            --}}
            <div class="liveness-overlay" id="stageLivenessOverlay" style="display:none;">
                <div class="liveness-circle-wrap">
                    <video class="video-circle-clone" id="videoCircleClone" autoplay muted playsinline></video>
                    <svg class="liveness-ring" id="livenessRing" viewBox="0 0 180 180" style="display:none;">
                        <circle class="ring-track" cx="90" cy="90" r="82"></circle>
                        <circle class="ring-fill" id="ringFill" cx="90" cy="90" r="82"></circle>
                    </svg>
                    <div class="blink-indicator" id="blinkIndicator">
                        <ion-icon name="eye-outline"></ion-icon>
                    </div>
                    <div class="liveness-check-badge" id="livenessCheckBadge">
                        <ion-icon name="checkmark-circle"></ion-icon>
                    </div>
                </div>

                {{-- ✅ Opsi 1: panah arah acak untuk tahap gerakan kepala terarah --}}
                <div class="direction-arrow" id="directionArrow">
                    <ion-icon name="arrow-forward-outline"></ion-icon>
                </div>

                <div class="liveness-instruction" id="livenessInstructionText">
                    Kedipkan mata Anda sekali.
                </div>
                <div class="liveness-hint" id="livenessHint">Tatap kamera secara normal</div>

                {{-- ✅ Checklist: kedipan -> gerakan kepala terarah --}}
                <div class="liveness-checklist">
                    <div class="check-item" id="checkBlink">
                        <ion-icon name="ellipse-outline"></ion-icon>
                        1. Kedipkan mata Anda sekali
                    </div>
                    <div class="check-item" id="checkMove">
                        <ion-icon name="ellipse-outline"></ion-icon>
                        2. Ikuti arah panah &amp; kembali ke tengah
                    </div>
                </div>
            </div>

            {{-- ✅ BARU: overlay jeda (cooldown) setelah foto/layar terdeteksi --}}
            <div class="liveness-cooldown-overlay" id="stageCooldownOverlay">
                <ion-icon name="hourglass-outline"></ion-icon>
                <div class="cooldown-title">Terdeteksi Foto/Layar</div>
                <div class="cooldown-desc" id="cooldownDescText">
                    Bukan wajah asli langsung. Mengulang verifikasi sebentar lagi...
                </div>
            </div>

            {{-- ✅ TAHAP BLOCKED: akun belum daftar wajah --}}
            <div class="blocked-overlay" id="stageBlockedOverlay">
                <ion-icon name="alert-circle-outline"></ion-icon>
                <div class="blocked-title">Wajah Anda Belum Terdaftar</div>
                <div class="blocked-desc">
                    Absensi wajah membutuhkan data wajah yang sudah didaftarkan.
                    Silakan daftarkan wajah Anda terlebih dahulu di halaman Profil.
                </div>
                <a href="/attendance/editprofile" class="btn-daftar-wajah">
                    Daftarkan Wajah Sekarang
                </a>
            </div>
        </div>

        {{-- ✅ Badge muncul sesaat setelah liveness sukses --}}
        <div class="liveness-success-badge" id="livenessSuccessBadge" style="display:none;">
            <ion-icon name="checkmark-circle-outline"></ion-icon>
            Liveness Terverifikasi — Wajah Asli Terdeteksi
        </div>
    </div>
    </div>

    {{-- ✅ Status otomatis: tampil setelah liveness lolos, absensi terkirim
    sendiri begitu lokasi didapat — user tidak perlu tap apa pun. --}}
    <div class="row mt-2" id="stageAutoStatus" style="display:none;">
        <div class="col">
            <div class="auto-attendance-status" id="autoAttendanceStatusBox">
                <ion-icon name="location-outline"></ion-icon>
                <span id="autoAttendanceStatusText">Mendapatkan lokasi Anda...</span>
            </div>
        </div>
    </div>

    {{-- ✅ Tombol fallback: HANYA tampil kalau auto-submit gagal
    (lokasi ditolak, koneksi error, wajah tidak cocok, di luar radius, dll) --}}
    <div class="row mt-2" id="stageReadyButton" style="display:none;">
        <div class="col">
            @if($modeAbsensi === 'out')
                <button class="btn btn-danger btn-block" id="takeabsen">
                    <ion-icon name="refresh-outline"></ion-icon>
                    Coba Lagi Kirim Absen Pulang
                </button>
            @else
                <button class="btn btn-primary btn-block" id="takeabsen">
                    <ion-icon name="refresh-outline"></ion-icon>
                    Coba Lagi Kirim Absen Masuk
                </button>
            @endif
        </div>
    </div>

    <div class="row mt-2" id="stageReadyMap" style="display:none;">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>

    <audio id="notifikasi_in">
        <source src="{{ asset('assets/sound/notifikasi_in.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_out">
        <source src=" {{ asset('assets/sound/notifikasi_out.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_radius">
        <source src="{{ asset('assets/sound/notifikasi_radius.mp3') }}" type="audio/mpeg">
    </audio>
@endsection

@push('myscript')

    <script>
        @if($libur)
            // ✅ Hari libur (Sabtu/Minggu) atau konfigurasi jam belum dibuat untuk hari ini:
            // JANGAN jalankan kamera/model wajah/liveness sama sekali (hemat resource & tidak
            // minta izin kamera untuk halaman yang memang tidak bisa dipakai absen).
            // Cukup tampilkan SweetAlert lalu arahkan siswa kembali ke dashboard.
            window.addEventListener('load', function () {
                Swal.fire({
                    title: 'Tidak Bisa Absen',
                    text: @json($pesan_libur),
                    icon: 'warning',
                    confirmButtonText: 'Kembali ke Dashboard',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    window.location.href = '/dashboard';
                });
            });
        @else
            let faceReady = false;
            let lastDetection = null;
            let labeledDescriptors = [];
            let faceMatcher = null;
            let detectedNis = null; // NIS siswa yang wajahnya cocok

            const video = document.getElementById('video');
            const videoCircleClone = document.getElementById('videoCircleClone');
            const faceStatus = document.getElementById('face-status');
            const earDebugText = document.getElementById('earDebugText'); // ✅ debug angka EAR/Smile real-time

            // ✅ Elemen foto hasil liveness (menggantikan live webcam di tahap ready)
            const livenessResultPhoto = document.getElementById('livenessResultPhoto');

            var notifikasi_in = document.getElementById('notifikasi_in');
            var notifikasi_out = document.getElementById('notifikasi_out');
            var notifikasi_radius = document.getElementById('notifikasi_radius');

            // ══════════════════════════════
            // ✅ STATE MACHINE LIVENESS
            // ══════════════════════════════
            // Tahap framing/bingkai dihapus (masih bisa ditembus foto) -> langsung mulai
            // dari liveness begitu kamera aktif dan model siap.
            // stage: 'liveness' -> 'ready'
            // livenessSubStage (di dalam 'liveness'): 'blink' -> 'move'
            let stage = 'liveness';
            let livenessStageStarted = false; // guard supaya transitionToLiveness() cuma dipanggil sekali

            let livenessMinYaw = 999;
            let livenessMaxYaw = -999;
            let livenessPassed = false;
            let livenessTimeoutTimer = null;
            const LIVENESS_TIMEOUT_MS = 25000; // 25 detik, kalau gagal beri instruksi ulang

            // Timer relaksasi progresif khusus fase kedip. Kalau user sudah mencoba
            // kedip cukup lama tapi tidak lolos (kemungkinan threshold masih terlalu ketat
            // untuk kamera/pencahayaan device tsb), beri hint yang lebih jelas dan longgarkan
            // sedikit toleransinya secara bertahap alih-alih membuat user stuck selamanya.
            let blinkRelaxTimer = null;
            let blinkRelaxLevel = 0; // 0 = normal, 1 = agak longgar, 2 = paling longgar
            const BLINK_RELAX_STEP_MS = 5000; // setiap 5 detik gagal, longgarkan satu level
            const BLINK_RELAX_MAX_LEVEL = 2;

            // ✅ Identitas: liveness harus dilakukan oleh wajah pemilik akun sendiri
            const nisLogin = "{{ Auth::guard('siswa')->user()->nis }}";
            let myDescriptorRegistered = false;
            let livenessMismatchStreak = 0;
            const LIVENESS_MISMATCH_LIMIT = 6; // ~1.8 detik wajah tidak cocok berturut-turut -> reset progress

            // ✅ BARU: Jeda (cooldown) setelah terdeteksi foto/layar, supaya flow-nya
            // "deteksi -> jeda singkat dengan pesan jelas -> ulang dari awal", BUKAN
            // ngulang-ngulang cepat/flicker seperti sebelumnya (karena dulu langsung
            // resetLivenessProgress() setiap kali limit tercapai, dan begitu direset,
            // deteksi jalan lagi seketika lalu bisa langsung ke-trigger lagi kalau
            // foto/layar masih ada di depan kamera).
            let inSpoofCooldown = false;
            const SPOOF_COOLDOWN_MS = 2500; // 2.5 detik jeda tenang sebelum mengulang dari awal
            const stageCooldownOverlay = document.getElementById('stageCooldownOverlay');
            const cooldownDescText = document.getElementById('cooldownDescText');

            // ✅ Blink detection (Eye Aspect Ratio) — kalibrasi adaptif per pengguna
            let eyeState = 'open'; // 'open' | 'closed'
            let blinkCount = 0;
            const BLINK_REQUIRED = 1; // minimal jumlah kedipan yang harus terdeteksi

            let earBaseline = null;
            let earCalibrationSamples = [];
            const EAR_CALIBRATION_FRAMES_NEEDED = 8; // sedikit ditambah agar baseline lebih stabil (median, bukan max)
            const EAR_CLOSED_RATIO = 0.88;
            const EAR_OPEN_RATIO = 0.90;
            const EAR_CLOSED_THRESHOLD_FALLBACK = 0.22;
            const EAR_OPEN_THRESHOLD_FALLBACK = 0.27;
            // Baseline ikut menyesuaikan perlahan (drift-follow) selama mata terbuka, supaya tidak
            // "kaku" ke angka hasil kalibrasi awal saja kalau pencahayaan/jarak wajah sedikit berubah.
            const EAR_BASELINE_ADAPT_RATE = 0.06;

            // ✅ Sub-tahap dalam Tahap 2: 'blink' -> 'move'
            let livenessSubStage = 'blink';

            // ══════════════════════════════
            // ✅ OPSI 1: Challenge arah acak + kembali ke tengah
            // ══════════════════════════════
            let targetYawSign = 1;
            let moveReachedTarget = false;
            const YAW_TARGET_THRESHOLD = 12; // derajat perkiraan yang wajib dicapai ke arah instruksi
            const YAW_RETURN_THRESHOLD = 6;  // dianggap "kembali ke tengah" jika |yaw| turun ke bawah ini

            // ✅ BARU (OPSI 3 - anti "foto dimiringkan"): rentang rasio lebar/tinggi bounding-box
            // wajah manusia yang wajar selama menoleh. Foto/HP yang dimiringkan secara fisik di
            // depan kamera untuk mensimulasikan yaw sering menghasilkan distorsi bounding-box yang
            // tidak proporsional (mendadak jauh lebih pipih/lebar dari wajah asli). Kalau rasio di
            // luar rentang ini, progress gerakan tidak dihitung.
            const FACE_BOX_RATIO_MIN = 0.55;
            const FACE_BOX_RATIO_MAX = 1.15;

            // ══════════════════════════════
            // ✅ OPSI 4: DETEKSI LAYAR / MOIRÉ (anti video-replay)
            // ══════════════════════════════
            // Behavior challenge (kedip/toleh) bisa ditembus dengan MEMUTAR VIDEO
            // wajah asli di layar HP lain di depan kamera — gerakannya memang "sungguhan"
            // terjadi secara geometris, hanya bukan terjadi live di depan lensa. Untuk
            // menutup celah ini, tiap beberapa frame kita crop area wajah, ubah ke
            // grayscale, lalu analisis FFT 2D untuk mencari pola moiré/aliasing frekuensi
            // tinggi yang khas saat sebuah layar RGB direkam ulang oleh kamera lain.
            // Ini heuristik ringan (bukan model ML terlatih) — WAJIB dikalibrasi ulang
            // di device/kamera nyata sebelum dipakai di production (lihat console.log
            // '🔍 Moire debug' untuk melihat angka mentahnya lalu sesuaikan threshold).
            const MOIRE_PATCH_SIZE = 64; // harus pangkat 2 (32/64/128) untuk FFT radix-2
            const MOIRE_LOW_FREQ_SKIP_RATIO = 0.08;  // radius rendah yang diabaikan (skin tone/pencahayaan)
            const MOIRE_MID_BAND_INNER_RATIO = 0.15;
            const MOIRE_MID_BAND_OUTER_RATIO = 0.32;
            const MOIRE_HIGH_BAND_RATIO = 0.38;
            // ⚠️ Threshold di bawah ini PERLU dikalibrasi: kumpulkan angka dari beberapa
            // percobaan wajah asli vs. layar HP, lalu set di tengah-tengah kedua populasi.
            const MOIRE_HIGH_FREQ_RATIO_THRESHOLD = 0.12;
            const MOIRE_PEAK_RATIO_THRESHOLD = 9;
            const MOIRE_CHECK_EVERY_N_TICKS = 1; // cek tiap tick loop utama (300ms); naikkan kalau device lambat
            const MOIRE_SUSPICION_LIMIT = 5;     // ~1.5 detik berturut-turut mencurigakan -> reset progress
            let moireSuspicionStreak = 0;
            let moireTickCounter = 0;

            // Canvas offscreen untuk crop+downsample area wajah, dipakai ulang tiap tick
            // supaya tidak alokasi canvas baru berkali-kali (hemat memori/GC).
            const moireCanvas = document.createElement('canvas');
            moireCanvas.width = MOIRE_PATCH_SIZE;
            moireCanvas.height = MOIRE_PATCH_SIZE;
            const moireCtx = moireCanvas.getContext('2d', { willReadFrequently: true });

            // ══════════════════════════════
            // ✅ OPSI 5: MODEL ANTI-SPOOFING TERLATIH (MiniFASNetV2, ONNX)
            // ══════════════════════════════
            // Menggantikan/melengkapi heuristik FFT moire di atas dengan model CNN yang
            // benar-benar dilatih dari puluhan ribu contoh wajah asli vs. serangan foto
            // cetak/layar (dataset CelebA-Spoof). Jauh lebih tahan terhadap layar HP
            // resolusi tinggi dibanding rumus FFT buatan sendiri, karena model belajar
            // pola tekstur/warna/refleksi halus, bukan cuma satu-dua angka statistik.
            //
            // Sumber model: proyek Silent-Face-Anti-Spoofing (minivision-ai, Apache-2.0),
            // diexport ke ONNX oleh QingHeYang/Silent-Face-Anti-Spoofing-onnx.
            // Arsitektur: MiniFASNetV2, input 80x80 BGR [0,1], output 3 kelas:
            //   index 0 = serangan tipe 1 (mis. print/foto cetak)
            //   index 1 = WAJAH ASLI
            //   index 2 = serangan tipe 2 (mis. replay/layar)
            // (urutan ini sesuai script test.py resmi: `if label == 1: Real Face`)
            //
            // ⚠️ File model (.onnx) TIDAK ikut dalam blade ini — taruh file
            // 2.7_80x80_MiniFASNetV2.onnx di public/models/antispoof/ pada project
            // Laravel Anda. File disediakan terpisah bersama jawaban ini.
            const ANTISPOOF_MODEL_URL = "{{ asset('models/antispoof/2.7_80x80_MiniFASNetV2.onnx') }}";
            const ANTISPOOF_INPUT_SIZE = 80;   // sesuai training resmi, jangan diubah
            const ANTISPOOF_CROP_SCALE = 2.7;  // sesuai nama file model, jangan diubah
            // ⚠️ Threshold ini lebih bisa dipercaya daripada threshold FFT (karena model
            // terlatih), tapi tetap sebaiknya diamati dulu lewat console log
            // '🛡️ AntiSpoof debug' pada beberapa percobaan asli vs spoof di device nyata.
            const ANTISPOOF_REAL_THRESHOLD = 0.90;
            const ANTISPOOF_CHECK_EVERY_N_TICKS = 2; // tiap 2 tick (~600ms); naikkan kalau device lambat
            const ANTISPOOF_SUSPICION_LIMIT = 4;     // ~2.4 detik berturut-turut mencurigakan -> reset progress
            let antiSpoofSession = null;
            let antiSpoofReady = false;
            let antiSpoofSuspicionStreak = 0;
            let antiSpoofTickCounter = 0;

            const antiSpoofCanvas = document.createElement('canvas');
            antiSpoofCanvas.width = ANTISPOOF_INPUT_SIZE;
            antiSpoofCanvas.height = ANTISPOOF_INPUT_SIZE;
            const antiSpoofCtx = antiSpoofCanvas.getContext('2d', { willReadFrequently: true });
            // ✅ DEBUG SEMENTARA: tampilkan crop 80x80 secara live di pojok kanan atas kamera,
            // supaya kelihatan persis apa yang dikirim ke model. Hapus blok ini setelah selesai debug.
            antiSpoofCanvas.style.width = '100%';
            antiSpoofCanvas.style.height = '100%';
            const antiSpoofDebugPreviewEl = document.getElementById('antiSpoofDebugPreview');
            if (antiSpoofDebugPreviewEl) antiSpoofDebugPreviewEl.appendChild(antiSpoofCanvas);

            const directionArrow = document.getElementById('directionArrow');

            const checkMove = document.getElementById('checkMove');
            const checkBlink = document.getElementById('checkBlink');
            const livenessRing = document.getElementById('livenessRing');
            const blinkIndicator = document.getElementById('blinkIndicator');
            const livenessCircleWrap = document.querySelector('.liveness-circle-wrap');
            const livenessCheckBadge = document.getElementById('livenessCheckBadge');

            const stageBlockedOverlay = document.getElementById('stageBlockedOverlay');
            const webcamCapture = document.getElementById('webcamCapture');

            const stageLivenessOverlay = document.getElementById('stageLivenessOverlay');
            const livenessInstructionText = document.getElementById('livenessInstructionText');
            const livenessHint = document.getElementById('livenessHint');
            const ringFill = document.getElementById('ringFill');

            const livenessSuccessBadge = document.getElementById('livenessSuccessBadge');
            const stageReadyButton = document.getElementById('stageReadyButton');
            const stageReadyMap = document.getElementById('stageReadyMap');

            // ✅ Elemen status auto-submit (menggantikan interaksi tombol manual)
            const stageAutoStatus = document.getElementById('stageAutoStatus');
            const autoAttendanceStatusBox = document.getElementById('autoAttendanceStatusBox');
            const autoAttendanceStatusText = document.getElementById('autoAttendanceStatusText');

            // ✅ Guard supaya absensi cuma auto-triggered sekali per sesi liveness,
            // dan tidak ada submit dobel kalau event lokasi/klik nyasar bersamaan.
            let attendanceAutoTriggered = false;
            let attendanceSubmitting = false;

            // Helper: update kotak status otomatis (teks + ikon + warna)
            function setAutoStatus(text, icon, isError) {
                stageAutoStatus.style.display = 'flex';
                autoAttendanceStatusText.textContent = text;
                const iconEl = autoAttendanceStatusBox.querySelector('ion-icon');
                if (iconEl) iconEl.setAttribute('name', icon || 'information-circle-outline');
                autoAttendanceStatusBox.classList.toggle('error', !!isError);
                autoAttendanceStatusBox.classList.toggle('processing', !isError && icon === 'sync-outline');
            }

            // Helper: tampilkan tombol fallback manual (dipakai kalau auto-submit gagal)
            function showRetryButton() {
                stageReadyButton.style.display = 'flex';
                $('#takeabsen').prop('disabled', false);
            }

            // ✅ BARU: Jalankan jeda (cooldown) rapi setelah foto/layar terdeteksi,
            // lalu baru mengulang liveness dari awal — menggantikan pola lama yang
            // langsung resetLivenessProgress() seketika (yang bisa terasa "ngulang-
            // ngulang"/flicker kalau kondisi spoof masih terus terdeteksi begitu direset).
            function triggerSpoofCooldownThenReset(message) {
                if (inSpoofCooldown) return; // sudah dalam jeda, jangan ditumpuk lagi
                inSpoofCooldown = true;

                // Hentikan semua loop deteksi selama jeda
                stopBlinkFastLoop();
                clearBlinkRelaxTimer();
                clearTimeout(livenessTimeoutTimer);

                // Sembunyikan overlay liveness, tampilkan overlay jeda yang jelas & tenang
                stageLivenessOverlay.style.display = 'none';
                if (cooldownDescText) cooldownDescText.textContent = message;
                stageCooldownOverlay.style.display = 'flex';

                setTimeout(() => {
                    stageCooldownOverlay.style.display = 'none';
                    inSpoofCooldown = false;
                    // Setelah jeda selesai, baru mulai ulang liveness dari awal (sub-tahap kedip)
                    stageLivenessOverlay.style.display = 'flex';
                    resetLivenessProgress();
                }, SPOOF_COOLDOWN_MS);
            }

            // ✅ Ambil 1 frame dari video yang sedang berjalan, tampilkan sebagai foto
            // diam menggantikan live webcam. Live <video>/<canvas> deteksi TETAP jalan
            // di background (disembunyikan lewat class 'show-result-photo') karena masih
            // dibutuhkan untuk pencocokan identitas & pengecekan anti-spoofing final
            // sebelum absensi benar-benar dikirim di submitAttendance().
            function captureAndShowResultPhoto() {
                try {
                    const snap = document.createElement('canvas');
                    snap.width = video.videoWidth;
                    snap.height = video.videoHeight;
                    snap.getContext('2d').drawImage(video, 0, 0);
                    livenessResultPhoto.src = snap.toDataURL('image/jpeg', 0.85);
                    livenessResultPhoto.style.display = 'block';
                    webcamCapture.classList.add('show-result-photo');
                } catch (e) {
                    console.error('❌ Gagal mengambil foto hasil liveness:', e);
                }
            }

            // Helper kebalikannya: tampilkan lagi live webcam (dipakai kalau verifikasi
            // akhir gagal/ditolak dan proses liveness harus diulang dari awal).
            function hideResultPhoto() {
                livenessResultPhoto.style.display = 'none';
                webcamCapture.classList.remove('show-result-photo');
            }

            const RING_RADIUS = 82;
            const RING_CIRC = 2 * Math.PI * RING_RADIUS;
            ringFill.style.strokeDasharray = RING_CIRC;
            ringFill.style.strokeDashoffset = RING_CIRC;

            function updateRingProgress(p) {
                const clamped = Math.max(0, Math.min(p, 1));
                ringFill.style.strokeDashoffset = RING_CIRC * (1 - clamped);
                if (clamped >= 1) {
                    ringFill.classList.add('done');
                }
            }

            // Estimasi arah yaw (kiri/kanan) dari 68 titik landmark wajah
            function estimateYaw(positions) {
                const jawLeft = positions[0];
                const jawRight = positions[16];
                const noseTip = positions[33];

                const faceWidth = jawRight.x - jawLeft.x;
                if (faceWidth === 0) return 0;

                const centerX = jawLeft.x + faceWidth / 2;
                const noseOffset = noseTip.x - centerX;
                const yawRatio = noseOffset / (faceWidth / 2);
                return yawRatio * 45;
            }

            // Hitung Eye Aspect Ratio (EAR) dari 6 titik landmark satu mata
            function computeEAR(eyePoints) {
                const dist = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);
                const vertical1 = dist(eyePoints[1], eyePoints[5]);
                const vertical2 = dist(eyePoints[2], eyePoints[4]);
                const horizontal = dist(eyePoints[0], eyePoints[3]);
                if (horizontal === 0) return 0.3;
                return (vertical1 + vertical2) / (2 * horizontal);
            }

            function estimateEAR(positions) {
                const leftEye = positions.slice(36, 42);
                const rightEye = positions.slice(42, 48);
                const earLeft = computeEAR(leftEye);
                const earRight = computeEAR(rightEye);
                return (earLeft + earRight) / 2;
            }

            function collectEarCalibrationSample(ear) {
                earCalibrationSamples.push(ear);
                if (earCalibrationSamples.length >= EAR_CALIBRATION_FRAMES_NEEDED) {
                    // Baseline = MEDIAN dari sampel kalibrasi (bukan MAX). Memakai MAX berisiko
                    // baseline "terkunci" ke satu frame outlier (mata melotot sesaat) sehingga
                    // closedThreshold jadi lebih tinggi dari EAR normal user sehari-hari -> blink
                    // jadi sangat sulit terpicu. Median jauh lebih representatif & stabil.
                    const sorted = [...earCalibrationSamples].sort((a, b) => a - b);
                    const mid = Math.floor(sorted.length / 2);
                    earBaseline = sorted.length % 2 !== 0
                        ? sorted[mid]
                        : (sorted[mid - 1] + sorted[mid]) / 2;
                    console.log('✅ Kalibrasi EAR selesai, baseline (median):', earBaseline.toFixed(3));
                }
            }

            // Ratio efektif memperhitungkan blinkRelaxLevel, supaya kalau user sudah mencoba
            // lama dan belum lolos, threshold ikut melonggar bertahap otomatis.
            function getEffectiveClosedRatio() {
                return EAR_CLOSED_RATIO + (blinkRelaxLevel * 0.02);
            }
            function getEffectiveOpenRatio() {
                return EAR_OPEN_RATIO - (blinkRelaxLevel * 0.01);
            }

            function processBlinkDetection(ear) {
                const closedThreshold = earBaseline !== null
                    ? earBaseline * getEffectiveClosedRatio()
                    : EAR_CLOSED_THRESHOLD_FALLBACK;
                const openThreshold = earBaseline !== null
                    ? earBaseline * getEffectiveOpenRatio()
                    : EAR_OPEN_THRESHOLD_FALLBACK;

                if (eyeState === 'open' && ear < closedThreshold) {
                    eyeState = 'closed';
                } else if (eyeState === 'closed' && ear > openThreshold) {
                    eyeState = 'open';
                    blinkCount++;
                }

                if (earBaseline !== null && eyeState === 'open' && ear > openThreshold) {
                    earBaseline = earBaseline * (1 - EAR_BASELINE_ADAPT_RATE) + ear * EAR_BASELINE_ADAPT_RATE;
                }

                if (earDebugText) {
                    earDebugText.textContent =
                        `EAR:${ear.toFixed(3)} Baseline:${earBaseline !== null ? earBaseline.toFixed(3) : '-'} ` +
                        `Tutup<${closedThreshold.toFixed(3)} Buka>${openThreshold.toFixed(3)} State:${eyeState} ` +
                        `Kedip:${blinkCount} Relax:${blinkRelaxLevel}`;
                }
            }

            // ══════════════════════════════
            // ✅ OPSI 4: FFT 2D ringan (Cooley-Tukey radix-2) untuk analisis moire
            // ══════════════════════════════
            // FFT 1D in-place, panjang array harus pangkat 2.
            function fft1d(re, im) {
                const n = re.length;
                for (let i = 1, j = 0; i < n; i++) {
                    let bit = n >> 1;
                    for (; j & bit; bit >>= 1) j ^= bit;
                    j ^= bit;
                    if (i < j) {
                        const tr = re[i]; re[i] = re[j]; re[j] = tr;
                        const ti = im[i]; im[i] = im[j]; im[j] = ti;
                    }
                }
                for (let len = 2; len <= n; len <<= 1) {
                    const ang = -2 * Math.PI / len;
                    const wr0 = Math.cos(ang), wi0 = Math.sin(ang);
                    for (let i = 0; i < n; i += len) {
                        let curWr = 1, curWi = 0;
                        const half = len / 2;
                        for (let j = 0; j < half; j++) {
                            const ur = re[i + j], ui = im[i + j];
                            const vr = re[i + j + half] * curWr - im[i + j + half] * curWi;
                            const vi = re[i + j + half] * curWi + im[i + j + half] * curWr;
                            re[i + j] = ur + vr; im[i + j] = ui + vi;
                            re[i + j + half] = ur - vr; im[i + j + half] = ui - vi;
                            const nextWr = curWr * wr0 - curWi * wi0;
                            const nextWi = curWr * wi0 + curWi * wr0;
                            curWr = nextWr; curWi = nextWi;
                        }
                    }
                }
            }

            // Crop kotak wajah dari `source` (video atau canvas), downsample ke
            // MOIRE_PATCH_SIZE x MOIRE_PATCH_SIZE, lalu ubah ke grayscale.
            function extractGrayscalePatch(source, box) {
                const size = Math.max(box.width, box.height, 1);
                const cx = box.x + box.width / 2;
                const cy = box.y + box.height / 2;
                const sx = Math.max(0, cx - size / 2);
                const sy = Math.max(0, cy - size / 2);

                moireCtx.clearRect(0, 0, MOIRE_PATCH_SIZE, MOIRE_PATCH_SIZE);
                moireCtx.drawImage(source, sx, sy, size, size, 0, 0, MOIRE_PATCH_SIZE, MOIRE_PATCH_SIZE);
                const imgData = moireCtx.getImageData(0, 0, MOIRE_PATCH_SIZE, MOIRE_PATCH_SIZE).data;

                const gray = new Float64Array(MOIRE_PATCH_SIZE * MOIRE_PATCH_SIZE);
                for (let i = 0; i < gray.length; i++) {
                    const o = i * 4;
                    gray[i] = 0.299 * imgData[o] + 0.587 * imgData[o + 1] + 0.114 * imgData[o + 2];
                }
                return gray;
            }

            // FFT 2D (baris lalu kolom) + windowing Hann untuk kurangi artefak tepi,
            // hasil: magnitude spectrum dengan DC di tengah (fftshift).
            function fft2dMagnitude(gray, size) {
                const re = new Float64Array(size * size);
                const im = new Float64Array(size * size);
                re.set(gray);

                for (let y = 0; y < size; y++) {
                    const wy = 0.5 - 0.5 * Math.cos(2 * Math.PI * y / (size - 1));
                    for (let x = 0; x < size; x++) {
                        const wx = 0.5 - 0.5 * Math.cos(2 * Math.PI * x / (size - 1));
                        re[y * size + x] *= wx * wy;
                    }
                }

                for (let y = 0; y < size; y++) {
                    fft1d(re.subarray(y * size, y * size + size), im.subarray(y * size, y * size + size));
                }

                const colRe = new Float64Array(size);
                const colIm = new Float64Array(size);
                for (let x = 0; x < size; x++) {
                    for (let y = 0; y < size; y++) {
                        colRe[y] = re[y * size + x];
                        colIm[y] = im[y * size + x];
                    }
                    fft1d(colRe, colIm);
                    for (let y = 0; y < size; y++) {
                        re[y * size + x] = colRe[y];
                        im[y * size + x] = colIm[y];
                    }
                }

                const mag = new Float64Array(size * size);
                const half = size / 2;
                for (let y = 0; y < size; y++) {
                    for (let x = 0; x < size; x++) {
                        const m = Math.hypot(re[y * size + x], im[y * size + x]);
                        const sy = (y + half) % size;
                        const sx = (x + half) % size;
                        mag[sy * size + sx] = m;
                    }
                }
                return mag;
            }

            // Ekstrak fitur dari magnitude spectrum:
            // - highFreqRatio: porsi energi di cincin frekuensi tinggi terhadap total energi.
            //   Layar yang direkam ulang menghasilkan noise/aliasing frekuensi tinggi yang
            //   tidak muncul pada tekstur kulit wajah asli.
            // - peakRatio: rasio puncak-terhadap-rata-rata di cincin frekuensi menengah.
            //   Moire menghasilkan puncak periodik yang tajam (grid piksel layar),
            //   berbeda dari spektrum wajah asli yang melandai halus.
            function computeMoireFeatures(mag, size) {
                const cx = size / 2, cy = size / 2;
                const lowR = size * MOIRE_LOW_FREQ_SKIP_RATIO;
                const midRInner = size * MOIRE_MID_BAND_INNER_RATIO;
                const midROuter = size * MOIRE_MID_BAND_OUTER_RATIO;
                const highR = size * MOIRE_HIGH_BAND_RATIO;

                let totalEnergy = 0;
                let highEnergy = 0;
                let midSum = 0;
                let midMax = 0;
                let midCount = 0;

                for (let y = 0; y < size; y++) {
                    for (let x = 0; x < size; x++) {
                        const dx = x - cx, dy = y - cy;
                        const r = Math.sqrt(dx * dx + dy * dy);
                        if (r < lowR) continue;

                        const v = mag[y * size + x];
                        totalEnergy += v;
                        if (r >= highR) highEnergy += v;
                        if (r >= midRInner && r < midROuter) {
                            midSum += v;
                            if (v > midMax) midMax = v;
                            midCount++;
                        }
                    }
                }

                const highFreqRatio = totalEnergy > 0 ? highEnergy / totalEnergy : 0;
                const midMean = midCount > 0 ? midSum / midCount : 0;
                const peakRatio = midMean > 0 ? midMax / midMean : 0;

                return { highFreqRatio, peakRatio };
            }

            let moireDebugTickThrottle = 0;

            // Fungsi utama: crop wajah dari `source` (video/canvas) di `box`, jalankan
            // FFT, dan kembalikan apakah pola frekuensinya mencurigakan (mirip layar).
            function computeMoireSuspicion(source, box) {
                const gray = extractGrayscalePatch(source, box);
                const mag = fft2dMagnitude(gray, MOIRE_PATCH_SIZE);
                const { highFreqRatio, peakRatio } = computeMoireFeatures(mag, MOIRE_PATCH_SIZE);

                // AND (bukan OR) supaya lebih tahan false-positive: butuh energi frekuensi
                // tinggi YANG JUGA punya puncak periodik tajam, bukan sekadar wajah blur/noisy.
                const suspicious = highFreqRatio > MOIRE_HIGH_FREQ_RATIO_THRESHOLD
                    && peakRatio > MOIRE_PEAK_RATIO_THRESHOLD;

                // Debug throttle: log tiap ~1 detik saja supaya console tidak banjir.
                moireDebugTickThrottle++;
                if (moireDebugTickThrottle % 3 === 0) {
                    console.log(`🔍 Moire debug — highFreqRatio:${highFreqRatio.toFixed(3)} peakRatio:${peakRatio.toFixed(2)} suspicious:${suspicious} streak:${moireSuspicionStreak}`);
                }

                return { suspicious, highFreqRatio, peakRatio };
            }

            function resetMoireSuspicion() {
                moireSuspicionStreak = 0;
                livenessInstructionText.classList.remove('screen-warning');
                livenessHint.classList.remove('screen-warning');
            }

            // ══════════════════════════════
            // ✅ OPSI 5: Preprocessing & inference model anti-spoofing
            // ══════════════════════════════
            // Port 1:1 dari `CropImage._get_new_box` di generate_patches.py milik
            // minivision-ai — WAJIB sama persis, karena model dilatih dengan crop
            // yang dihasilkan fungsi ini (bukan crop kotak biasa).
            function getAntiSpoofCropBox(srcW, srcH, bbox, scale) {
                const x = bbox.x, y = bbox.y, boxW = bbox.width, boxH = bbox.height;
                const effScale = Math.min((srcH - 1) / boxH, Math.min((srcW - 1) / boxW, scale));

                const newWidth = boxW * effScale;
                const newHeight = boxH * effScale;
                const centerX = boxW / 2 + x;
                const centerY = boxH / 2 + y;

                let leftTopX = centerX - newWidth / 2;
                let leftTopY = centerY - newHeight / 2;
                let rightBottomX = centerX + newWidth / 2;
                let rightBottomY = centerY + newHeight / 2;

                if (leftTopX < 0) { rightBottomX -= leftTopX; leftTopX = 0; }
                if (leftTopY < 0) { rightBottomY -= leftTopY; leftTopY = 0; }
                if (rightBottomX > srcW - 1) { leftTopX -= (rightBottomX - srcW + 1); rightBottomX = srcW - 1; }
                if (rightBottomY > srcH - 1) { leftTopY -= (rightBottomY - srcH + 1); rightBottomY = srcH - 1; }

                return {
                    x: Math.round(leftTopX),
                    y: Math.round(leftTopY),
                    width: Math.round(rightBottomX - leftTopX) + 1,
                    height: Math.round(rightBottomY - leftTopY) + 1
                };
            }

            // Crop+resize ke 80x80, lalu susun tensor NCHW dalam urutan channel BGR
            // (BUKAN RGB) — model aslinya dilatih dari gambar OpenCV (BGR) tanpa
            // konversi warna, jadi urutan channel harus sama persis.
            //
            // ⚠️ PENTING (bug yang sempat bikin model selalu bilang "fake" untuk
            // wajah asli): to_tensor() versi resmi minivision-ai punya baris
            // `img.float().div(255)` yang DI-COMMENT ("modify by zkx") dan diganti
            // `img.float()` TANPA pembagian 255. Jadi model ini dilatih dengan
            // piksel RAW range [0,255], BUKAN dinormalisasi ke [0,1] seperti
            // konvensi ToTensor pada umumnya! Deskripsi di model card (HuggingFace)
            // yang bilang "range [0,1]" ternyata tidak sesuai source code aslinya.
            function buildAntiSpoofTensor(source, faceBox, srcW, srcH) {
                const crop = getAntiSpoofCropBox(srcW, srcH, faceBox, ANTISPOOF_CROP_SCALE);
                if (crop.width <= 0 || crop.height <= 0) return null;

                antiSpoofCtx.clearRect(0, 0, ANTISPOOF_INPUT_SIZE, ANTISPOOF_INPUT_SIZE);
                antiSpoofCtx.drawImage(
                    source,
                    crop.x, crop.y, crop.width, crop.height,
                    0, 0, ANTISPOOF_INPUT_SIZE, ANTISPOOF_INPUT_SIZE
                );
                const imgData = antiSpoofCtx.getImageData(0, 0, ANTISPOOF_INPUT_SIZE, ANTISPOOF_INPUT_SIZE).data;

                const size = ANTISPOOF_INPUT_SIZE * ANTISPOOF_INPUT_SIZE;
                const data = new Float32Array(3 * size);
                for (let i = 0; i < size; i++) {
                    const o = i * 4;
                    data[i] = imgData[o + 2];             // channel 0 = B, RAW [0,255], TIDAK dibagi 255
                    data[size + i] = imgData[o + 1];      // channel 1 = G, RAW [0,255]
                    data[2 * size + i] = imgData[o];      // channel 2 = R, RAW [0,255]
                }
                return new ort.Tensor('float32', data, [1, 3, ANTISPOOF_INPUT_SIZE, ANTISPOOF_INPUT_SIZE]);
            }

            function softmax3(logits) {
                const max = Math.max(logits[0], logits[1], logits[2]);
                const exps = [Math.exp(logits[0] - max), Math.exp(logits[1] - max), Math.exp(logits[2] - max)];
                const sum = exps[0] + exps[1] + exps[2];
                return [exps[0] / sum, exps[1] / sum, exps[2] / sum];
            }

            let antiSpoofDebugThrottle = 0;

            // Jalankan inference model untuk satu crop wajah. Return null kalau model
            // belum siap atau crop tidak valid (supaya caller bisa fallback ke FFT).
            async function computeAntiSpoofScore(source, faceBox, srcW, srcH) {
                if (!antiSpoofReady || !antiSpoofSession) return null;

                const tensor = buildAntiSpoofTensor(source, faceBox, srcW, srcH);
                if (!tensor) return null;

                const feeds = {};
                feeds[antiSpoofSession.inputNames[0]] = tensor;
                const results = await antiSpoofSession.run(feeds);
                const outputName = antiSpoofSession.outputNames[0];
                const logits = Array.from(results[outputName].data);
                const probs = softmax3(logits);

                antiSpoofDebugThrottle++;
                if (antiSpoofDebugThrottle % 3 === 0) {
                    console.log(`🛡️ AntiSpoof debug — real:${probs[1].toFixed(3)} fake1:${probs[0].toFixed(3)} fake2:${probs[2].toFixed(3)}`);
                    // ✅ DEBUG SEMENTARA: bandingkan box wajah asli (dari face-api) vs box crop
                    // yang dipakai untuk model, untuk mengecek kecurigaan mismatch konvensi bbox.
                    const dbgCrop = getAntiSpoofCropBox(srcW, srcH, faceBox, ANTISPOOF_CROP_SCALE);
                    console.log(`   faceBox: x=${faceBox.x.toFixed(0)} y=${faceBox.y.toFixed(0)} w=${faceBox.width.toFixed(0)} h=${faceBox.height.toFixed(0)} | cropBox: x=${dbgCrop.x} y=${dbgCrop.y} w=${dbgCrop.width} h=${dbgCrop.height} | src: ${srcW}x${srcH}`);
                }

                return { realScore: probs[1], fakeType1Score: probs[0], fakeType2Score: probs[2] };
            }

            async function loadAntiSpoofModel() {
                try {
                    ort.env.wasm.numThreads = 1; // hindari kebutuhan header COOP/COEP untuk WASM multi-thread
                    ort.env.wasm.wasmPaths = 'https://cdn.jsdelivr.net/npm/onnxruntime-web@1.19.2/dist/';
                    antiSpoofSession = await ort.InferenceSession.create(ANTISPOOF_MODEL_URL, {
                        executionProviders: ['wasm']
                    });
                    antiSpoofReady = true;
                    console.log('✅ Model anti-spoofing (MiniFASNetV2) siap');
                } catch (e) {
                    antiSpoofReady = false;
                    console.error('❌ Gagal load model anti-spoofing, fallback ke FFT moire:', e);
                }
            }

            function resetAntiSpoofSuspicion() {
                antiSpoofSuspicionStreak = 0;
                livenessInstructionText.classList.remove('screen-warning');
                livenessHint.classList.remove('screen-warning');
            }

            function updateChecklistUI(moveDone, blinkDone) {
                checkMove.classList.toggle('done', moveDone);
                checkMove.querySelector('ion-icon').setAttribute('name', moveDone ? 'checkmark-circle' : 'ellipse-outline');

                checkBlink.classList.toggle('done', blinkDone);
                checkBlink.querySelector('ion-icon').setAttribute('name', blinkDone ? 'checkmark-circle' : 'ellipse-outline');
            }

            // Jadwalkan pelonggaran threshold bertahap selama fase blink berlangsung.
            // Kalau user memang sudah berusaha kedip tapi device/kamera tidak cukup peka
            // menangkap perbedaan EAR-nya, daripada macet 25 detik penuh, kita bantu dengan
            // melonggarkan toleransi tiap beberapa detik dan kasih hint yang lebih spesifik.
            function scheduleBlinkRelaxTimer() {
                clearBlinkRelaxTimer();
                blinkRelaxTimer = setTimeout(() => {
                    if (stage !== 'liveness' || livenessSubStage !== 'blink') return;
                    if (blinkRelaxLevel < BLINK_RELAX_MAX_LEVEL) {
                        blinkRelaxLevel++;
                        console.log('⚠️ Blink belum lolos, melonggarkan threshold ke level', blinkRelaxLevel);
                        livenessHint.textContent = blinkRelaxLevel >= BLINK_RELAX_MAX_LEVEL
                            ? 'Kedipkan mata lebih pelan & agak lama, lalu buka lagi'
                            : 'Masih belum terdeteksi, coba kedipkan lebih jelas';
                    }
                    scheduleBlinkRelaxTimer(); // lanjut jadwalkan lagi (mentok di level max)
                }, BLINK_RELAX_STEP_MS);
            }

            function clearBlinkRelaxTimer() {
                if (blinkRelaxTimer) {
                    clearTimeout(blinkRelaxTimer);
                    blinkRelaxTimer = null;
                }
            }

            // ══════════════════════════════
            // ✅ LOOP CEPAT KHUSUS DETEKSI KEDIP
            // ══════════════════════════════
            let blinkLoopTimer = null;
            let blinkLoopBusy = false; // cegah tumpang-tindih (overlap) antar pemanggilan async di device lambat
            const BLINK_LOOP_MS = 90;

            function startBlinkFastLoop() {
                if (blinkLoopTimer) return;
                blinkLoopTimer = setInterval(async () => {
                    if (blinkLoopBusy) return;
                    if (inSpoofCooldown) return; // ✅ jangan proses apa pun selama jeda cooldown
                    if (stage !== 'liveness' || livenessSubStage !== 'blink') return;
                    if (!faceReady) return;

                    blinkLoopBusy = true;
                    try {
                        let result;
                        try {
                            result = await faceapi
                                .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                                    inputSize: 320,
                                    scoreThreshold: 0.3
                                }))
                                .withFaceLandmarks();
                        } catch (e) {
                            return;
                        }

                        if (!result) return;

                        // ✅ TAMBAHAN: cek anti-spoof JUGA selama fase blink, bukan hanya
                        // fase gerakan kepala. Ini menutup celah foto/layar yang lolos
                        // kedip karena anti-spoof belum pernah dicek sama sekali di sini.
                        if (antiSpoofReady) {
                            antiSpoofTickCounter++;
                            if (antiSpoofTickCounter % ANTISPOOF_CHECK_EVERY_N_TICKS === 0) {
                                let antiSpoofResult = null;
                                try {
                                    antiSpoofResult = await computeAntiSpoofScore(
                                        video, result.detection.box, video.videoWidth, video.videoHeight
                                    );
                                } catch (e) {
                                    console.error('AntiSpoof inference error (blink phase):', e);
                                }

                                if (antiSpoofResult && antiSpoofResult.realScore < ANTISPOOF_REAL_THRESHOLD) {
                                    antiSpoofSuspicionStreak++;
                                    livenessInstructionText.innerHTML = '🖥️ Sepertinya Anda menunjukkan layar/rekaman/foto';
                                    livenessInstructionText.classList.add('screen-warning');
                                    livenessHint.textContent =
                                        `Terdeteksi kemungkinan spoof (skor asli: ${(antiSpoofResult.realScore * 100).toFixed(0)}%). Gunakan wajah asli langsung.`;
                                    livenessHint.classList.add('screen-warning');

                                    if (antiSpoofSuspicionStreak >= ANTISPOOF_SUSPICION_LIMIT) {
                                        // ✅ Jeda rapi dulu, baru mengulang dari awal (bukan reset seketika)
                                        triggerSpoofCooldownThenReset('🖥️ Terdeteksi foto/layar/rekaman, bukan wajah asli. Mengulang sebentar lagi...');
                                    }
                                    return; // jangan proses blink kalau sedang mencurigakan
                                } else if (antiSpoofSuspicionStreak > 0) {
                                    resetAntiSpoofSuspicion();
                                }
                            }
                        } else {
                            // fallback FFT moire kalau model ONNX gagal load
                            moireTickCounter++;
                            if (moireTickCounter % MOIRE_CHECK_EVERY_N_TICKS === 0) {
                                const moireResult = computeMoireSuspicion(video, result.detection.box);
                                if (moireResult.suspicious) {
                                    moireSuspicionStreak++;
                                    livenessInstructionText.innerHTML = '🖥️ Sepertinya Anda menunjukkan layar/rekaman';
                                    livenessInstructionText.classList.add('screen-warning');
                                    livenessHint.textContent = 'Gunakan wajah asli langsung di depan kamera.';
                                    livenessHint.classList.add('screen-warning');

                                    if (moireSuspicionStreak >= MOIRE_SUSPICION_LIMIT) {
                                        // ✅ Jeda rapi dulu, baru mengulang dari awal
                                        triggerSpoofCooldownThenReset('🖥️ Terdeteksi foto/layar/rekaman, bukan wajah asli. Mengulang sebentar lagi...');
                                    }
                                    return;
                                } else if (moireSuspicionStreak > 0) {
                                    resetMoireSuspicion();
                                }
                            }
                        }

                        const ear = estimateEAR(result.landmarks.positions);

                        if (earBaseline === null) {
                            collectEarCalibrationSample(ear);
                            livenessInstructionText.innerHTML = 'Tatap kamera dengan mata terbuka<br>sebentar...';
                            livenessHint.textContent = 'Sedang menyesuaikan sensor mata';
                            if (earBaseline !== null) {
                                livenessInstructionText.innerHTML = 'Kedipkan mata Anda sekali.';
                                livenessHint.textContent = 'Tatap kamera secara normal';
                                scheduleBlinkRelaxTimer();
                            }
                            return;
                        }

                        processBlinkDetection(ear);

                        const blinkDone = blinkCount >= BLINK_REQUIRED;
                        updateChecklistUI(false, blinkDone);

                        if (blinkDone) {
                            stopBlinkFastLoop();
                            clearBlinkRelaxTimer();

                            blinkIndicator.style.display = 'none';
                            updateChecklistUI(false, true);
                            startMoveStage();
                        }
                    } finally {
                        blinkLoopBusy = false;
                    }
                }, BLINK_LOOP_MS);
            }
            function stopBlinkFastLoop() {
                if (blinkLoopTimer) {
                    clearInterval(blinkLoopTimer);
                    blinkLoopTimer = null;
                }
            }

            // ══════════════════════════════
            // ✅ OPSI 1: MULAI SUB-TAHAP GERAKAN KEPALA TERARAH
            // ══════════════════════════════
            function startMoveStage() {
                livenessSubStage = 'move';
                moveReachedTarget = false;

                // Arah dipilih ACAK setiap sesi agar tidak bisa ditebak/diprediksi sebelumnya
                targetYawSign = Math.random() < 0.5 ? 1 : -1;

                ringFill.classList.add('phase-move');
                livenessCircleWrap.classList.add('pulsing');
                updateRingProgress(0.5);

                directionArrow.style.display = 'flex';
                directionArrow.classList.toggle('arrow-left', targetYawSign < 0);
                directionArrow.querySelector('ion-icon')
                    .setAttribute('name', targetYawSign > 0 ? 'arrow-forward-outline' : 'arrow-back-outline');

                livenessMinYaw = 999;
                livenessMaxYaw = -999;

                livenessInstructionText.innerHTML = 'Ikuti arah panah,<br>lalu kembalikan wajah ke tengah.';
                livenessHint.textContent = 'Pastikan wajah tetap di tengah kamera';
            }

            function transitionToLiveness() {
                if (livenessStageStarted) return; // guard: cuma dijalankan sekali
                livenessStageStarted = true;
                stage = 'liveness';
                stageLivenessOverlay.style.display = 'flex';
                webcamCapture.classList.add('stage-liveness-active');
                livenessMinYaw = 999;
                livenessMaxYaw = -999;
                updateRingProgress(0);

                // Selalu mulai dari sub-tahap "kedip" dulu
                livenessSubStage = 'blink';
                eyeState = 'open';
                blinkCount = 0;
                earBaseline = null;
                earCalibrationSamples = [];
                moveReachedTarget = false;
                blinkRelaxLevel = 0;
                resetMoireSuspicion();
                resetAntiSpoofSuspicion();
                updateChecklistUI(false, false);

                livenessRing.style.display = 'block';
                ringFill.classList.remove('phase-move', 'done');
                blinkIndicator.style.display = 'flex';
                directionArrow.style.display = 'none';
                livenessCheckBadge.classList.remove('show');
                livenessCircleWrap.classList.add('pulsing');
                livenessInstructionText.innerHTML = 'Kedipkan mata Anda sekali.';
                livenessHint.textContent = 'Tatap kamera secara normal';

                startBlinkFastLoop();

                clearTimeout(livenessTimeoutTimer);
                livenessTimeoutTimer = setTimeout(() => {
                    if (stage === 'liveness' && !livenessPassed) {
                        livenessHint.textContent = 'Belum terdeteksi gerakan/kedipan. Coba ulangi dengan lebih jelas.';
                    }
                }, LIVENESS_TIMEOUT_MS);
            }

            // Reset total progress liveness (dipakai saat identitas mismatch berkepanjangan,
            // atau dipanggil otomatis oleh triggerSpoofCooldownThenReset() setelah jeda selesai)
            // -> kembali ke sub-tahap "kedip"
            function resetLivenessProgress() {
                livenessMinYaw = 999;
                livenessMaxYaw = -999;
                eyeState = 'open';
                blinkCount = 0;
                earBaseline = null;
                earCalibrationSamples = [];
                livenessSubStage = 'blink';
                moveReachedTarget = false;
                blinkRelaxLevel = 0;
                clearBlinkRelaxTimer();
                resetMoireSuspicion();
                resetAntiSpoofSuspicion();

                ringFill.classList.remove('phase-move', 'done');
                blinkIndicator.style.display = 'flex';
                directionArrow.style.display = 'none';
                livenessCheckBadge.classList.remove('show');
                livenessCircleWrap.classList.add('pulsing');
                updateRingProgress(0);
                updateChecklistUI(false, false);
                livenessInstructionText.innerHTML = 'Kedipkan mata Anda sekali.';
                livenessHint.textContent = 'Tatap kamera secara normal';
                startBlinkFastLoop();

                clearTimeout(livenessTimeoutTimer);
                livenessTimeoutTimer = setTimeout(() => {
                    if (stage === 'liveness' && !livenessPassed) {
                        livenessHint.textContent = 'Belum terdeteksi gerakan/kedipan. Coba ulangi dengan lebih jelas.';
                    }
                }, LIVENESS_TIMEOUT_MS);
            }

            function showBlockedNoFaceRegistered() {
                stage = 'blocked';
                clearTimeout(livenessTimeoutTimer);
                clearBlinkRelaxTimer();
                stopBlinkFastLoop();
                stageLivenessOverlay.style.display = 'none';
                stageBlockedOverlay.style.display = 'flex';
                webcamCapture.classList.remove('stage-liveness-active');
            }

            function transitionToReady() {
                stage = 'ready';
                livenessPassed = true;
                clearTimeout(livenessTimeoutTimer);
                clearBlinkRelaxTimer();
                stopBlinkFastLoop();

                stageLivenessOverlay.style.display = 'none';
                webcamCapture.classList.remove('stage-liveness-active');
                livenessSuccessBadge.style.display = 'flex';
                stageReadyMap.style.display = 'flex';

                // ✅ Foto hasil liveness ditampilkan menggantikan live webcam mulai
                // dari sini. Live video tetap jalan diam-diam untuk kebutuhan
                // pencocokan identitas + anti-spoofing final di submitAttendance().
                captureAndShowResultPhoto();

                // ✅ Tombol manual TIDAK langsung ditampilkan — absensi akan terkirim
                // otomatis begitu lokasi didapat. Tombol hanya muncul sebagai fallback
                // lewat showRetryButton() kalau ada kegagalan di tengah jalan.
                stageReadyButton.style.display = 'none';
                attendanceAutoTriggered = false;
                attendanceSubmitting = false;
                setAutoStatus('Mendapatkan lokasi Anda...', 'location-outline');

                initMapIfNeeded();
            }

            // ══════════════════════════════
            // LOAD MODEL + KAMERA
            // ══════════════════════════════
            async function loadFaceAPI() {
                const MODEL_URL = '/models';
                try {
                    faceStatus.textContent = '⏳ Memuat model...';
                    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                    faceReady = true;
                    faceStatus.textContent = '✅ Model siap';
                    console.log("✅ Face API Ready");
                    await loadSiswaDescriptors();
                } catch (e) {
                    faceStatus.textContent = '❌ Gagal load model';
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
                    videoCircleClone.srcObject = stream;
                    video.onloadedmetadata = () => {
                        video.play();
                        videoCircleClone.play();
                        console.log("✅ Kamera aktif");
                    };
                } catch (err) {
                    console.error("❌ Kamera error:", err);
                    alert("Kamera tidak bisa diakses! Izinkan permission kamera.");
                }
            }

            window.addEventListener('load', async () => {
                await loadFaceAPI();
                loadAntiSpoofModel(); // tidak perlu ditunggu (await) — biar tidak menunda kamera nyala
                await startCamera();
            });

            // ══════════════════════════════
            // LOAD DESCRIPTOR SISWA DARI DB
            // ══════════════════════════════
            async function loadSiswaDescriptors() {
                try {
                    const res = await fetch('/siswa/face-descriptors');
                    const data = await res.json();

                    if (data.length === 0) {
                        console.warn("⚠️ Belum ada siswa yang mendaftarkan wajah");
                        return;
                    }

                    labeledDescriptors = data.map(siswa => {
                        const descriptorArray = new Float32Array(siswa.face_descriptor);
                        return new faceapi.LabeledFaceDescriptors(
                            siswa.nama + '|' + siswa.nis,
                            [descriptorArray]
                        );
                    });

                    faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.5);
                    console.log(`✅ ${data.length} wajah siswa berhasil dimuat`);

                    myDescriptorRegistered = data.some(s => String(s.nis) === String(nisLogin));
                    if (!myDescriptorRegistered) {
                        showBlockedNoFaceRegistered();
                    }
                } catch (e) {
                    console.error("❌ Gagal load descriptor siswa:", e);
                    showBlockedNoFaceRegistered();
                }
            }

            // ══════════════════════════════
            // DETEKSI WAJAH (loop utama, menangani semua tahap)
            // ══════════════════════════════
            video.addEventListener('play', () => {
                // ✅ Tahap framing/bingkai dihapus -> langsung mulai liveness begitu video jalan.
                // startBlinkFastLoop() di dalamnya sudah aman dipanggil sebelum model selesai
                // load, karena loop tsb mengecek faceReady di setiap tick dan menunggu sampai true.
                transitionToLiveness();

                const canvas = faceapi.createCanvasFromMedia(video);
                canvas.classList.add('face-canvas'); // ✅ dipakai CSS untuk sembunyikan saat foto hasil ditampilkan
                document.getElementById('webcamCapture').append(canvas);

                const displaySize = {
                    width: video.videoWidth,
                    height: video.videoHeight
                };

                canvas.width = displaySize.width;
                canvas.height = displaySize.height;
                faceapi.matchDimensions(canvas, displaySize);

                // Guard anti-overlap untuk loop utama. Kalau device lambat, beberapa pemanggilan
                // detectAllFaces() bisa numpuk berjalan bersamaan dan makin membebani CPU/GPU
                // -> memperlambat loop kedip 90ms yang jalan paralel di fase blink.
                let mainLoopBusy = false;

                setInterval(async () => {
                    if (!faceReady) return;
                    if (stage === 'blocked') return;
                    if (inSpoofCooldown) return; // ✅ jangan proses apa pun selama jeda cooldown
                    if (mainLoopBusy) return; // frame sebelumnya masih diproses, jangan numpuk

                    // Saat sedang di sub-tahap "blink", biarkan startBlinkFastLoop() (90ms, ringan:
                    // detectSingleFace + landmarks saja) yang bekerja SENDIRIAN. Loop utama ini
                    // menjalankan detectAllFaces() + withFaceDescriptors() yang jauh lebih berat
                    // setiap 300ms — kalau jalan bersamaan dengan loop kedip, device jadi keteteran.
                    if (stage === 'liveness' && livenessSubStage === 'blink') return;

                    mainLoopBusy = true;
                    try {
                        const detections = await faceapi
                            .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({
                                inputSize: 416,
                                scoreThreshold: 0.3
                            }))
                            .withFaceLandmarks()
                            .withFaceDescriptors();

                        const resized = faceapi.resizeResults(detections, displaySize);

                        // ── TAHAP LIVENESS (kedip -> gerakan kepala terarah) ──
                        if (stage === 'liveness') {
                            if (resized.length === 1) {
                                const detection = resized[0];

                                const ear = estimateEAR(detection.landmarks.positions);

                                const safetyClosedThreshold = earBaseline !== null
                                    ? earBaseline * getEffectiveClosedRatio()
                                    : EAR_CLOSED_THRESHOLD_FALLBACK;
                                const eyesOpenEnoughForIdentityCheck = ear >= (safetyClosedThreshold + 0.03);

                                if (eyesOpenEnoughForIdentityCheck) {
                                    let identityOk = false;
                                    if (faceMatcher) {
                                        const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                                        const isUnknown = bestMatch.label === 'unknown';
                                        const matchedNis = isUnknown ? null : bestMatch.label.split('|')[1];
                                        identityOk = (matchedNis === nisLogin);
                                    }

                                    if (!identityOk) {
                                        livenessMismatchStreak++;
                                        livenessInstructionText.innerHTML = '❌ Wajah tidak sesuai dengan akun Anda';
                                        livenessInstructionText.classList.add('mismatch');
                                        livenessHint.textContent = 'Pastikan wajah Anda sendiri yang terlihat di kamera.';
                                        livenessHint.classList.add('mismatch');

                                        if (livenessMismatchStreak >= LIVENESS_MISMATCH_LIMIT) {
                                            resetLivenessProgress();
                                        }
                                        return;
                                    }

                                    livenessMismatchStreak = 0;
                                    livenessInstructionText.classList.remove('mismatch');
                                    livenessHint.classList.remove('mismatch');
                                }

                                // ── OPSI 5: CEK ANTI-SPOOFING (model ONNX, fallback ke FFT moire) ──
                                // Dijalankan tiap beberapa tick loop utama (di luar sub-tahap "blink"
                                // yang ditangani loop 90ms terpisah demi performa). Prioritas: model
                                // MiniFASNetV2 yang terlatih; kalau modelnya gagal dimuat (mis. CDN
                                // diblokir jaringan sekolah), otomatis fallback ke heuristik FFT moire.
                                if (antiSpoofReady) {
                                    antiSpoofTickCounter++;
                                    if (antiSpoofTickCounter % ANTISPOOF_CHECK_EVERY_N_TICKS === 0) {
                                        let antiSpoofResult = null;
                                        try {
                                            antiSpoofResult = await computeAntiSpoofScore(
                                                video, detection.detection.box, displaySize.width, displaySize.height
                                            );
                                        } catch (e) {
                                            console.error('AntiSpoof inference error:', e);
                                        }

                                        if (antiSpoofResult) {
                                            const suspicious = antiSpoofResult.realScore < ANTISPOOF_REAL_THRESHOLD;

                                            if (suspicious) {
                                                antiSpoofSuspicionStreak++;
                                                livenessInstructionText.innerHTML = '🖥️ Sepertinya Anda menunjukkan layar/rekaman/foto';
                                                livenessInstructionText.classList.add('screen-warning');
                                                livenessHint.textContent =
                                                    `Model anti-spoofing mendeteksi kemungkinan spoof (skor asli: ${(antiSpoofResult.realScore * 100).toFixed(0)}%). Gunakan wajah asli langsung di depan kamera.`;
                                                livenessHint.classList.add('screen-warning');

                                                if (antiSpoofSuspicionStreak >= ANTISPOOF_SUSPICION_LIMIT) {
                                                    // ✅ Jeda rapi dulu, baru mengulang dari awal
                                                    triggerSpoofCooldownThenReset('🖥️ Terdeteksi foto/layar/rekaman, bukan wajah asli. Mengulang sebentar lagi...');
                                                }
                                                return;
                                            }

                                            if (antiSpoofSuspicionStreak > 0) {
                                                resetAntiSpoofSuspicion();
                                            }
                                        }
                                    }
                                } else {
                                    // Fallback: FFT moire (dipakai HANYA kalau model ONNX gagal dimuat)
                                    moireTickCounter++;
                                    if (moireTickCounter % MOIRE_CHECK_EVERY_N_TICKS === 0) {
                                        const moireResult = computeMoireSuspicion(video, detection.detection.box);

                                        if (moireResult.suspicious) {
                                            moireSuspicionStreak++;
                                            livenessInstructionText.innerHTML = '🖥️ Sepertinya Anda menunjukkan layar/rekaman';
                                            livenessInstructionText.classList.add('screen-warning');
                                            livenessHint.textContent = 'Gunakan wajah asli langsung di depan kamera, bukan foto/video di layar.';
                                            livenessHint.classList.add('screen-warning');

                                            if (moireSuspicionStreak >= MOIRE_SUSPICION_LIMIT) {
                                                // ✅ Jeda rapi dulu, baru mengulang dari awal
                                                triggerSpoofCooldownThenReset('🖥️ Terdeteksi foto/layar/rekaman, bukan wajah asli. Mengulang sebentar lagi...');
                                            }
                                            return;
                                        }

                                        if (moireSuspicionStreak > 0) {
                                            resetMoireSuspicion();
                                        }
                                    }
                                }

                                // SUB-TAHAP 1: KEDIPAN MATA — ditangani oleh startBlinkFastLoop
                                // (loop ini sudah return lebih awal di atas kalau sub-tahap = blink,
                                // baris di bawah ini praktis tidak akan tereksekusi lagi, dibiarkan
                                // sebagai safety-net kalau ada race condition transisi sub-tahap)
                                if (livenessSubStage === 'blink') {
                                    return;
                                }

                                // SUB-TAHAP 2 (OPSI 1 + OPSI 3): GERAKAN KEPALA TERARAH
                                // + KEMBALI KE TENGAH, dengan guard rasio geometri wajah.
                                const box = detection.detection.box;
                                const faceBoxRatio = box.width / box.height;
                                const geometryLooksNatural =
                                    faceBoxRatio >= FACE_BOX_RATIO_MIN && faceBoxRatio <= FACE_BOX_RATIO_MAX;

                                if (!geometryLooksNatural) {
                                    // Kemungkinan foto/kartu yang dimiringkan di depan kamera
                                    // (bukan kepala 3D asli yang menoleh) -> jangan hitung progress.
                                    livenessInstructionText.innerHTML = 'Pastikan wajah menghadap kamera dengan wajar.';
                                    livenessHint.textContent = 'Gerakan tidak terdeteksi wajar, coba lagi';
                                    return;
                                }

                                const yaw = estimateYaw(detection.landmarks.positions);
                                livenessMinYaw = Math.min(livenessMinYaw, yaw);
                                livenessMaxYaw = Math.max(livenessMaxYaw, yaw);

                                // Progress menuju target arah yang diinstruksikan (bukan swing bebas ke arah manapun)
                                const signedProgressToTarget = targetYawSign > 0
                                    ? Math.min(Math.max(yaw, 0) / YAW_TARGET_THRESHOLD, 1)
                                    : Math.min(Math.max(-yaw, 0) / YAW_TARGET_THRESHOLD, 1);

                                if (signedProgressToTarget >= 1) {
                                    moveReachedTarget = true;
                                }

                                // Setelah mencapai target, wajib kembali mendekati tengah
                                // (memastikan gerakan bolak-balik alami, bukan kemiringan statis yang ditahan)
                                let returnProgress = 0;
                                if (moveReachedTarget) {
                                    const range = Math.max(YAW_TARGET_THRESHOLD - YAW_RETURN_THRESHOLD, 1);
                                    returnProgress = Math.min(Math.max(YAW_TARGET_THRESHOLD - Math.abs(yaw), 0) / range, 1);
                                }

                                const moveProgress = moveReachedTarget
                                    ? 0.5 + 0.5 * returnProgress
                                    : 0.5 * signedProgressToTarget;
                                updateRingProgress(0.3 + moveProgress * 0.7);

                                const moveDone = moveReachedTarget && Math.abs(yaw) <= YAW_RETURN_THRESHOLD;
                                updateChecklistUI(moveDone, true);

                                if (!moveReachedTarget) {
                                    livenessInstructionText.innerHTML = 'Ikuti arah panah,<br>lalu kembalikan wajah ke tengah.';
                                    livenessHint.textContent = 'Pastikan wajah tetap di tengah kamera';
                                } else if (!moveDone) {
                                    livenessInstructionText.innerHTML = 'Bagus, sekarang kembalikan<br>wajah ke tengah.';
                                    livenessHint.textContent = 'Pastikan wajah tetap di tengah kamera';
                                } else {
                                    livenessInstructionText.innerHTML = '✅ Verifikasi berhasil!';
                                    livenessHint.textContent = '';
                                    ringFill.classList.add('done');
                                    livenessCircleWrap.classList.remove('pulsing');
                                    livenessCheckBadge.classList.add('show');
                                    directionArrow.style.display = 'none';
                                }

                                if (moveDone && !livenessPassed) {
                                    setTimeout(() => transitionToReady(), 500);
                                }
                            } else {
                                livenessInstructionText.innerHTML = 'Pastikan hanya wajah Anda yang terlihat di kamera.';
                            }
                            return;
                        }

                        // ── TAHAP 3: READY (logika absen normal, sama seperti sebelumnya) ──
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        detectedNis = null;
                        lastDetection = null;

                        resized.forEach(detection => {
                            const box = detection.detection.box;

                            if (faceMatcher) {
                                const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                                const isUnknown = bestMatch.label === 'unknown';
                                const parts = bestMatch.label.split('|');

                                const namaLabel = isUnknown ? '❓ Tidak Dikenal' : `✅ ${parts[0]}`;
                                const color = isUnknown ? '#ff3333' : '#00cc44';
                                const score = ((1 - bestMatch.distance) * 100).toFixed(0);

                                if (!isUnknown) {
                                    detectedNis = parts[1];
                                    lastDetection = detection;
                                }

                                ctx.strokeStyle = color;
                                ctx.lineWidth = 3;
                                ctx.strokeRect(box.x, box.y, box.width, box.height);

                                ctx.fillStyle = color;
                                ctx.fillRect(box.x, box.y - 30, box.width, 30);

                                ctx.fillStyle = '#ffffff';
                                ctx.font = 'bold 14px Arial';
                                ctx.fillText(namaLabel, box.x + 6, box.y - 10);

                                if (!isUnknown) {
                                    ctx.fillStyle = color;
                                    ctx.font = '11px Arial';
                                    ctx.fillText(`Kecocokan: ${score}%`, box.x + 6, box.y + box.height + 15);
                                }
                            } else {
                                ctx.strokeStyle = '#ffaa00';
                                ctx.lineWidth = 2;
                                ctx.strokeRect(box.x, box.y, box.width, box.height);

                                ctx.fillStyle = '#ffaa00';
                                ctx.fillRect(box.x, box.y - 26, box.width, 26);
                                ctx.fillStyle = '#000000';
                                ctx.font = 'bold 12px Arial';
                                ctx.fillText('⚠️ Belum ada data wajah', box.x + 5, box.y - 8);

                                lastDetection = detection;
                            }
                        });
                    } finally {
                        mainLoopBusy = false;
                    }
                }, 300);
            });

            // ══════════════════════════════
            // GEOLOCATION (hanya diinisialisasi setelah stage ready)
            // ══════════════════════════════
            var lokasi = document.getElementById('lokasi');
            var mapInitialized = false;

            function initMapIfNeeded() {
                if (mapInitialized) return;
                mapInitialized = true;

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
                }
            }

            function successCallback(position) {
                lokasi.value = position.coords.latitude + ',' + position.coords.longitude;

                var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
                var lat_sekolah = "{{ $lok_sekolah->latitude }}";
                var long_sekolah = "{{ $lok_sekolah->longitude }}";
                var radius = "{{ $lok_sekolah->radius }}";
                var radius = "{{ $lok_sekolah->radius }}";
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);

                L.circle([lat_sekolah, long_sekolah], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: radius
                }).addTo(map);

                // ✅ Lokasi berhasil didapat -> langsung proses absensi tanpa perlu klik tombol
                if (!attendanceAutoTriggered) {
                    attendanceAutoTriggered = true;
                    setAutoStatus('Memvalidasi wajah & mengirim absensi...', 'sync-outline');
                    submitAttendance();
                }
            }

            function errorCallback(err) {
                console.error('❌ Gagal ambil lokasi:', err);
                setAutoStatus('Gagal mendapatkan lokasi. Aktifkan GPS lalu coba lagi.', 'alert-circle-outline', true);
                showRetryButton();
            }

            // ══════════════════════════════
            // ✅ ABSEN — dipanggil OTOMATIS setelah lokasi didapat (successCallback),
            // dan juga bisa dipanggil manual lewat tombol fallback #takeabsen kalau
            // auto-submit sebelumnya gagal.
            // ══════════════════════════════
            async function submitAttendance() {
                if (attendanceSubmitting) return; // cegah submit dobel
                attendanceSubmitting = true;

                const jadwalId = $('#jadwal_id').val();

                if (!jadwalId) {
                    setAutoStatus('Jadwal absensi tidak ditemukan. Silakan refresh halaman.', 'alert-circle-outline', true);
                    attendanceSubmitting = false;
                    showRetryButton();
                    return;
                }

                const btn = $('#takeabsen');

                if (!faceReady) {
                    setAutoStatus('Model wajah belum siap, tunggu sebentar...', 'alert-circle-outline', true);
                    attendanceSubmitting = false;
                    showRetryButton();
                    return;
                }

                if (!livenessPassed) {
                    setAutoStatus('Verifikasi liveness belum selesai. Silakan ulangi.', 'alert-circle-outline', true);
                    attendanceSubmitting = false;
                    return;
                }

                if (faceMatcher && !detectedNis) {
                    setAutoStatus('Wajah tidak dikenal. Pastikan wajah terlihat jelas atau hubungi admin.', 'alert-circle-outline', true);
                    attendanceSubmitting = false;
                    showRetryButton();
                    return;
                }

                if (detectedNis && detectedNis !== nisLogin) {
                    setAutoStatus('Wajah tidak sesuai dengan akun ini. Absensi dibatalkan.', 'alert-circle-outline', true);
                    attendanceSubmitting = false;
                    showRetryButton();
                    return;
                }

                if (!lastDetection) {
                    setAutoStatus('Wajah tidak terdeteksi. Pastikan wajah terlihat jelas di kamera.', 'alert-circle-outline', true);
                    attendanceSubmitting = false;
                    showRetryButton();
                    return;
                }

                stageReadyButton.style.display = 'none';
                btn.prop('disabled', true);
                setAutoStatus('Memproses absensi...', 'sync-outline');

                const snapCanvas = document.createElement('canvas');
                snapCanvas.width = video.videoWidth;
                snapCanvas.height = video.videoHeight;
                const ctx = snapCanvas.getContext('2d');
                ctx.drawImage(video, 0, 0);

                // ── OPSI 5: SAFETY-NET terakhir sebelum submit ──
                // Cek ulang tepat di frame yang akan dikirim, pakai model ONNX kalau siap,
                // fallback ke FFT moire kalau model gagal dimuat. lastDetection.detection.box
                // valid dipakai di snapCanvas karena dimensinya sama persis dengan video native.
                let finalSuspicious = false;
                let finalReason = '';
                if (antiSpoofReady) {
                    try {
                        const finalAntiSpoof = await computeAntiSpoofScore(
                            snapCanvas, lastDetection.detection.box, snapCanvas.width, snapCanvas.height
                        );
                        if (finalAntiSpoof && finalAntiSpoof.realScore < ANTISPOOF_REAL_THRESHOLD) {
                            finalSuspicious = true;
                            finalReason = `skor asli ${(finalAntiSpoof.realScore * 100).toFixed(0)}%`;
                        }
                    } catch (e) {
                        console.error('AntiSpoof final check error:', e);
                    }
                } else {
                    const finalMoireCheck = computeMoireSuspicion(snapCanvas, lastDetection.detection.box);
                    if (finalMoireCheck.suspicious) {
                        finalSuspicious = true;
                        finalReason = 'pola frekuensi mencurigakan (FFT fallback)';
                    }
                }

                if (finalSuspicious) {
                    Swal.fire({
                        title: 'Terdeteksi Layar/Rekaman!',
                        text: `Sistem mendeteksi kemungkinan foto/video di layar, bukan wajah asli langsung (${finalReason}). Silakan ulangi verifikasi menggunakan kamera langsung.`,
                        icon: 'error'
                    });
                    attendanceSubmitting = false;
                    attendanceAutoTriggered = false;
                    resetLivenessProgress();
                    stage = 'liveness';
                    livenessPassed = false;
                    stageLivenessOverlay.style.display = 'flex';
                    webcamCapture.classList.add('stage-liveness-active');
                    hideResultPhoto(); // ✅ kembali ke live webcam karena verifikasi diulang dari awal
                    stageReadyButton.style.display = 'none';
                    stageAutoStatus.style.display = 'none';
                    stageReadyMap.style.display = 'none';
                    livenessSuccessBadge.style.display = 'none';
                    return;
                }

                const image = snapCanvas.toDataURL('image/jpeg', 0.8);
                var lokasiVal = $('#lokasi').val();

                $.ajax({
                    url: '/attendance/store',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        image: image,
                        lokasi: lokasiVal,
                        detected_nis: detectedNis,
                        liveness_passed: livenessPassed ? 1 : 0,
                        jadwal_id: jadwalId
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

                            setAutoStatus(status[1], 'checkmark-circle-outline');

                            Swal.fire({
                                title: 'Success!',
                                text: status[1],
                                icon: 'success',
                            });

                            setTimeout("location.href='/dashboard'", 3000);

                        } else {

                            attendanceSubmitting = false;

                            if (status[2] == "radius") {
                                notifikasi_radius.play();
                            }

                            setAutoStatus(status[1], 'alert-circle-outline', true);

                            // TAMBAHAN UNTUK HARI LIBUR
                            if (status[2] == "libur") {
                                Swal.fire({
                                    title: 'Hari Libur',
                                    text: status[1],
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    showRetryButton();
                                });

                                return;
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: status[1],
                                icon: 'error',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                showRetryButton();
                            });
                        }
                    },
                    error: function () {
                        attendanceSubmitting = false;
                        setAutoStatus('Terjadi kesalahan K.', 'alert-circle-outline', true);
                        Swal.fire({ title: 'Error!', text: 'Terjadi kesalahan koneksi.', icon: 'error' });
                        showRetryButton();
                    }
                });
            }

            // Tombol fallback: dipanggil manual HANYA kalau auto-submit gagal
            $(document).on('click', '#takeabsen', function () {
                attendanceSubmitting = false; // reset guard supaya retry manual bisa jalan
                submitAttendance();
            });
        @endif
    </script>
@endpush

// ini kode create.blade.php