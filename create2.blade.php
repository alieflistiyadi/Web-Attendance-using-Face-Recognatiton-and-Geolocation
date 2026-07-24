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

        /* ── Tahap 1: Frame guide (bingkai) ── */
        .frame-guide-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 20;
            pointer-events: none;
        }

        .frame-corners {
            position: relative;
            width: 160px;
            height: 200px;
            margin-bottom: 16px;
        }

        .frame-corners span {
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: #fff;
            border-style: solid;
        }

        .frame-corners span:nth-child(1) {
            top: 0;
            left: 0;
            border-width: 3px 0 0 3px;
            border-radius: 8px 0 0 0;
        }

        .frame-corners span:nth-child(2) {
            top: 0;
            right: 0;
            border-width: 3px 3px 0 0;
            border-radius: 0 8px 0 0;
        }

        .frame-corners span:nth-child(3) {
            bottom: 0;
            left: 0;
            border-width: 0 0 3px 3px;
            border-radius: 0 0 0 8px;
        }

        .frame-corners span:nth-child(4) {
            bottom: 0;
            right: 0;
            border-width: 0 3px 3px 0;
            border-radius: 0 0 8px 0;
        }

        .frame-corners.face-ok span {
            border-color: #00e676;
        }

        .frame-instruction {
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
            padding: 0 20px;
        }

        /* ── Tahap 2: Liveness circle + ring ── */
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
        /*  Opsi 2: buka mulut/senyum,     */
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
    <!-- Content tetap sama seperti sebelumnya -->
    <div class="row" style="margin-top: 70px;">
        <div class="col">
            <input type="hidden" id="lokasi">

            <div class="webcam-capture" id="webcamCapture">
                <video id="video" autoplay muted playsinline></video>

                <div id="face-status">⏳ Memuat model...</div>
                <div id="earDebugText" class="ear-debug"></div>

                <div class="frame-guide-overlay" id="stageFrameGuide">
                    <div class="frame-corners" id="frameCorners">
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <div class="frame-instruction" id="frameInstructionText">
                        Posisikan wajah Anda dalam bingkai.
                    </div>
                </div>

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

                    <div class="direction-arrow" id="directionArrow">
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </div>

                    <div class="liveness-instruction" id="livenessInstructionText">
                        Kedipkan mata Anda sekali.
                    </div>
                    <div class="liveness-hint" id="livenessHint">Tatap kamera secara normal</div>

                    <div class="liveness-checklist">
                        <div class="check-item" id="checkBlink">
                            <ion-icon name="ellipse-outline"></ion-icon>
                            1. Kedipkan mata Anda sekali
                        </div>
                        <div class="check-item" id="checkMouth">
                            <ion-icon name="ellipse-outline"></ion-icon>
                            2. Senyum sebentar
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

    <div class="liveness-success-badge" id="livenessSuccessBadge" style="display:none;">
        <ion-icon name="checkmark-circle-outline"></ion-icon>
        Liveness Terverifikasi — Wajah Asli Terdeteksi
    </div>
    </div>
    </div>

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
        const earDebugText = document.getElementById('earDebugText'); // ✅ debug angka EAR real-time

        var notifikasi_in = document.getElementById('notifikasi_in');
        var notifikasi_out = document.getElementById('notifikasi_out');
        var notifikasi_radius = document.getElementById('notifikasi_radius');

        // ══════════════════════════════
        // ✅ STATE MACHINE LIVENESS
        // ══════════════════════════════
        // stage: 'frame' -> 'liveness' -> 'ready'
        // livenessSubStage (di dalam 'liveness'): 'blink' -> 'mouth' -> 'move'
        let stage = 'frame';
        let frameStableCount = 0;
        const FRAME_STABLE_NEEDED = 5; // ~1.5 detik stabil (interval 300ms)

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


        // ================== BAGIAN BARU: DETEKSI SENYUM ==================
        let mouthState = 'neutral';
        let smileActionCount = 0;
        let smileCheckPassed = false;
        const SMILE_REQUIRED = 1;

        let smileBaseline = null;
        let smileCalibrationSamples = [];
        const SMILE_CALIBRATION_FRAMES_NEEDED = 12;

        const SMILE_RATIO = 1.35;           // Sesuaikan kalau perlu (1.32 ~ 1.4)
        const SMILE_NEUTRAL_RATIO = 1.15;

        function estimateSmile(positions) {
            const dist = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);
            const leftCorner = positions[48];
            const rightCorner = positions[54];
            const upperLip = positions[51];
            const lowerLip = positions[57];

            const width = dist(leftCorner, rightCorner);
            if (width < 10) return 0.3;
            return dist(upperLip, lowerLip) / width;
        }

        function collectSmileCalibrationSample(smile) {
            smileCalibrationSamples.push(smile);
            if (smileCalibrationSamples.length >= SMILE_CALIBRATION_FRAMES_NEEDED) {
                const sorted = [...smileCalibrationSamples].sort((a, b) => a - b);
                const mid = Math.floor(sorted.length / 2);
                smileBaseline = sorted.length % 2 !== 0 ? sorted[mid] : (sorted[mid - 1] + sorted[mid]) / 2;
                console.log('✅ Kalibrasi Senyum selesai, baseline:', smileBaseline.toFixed(3));
            }
        }

        function processSmileDetection(smile) {
            const smileThreshold = smileBaseline !== null ? smileBaseline * SMILE_RATIO : 0.48;
            const neutralThreshold = smileBaseline !== null ? smileBaseline * SMILE_NEUTRAL_RATIO : 0.38;

            if (mouthState === 'neutral' && smile > smileThreshold) {
                mouthState = 'smiling';
            } else if (mouthState === 'smiling' && smile < neutralThreshold) {
                mouthState = 'neutral';
                smileActionCount++;
            }

            if (earDebugText) {
                earDebugText.textContent =
                    `SMILE:${smile.toFixed(3)} Base:${smileBaseline?.toFixed(3) || '-'} ` +
                    `Sen>${smileThreshold.toFixed(3)} Net<${neutralThreshold.toFixed(3)} ` +
                    `State:${mouthState} Aksi:${smileActionCount}`;
            }
        }

        // Update checklist UI (tetap sama)
        function updateChecklistUI(moveDone, blinkDone, mouthDone) {
            // ... (tetap sama)
        }

        // Ganti di startBlinkFastLoop() saat transisi ke mouth stage
        // Cari baris ini dan ganti:
        livenessInstructionText.innerHTML = 'Senyum sebentar (angkat sudut bibir), lalu kembalikan wajah netral.';
        livenessHint.textContent = 'Tatap kamera secara normal';

        // Di dalam main loop, ganti bagian if (livenessSubStage === 'mouth')
        if (livenessSubStage === 'mouth') {
            const smile = estimateSmile(detection.landmarks.positions);

            if (smileBaseline === null) {
                collectSmileCalibrationSample(smile);
                livenessHint.textContent = 'Sedang menyesuaikan sensor senyum...';
                return;
            }

            processSmileDetection(smile);

            const smileDone = smileActionCount >= SMILE_REQUIRED;
            updateChecklistUI(false, true, smileDone);
            updateRingProgress(smileDone ? 0.5 : (mouthState === 'smiling' ? 0.45 : 0.3));

            if (smileDone) {
                smileCheckPassed = true;
                livenessInstructionText.innerHTML = '✅ Senyum terdeteksi!';
                livenessHint.textContent = '';
                startMoveStage();
            }
            return;
        }

        // ... (sisanya kode tetap sama)
    </script>
@endpush