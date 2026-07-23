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

@endsection

@section('content')

    <div class="row" style="margin-top: 70px;">
        <div class="col">
            <input type="hidden" id="lokasi">

            <div class="webcam-capture" id="webcamCapture">
                <video id="video" autoplay muted playsinline></video>

                {{-- Status kecil di pojok --}}
                <div id="face-status">⏳ Memuat model...</div>

                {{-- ✅ Debug: angka EAR/Smile real-time untuk membantu menyetel threshold.
                Hapus/hilangkan div ini kalau sudah tidak dibutuhkan lagi di production. --}}
                <div id="earDebugText" class="ear-debug"></div>

                {{-- ✅ TAHAP LIVENESS (kedip -> senyum -> gerakan kepala terarah), langsung tampil tanpa tahap framing --}}
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

                    {{-- ✅ Checklist: kedipan -> senyum -> gerakan kepala terarah --}}
                    <div class="liveness-checklist">
                        <div class="check-item" id="checkBlink">
                            <ion-icon name="ellipse-outline"></ion-icon>
                            1. Kedipkan mata Anda sekali
                        </div>
                        <div class="check-item" id="checkMouth">
                            <ion-icon name="ellipse-outline"></ion-icon>
                            2. Tersenyumlah sebentar
                        </div>
                        <div class="check-item" id="checkMove">
                            <ion-icon name="ellipse-outline"></ion-icon>
                            3. Ikuti arah panah &amp; kembali ke tengah
                        </div>
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

    {{-- ✅ Tombol & peta hanya tampil setelah liveness lolos --}}
    <div class="row mt-2" id="stageReadyButton" style="display:none;">
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

    <div class="row mt-2" id="stageReadyMap" style="display:none;">
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
        let labeledDescriptors = [];
        let faceMatcher = null;
        let detectedNis = null; // NIS siswa yang wajahnya cocok

        const video = document.getElementById('video');
        const videoCircleClone = document.getElementById('videoCircleClone');
        const faceStatus = document.getElementById('face-status');
        const earDebugText = document.getElementById('earDebugText'); // ✅ debug angka EAR/Smile real-time

        var notifikasi_in = document.getElementById('notifikasi_in');
        var notifikasi_out = document.getElementById('notifikasi_out');
        var notifikasi_radius = document.getElementById('notifikasi_radius');

        // ══════════════════════════════
        // ✅ STATE MACHINE LIVENESS
        // ══════════════════════════════
        // Tahap framing/bingkai dihapus (masih bisa ditembus foto) -> langsung mulai
        // dari liveness begitu kamera aktif dan model siap.
        // stage: 'liveness' -> 'ready'
        // livenessSubStage (di dalam 'liveness'): 'blink' -> 'mouth' -> 'move'
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

        // ✅ Sub-tahap dalam Tahap 2: 'blink' -> 'mouth' -> 'move'
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
        // ✅ OPSI 2: SENYUM challenge (Smile Ratio)
        // ══════════════════════════════
        // Sebelumnya pakai Mouth Aspect Ratio (buka mulut vertikal), tapi sering gagal
        // terdeteksi karena orang cenderung tersenyum (mulut melebar ke samping) alih-alih
        // membuka mulut lebar-lebar (mulut naik-turun). Sekarang diganti jadi Smile Ratio:
        // mengukur pelebaran sudut mulut (titik 48 & 54) relatif terhadap jarak referensi
        // skala wajah (jarak antar sudut luar mata, titik 36 & 45). Rasio ini stabil terhadap
        // jarak/kamera (sama-sama diukur dari landmark wajah) dan naik jelas saat senyum,
        // serta tetap sulit dipalsukan pakai foto statis (foto tidak bisa mengubah ekspresi).
        let mouthState = 'closed'; // 'closed' (netral) | 'open' (senyum)
        let mouthActionCount = 0;
        let mouthCheckPassed = false;
        const MOUTH_REQUIRED = 1; // minimal 1 kali senyum lalu kembali netral

        let marBaseline = null; // baseline rasio saat ekspresi netral (tidak senyum)
        let marCalibrationSamples = [];
        const MAR_CALIBRATION_FRAMES_NEEDED = 8;
        // Rasio relatif terhadap baseline (ekspresi netral), meniru pola EAR di atas.
        const SMILE_OPEN_RATIO = 1.12;  // dianggap "senyum" jika rasio >= baseline * 1.12
        const SMILE_CLOSE_RATIO = 1.05; // dianggap "kembali netral" jika rasio turun <= baseline * 1.05
        const SMILE_OPEN_THRESHOLD_FALLBACK = 0.9;
        const SMILE_CLOSE_THRESHOLD_FALLBACK = 0.8;

        const directionArrow = document.getElementById('directionArrow');

        const checkMove = document.getElementById('checkMove');
        const checkBlink = document.getElementById('checkBlink');
        const checkMouth = document.getElementById('checkMouth');
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
        // ✅ OPSI 2: SMILE RATIO — deteksi senyum
        // ══════════════════════════════
        // Rasio lebar sudut mulut (titik 48 & 54) terhadap jarak antar sudut luar
        // mata (titik 36 & 45) sebagai referensi skala wajah yang stabil (tidak
        // terpengaruh jarak wajah ke kamera seperti halnya lebar bounding-box).
        // Baseline dikalibrasi saat ekspresi netral, lalu rasio wajib naik signifikan
        // (senyum) dan turun lagi (kembali netral) untuk dianggap satu aksi yang valid.
        function estimateSmileRatio(positions) {
            const dist = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);
            const mouthCorner1 = positions[48];
            const mouthCorner2 = positions[54];
            const eyeOuterLeft = positions[36];
            const eyeOuterRight = positions[45];

            const mouthWidth = dist(mouthCorner1, mouthCorner2);
            const eyeDist = dist(eyeOuterLeft, eyeOuterRight);
            if (eyeDist === 0) return 0.3;
            return mouthWidth / eyeDist;
        }

        function collectMarCalibrationSample(ratio) {
            marCalibrationSamples.push(ratio);
            if (marCalibrationSamples.length >= MAR_CALIBRATION_FRAMES_NEEDED) {
                // Median, sama seperti baseline EAR, supaya tidak "terkunci" ke satu
                // frame outlier (misal terekam pas sedang bicara/senyum sekilas).
                const sorted = [...marCalibrationSamples].sort((a, b) => a - b);
                const mid = Math.floor(sorted.length / 2);
                marBaseline = sorted.length % 2 !== 0
                    ? sorted[mid]
                    : (sorted[mid - 1] + sorted[mid]) / 2;
                console.log('✅ Kalibrasi Smile Ratio selesai, baseline (median):', marBaseline.toFixed(3));
            }
        }

        function processMouthDetection(ratio) {
            const openThreshold = marBaseline !== null
                ? marBaseline * SMILE_OPEN_RATIO
                : SMILE_OPEN_THRESHOLD_FALLBACK;
            const closeThreshold = marBaseline !== null
                ? marBaseline * SMILE_CLOSE_RATIO
                : SMILE_CLOSE_THRESHOLD_FALLBACK;

            if (mouthState === 'closed' && ratio > openThreshold) {
                mouthState = 'open';
            } else if (mouthState === 'open' && ratio < closeThreshold) {
                mouthState = 'closed';
                mouthActionCount++;
            }

            if (earDebugText) {
                earDebugText.textContent =
                    `Smile:${ratio.toFixed(3)} Baseline:${marBaseline !== null ? marBaseline.toFixed(3) : '-'} ` +
                    `Senyum>${openThreshold.toFixed(3)} Netral<${closeThreshold.toFixed(3)} State:${mouthState} ` +
                    `Aksi:${mouthActionCount}`;
            }
        }

        function updateChecklistUI(moveDone, blinkDone, mouthDone) {
            checkMove.classList.toggle('done', moveDone);
            checkMove.querySelector('ion-icon').setAttribute('name', moveDone ? 'checkmark-circle' : 'ellipse-outline');

            checkBlink.classList.toggle('done', blinkDone);
            checkBlink.querySelector('ion-icon').setAttribute('name', blinkDone ? 'checkmark-circle' : 'ellipse-outline');

            checkMouth.classList.toggle('done', mouthDone);
            checkMouth.querySelector('ion-icon').setAttribute('name', mouthDone ? 'checkmark-circle' : 'ellipse-outline');
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
                if (blinkLoopBusy) return; // frame sebelumnya masih diproses -> lewati, jangan numpuk
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
                    updateChecklistUI(false, blinkDone, false);

                    if (blinkDone) {
                        // Kedip terdeteksi -> hentikan loop cepat, lanjut ke OPSI 2 (senyum)
                        stopBlinkFastLoop();
                        clearBlinkRelaxTimer();

                        livenessSubStage = 'mouth';
                        mouthState = 'closed';
                        mouthActionCount = 0;
                        marBaseline = null;
                        marCalibrationSamples = [];
                        blinkIndicator.style.display = 'none';
                        livenessCircleWrap.classList.add('pulsing');
                        updateRingProgress(0.3);
                        livenessInstructionText.innerHTML = 'Tersenyumlah sebentar,<br>lalu kembali ke ekspresi normal.';
                        livenessHint.textContent = 'Tatap kamera secara normal';
                        updateChecklistUI(false, true, false);
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
            mouthState = 'closed';
            mouthActionCount = 0;
            mouthCheckPassed = false;
            marBaseline = null;
            marCalibrationSamples = [];
            moveReachedTarget = false;
            blinkRelaxLevel = 0;
            updateChecklistUI(false, false, false);

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

        // Reset total progress liveness (dipakai saat identitas mismatch berkepanjangan)
        // -> kembali ke sub-tahap "kedip"
        function resetLivenessProgress() {
            livenessMinYaw = 999;
            livenessMaxYaw = -999;
            eyeState = 'open';
            blinkCount = 0;
            earBaseline = null;
            earCalibrationSamples = [];
            livenessSubStage = 'blink';
            mouthState = 'closed';
            mouthActionCount = 0;
            mouthCheckPassed = false;
            marBaseline = null;
            marCalibrationSamples = [];
            moveReachedTarget = false;
            blinkRelaxLevel = 0;
            clearBlinkRelaxTimer();

            ringFill.classList.remove('phase-move', 'done');
            blinkIndicator.style.display = 'flex';
            directionArrow.style.display = 'none';
            livenessCheckBadge.classList.remove('show');
            livenessCircleWrap.classList.add('pulsing');
            updateRingProgress(0);
            updateChecklistUI(false, false, false);
            startBlinkFastLoop();
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
            stageReadyButton.style.display = 'flex';
            stageReadyMap.style.display = 'flex';

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

                    // ── TAHAP LIVENESS (kedip -> senyum -> gerakan kepala terarah) ──
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

                            // SUB-TAHAP 1: KEDIPAN MATA — ditangani oleh startBlinkFastLoop
                            // (loop ini sudah return lebih awal di atas kalau sub-tahap = blink,
                            // baris di bawah ini praktis tidak akan tereksekusi lagi, dibiarkan
                            // sebagai safety-net kalau ada race condition transisi sub-tahap)
                            if (livenessSubStage === 'blink') {
                                return;
                            }

                            // SUB-TAHAP 2 (OPSI 2): SENYUM (Smile Ratio)
                            // Murni geometri landmark sehingga bisa langsung diproses di loop
                            // utama (300ms) tanpa perlu loop cepat terpisah seperti kedip.
                            if (livenessSubStage === 'mouth') {
                                const smileRatio = estimateSmileRatio(detection.landmarks.positions);

                                if (marBaseline === null) {
                                    collectMarCalibrationSample(smileRatio);
                                    livenessHint.textContent = 'Sedang menyesuaikan sensor senyum...';
                                    return;
                                }

                                processMouthDetection(smileRatio);

                                const mouthDone = mouthActionCount >= MOUTH_REQUIRED;
                                updateChecklistUI(false, true, mouthDone);
                                updateRingProgress(mouthDone ? 0.5 : (mouthState === 'open' ? 0.4 : 0.3));

                                if (mouthDone) {
                                    mouthCheckPassed = true;
                                    livenessInstructionText.innerHTML = '✅ Terverifikasi, lanjut...';
                                    livenessHint.textContent = '';
                                    startMoveStage();
                                }
                                return;
                            }

                            // SUB-TAHAP 3 (OPSI 1 + OPSI 3): GERAKAN KEPALA TERARAH
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
                            updateChecklistUI(moveDone, true, true);

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
            var lokasi_sekolah = "{{ $lok_sekolah->lokasi_sekolah }}";
            var lok = lokasi_sekolah.split(",");
            var lat_sekolah = lok[0];
            var long_sekolah = lok[1];
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
        }

        function errorCallback() { }

        // ══════════════════════════════
        // ABSEN
        // ══════════════════════════════
        $(document).on('click', '#takeabsen', function () {
            const btn = $(this);

            if (!faceReady) {
                Swal.fire({ title: 'Error!', text: 'Model wajah belum siap, tunggu sebentar.', icon: 'error' });
                return;
            }

            if (!livenessPassed) {
                Swal.fire({ title: 'Error!', text: 'Verifikasi liveness belum selesai. Silakan ulangi.', icon: 'error' });
                return;
            }

            if (faceMatcher && !detectedNis) {
                Swal.fire({
                    title: 'Wajah Tidak Dikenal!',
                    text: 'Wajah Anda tidak cocok dengan data siswa. Pastikan wajah terlihat jelas atau hubungi admin untuk mendaftarkan wajah.',
                    icon: 'error'
                });
                return;
            }

            if (detectedNis && detectedNis !== nisLogin) {
                Swal.fire({
                    title: 'Wajah Tidak Sesuai!',
                    text: 'Wajah yang terdeteksi bukan wajah pemilik akun ini. Absensi tidak dapat dilakukan.',
                    icon: 'error'
                });
                return;
            }

            if (!lastDetection) {
                Swal.fire({ title: 'Error!', text: 'Wajah tidak terdeteksi! Pastikan wajah terlihat jelas di kamera.', icon: 'error' });
                return;
            }

            btn.prop('disabled', true).html('<ion-icon name="hourglass-outline"></ion-icon> Memproses...');

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
                    lokasi: lokasiVal,
                    detected_nis: detectedNis,
                    liveness_passed: livenessPassed ? 1 : 0,
                    mouth_check_passed: mouthCheckPassed ? 1 : 0
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
                        }).then(() => {
                            @if($cek > 0)
                                btn.prop('disabled', false).html('<ion-icon name="camera-outline"></ion-icon> Absen Pulang');
                            @else
                                btn.prop('disabled', false).html('<ion-icon name="camera-outline"></ion-icon> Absen Masuk');
                            @endif
                                                                                                });
                    }
                },
                error: function () {
                    Swal.fire({ title: 'Error!', text: 'Terjadi kesalahan koneksi.', icon: 'error' });
                    @if($cek > 0)
                        btn.prop('disabled', false).html('<ion-icon name="camera-outline"></ion-icon> Absen Pulang');
                    @else
                        btn.prop('disabled', false).html('<ion-icon name="camera-outline"></ion-icon> Absen Masuk');
                    @endif
                                                                                        }
            });
        });
    </script>
@endpush