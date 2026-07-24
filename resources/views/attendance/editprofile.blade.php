@extends('layouts.attendance')
@section('header')

    <!-- App Header -->
    <div class="appHeader "
        style="background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); color: #fff; box-shadow: 0 2px 16px rgba(26,115,232,0.18);">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack" style="color:#fff;">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle" style="font-weight:700; letter-spacing:0.3px;">Profil Saya</div>
        <div class="right"></div>
    </div>

    <script defer src="{{ asset('assets/js/face-api.min.js') }}"></script>

    <style>
        :root {
            --blue: #1a73e8;
            --blue-dark: #0d47a1;
            --blue-light: #e8f0fe;
            --green: #00c853;
            --orange: #ff9800;
            --red: #f44336;
            --gray: #f5f6fa;
            --gray-2: #e0e0e0;
            --text: #1a1a2e;
            --text-muted: #8a93a2;
            --radius: 16px;
            --shadow: 0 4px 24px rgba(26, 115, 232, 0.10);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: #f0f4ff !important;
        }

        /* ── Avatar hero ── */
        .profile-hero {
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            padding: 56px 20px 60px;
            text-align: center;
            position: relative;
        }

        .profile-hero::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 40px;
            background: #f0f4ff;
            border-radius: 40px 40px 0 0;
        }

        .avatar-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 10px;
        }

        .avatar-initial {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.9);
            background: linear-gradient(135deg, #42a5f5, #1565c0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.18);
        }

        .hero-name {
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            margin: 4px 0 2px;
            letter-spacing: 0.2px;
        }

        .hero-sub {
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
        }

        /* ── Tab switcher ── */
        .tab-switcher {
            display: flex;
            background: #fff;
            border-radius: 14px;
            margin: 0 16px 18px;
            padding: 5px;
            box-shadow: var(--shadow);
            gap: 4px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px 6px;
            border: none;
            border-radius: 10px;
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all .22s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .tab-btn.active {
            background: var(--blue);
            color: #fff;
            box-shadow: 0 3px 12px rgba(26, 115, 232, 0.30);
        }

        .tab-btn ion-icon {
            font-size: 16px;
        }

        /* ── Tab panels ── */
        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        /* ── Card ── */
        .profile-card {
            background: #fff;
            border-radius: var(--radius);
            margin: 0 16px 16px;
            padding: 20px 16px;
            box-shadow: var(--shadow);
        }

        .card-section-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        /* ── Input fields ── */
        .field-group {
            margin-bottom: 14px;
        }

        .field-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .field-label ion-icon {
            font-size: 14px;
            color: var(--blue);
        }

        .field-input {
            width: 100%;
            padding: 13px 14px;
            border: 2px solid var(--gray-2);
            border-radius: 12px;
            font-size: 14px;
            color: var(--text);
            background: var(--gray);
            outline: none;
            transition: border .18s, background .18s;
        }

        .field-input:focus {
            border-color: var(--blue);
            background: var(--blue-light);
        }

        /* ── Tombol Simpan Perubahan ── */
        .btn-save {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(26, 115, 232, 0.30);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity .18s, transform .18s;
        }

        .btn-save:active {
            opacity: .88;
            transform: scale(.98);
        }

        .btn-save:disabled {
            background: var(--gray-2);
            color: var(--text-muted);
            box-shadow: none;
            cursor: not-allowed;
        }

        /* ✅ Tombol Logout — merah, tepat di bawah Simpan Perubahan */
        .btn-logout {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f44336, #b71c1c);
            color: #fff !important;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(244, 67, 54, 0.30);

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            transition: opacity .18s, transform .18s;

            text-decoration: none !important;
        }

        .btn-logout:hover,
        .btn-logout:focus,
        .btn-logout:active,
        .btn-logout:visited {
            color: #fff !important;
            text-decoration: none !important;
        }

        .btn-logout:active {
            opacity: .88;
            transform: scale(.98);
        }

        .btn-logout ion-icon {
            font-size: 18px;
        }

        /* ── Webcam box ── */
        .webcam-box {
            position: relative;
            width: 100%;
            height: 230px;
            border-radius: 14px;
            overflow: hidden;
            background: #000;
            margin-bottom: 12px;
        }

        .webcam-box video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .webcam-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        /* Scanning frame corner brackets */
        .scan-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -56%);
            width: 140px;
            height: 140px;
        }

        .scan-frame::before,
        .scan-frame::after,
        .scan-frame span::before,
        .scan-frame span::after {
            content: '';
            position: absolute;
            width: 28px;
            height: 28px;
            border-color: #fff;
            border-style: solid;
        }

        .scan-frame::before {
            top: 0;
            left: 0;
            border-width: 3px 0 0 3px;
            border-radius: 6px 0 0 0;
        }

        .scan-frame::after {
            top: 0;
            right: 0;
            border-width: 3px 3px 0 0;
            border-radius: 0 6px 0 0;
        }

        .scan-frame span::before {
            bottom: 0;
            left: 0;
            border-width: 0 0 3px 3px;
            border-radius: 0 0 0 6px;
        }

        .scan-frame span::after {
            bottom: 0;
            right: 0;
            border-width: 0 3px 3px 0;
            border-radius: 0 0 6px 0;
        }

        /* Scan line animation */
        .scan-line {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -56%);
            width: 140px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00e5ff, transparent);
            animation: scanMove 2s ease-in-out infinite;
        }

        @keyframes scanMove {
            0% {
                margin-top: -70px;
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                margin-top: 70px;
                opacity: 0;
            }
        }

        .webcam-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.55);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            backdrop-filter: blur(6px);
        }

        /* ── Face status pill ── */
        .face-status-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--gray);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 14px;
        }

        .face-status-pill .pill-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .pill-icon.registered {
            background: #e8f5e9;
            color: var(--green);
        }

        .pill-icon.not-registered {
            background: #fff3e0;
            color: var(--orange);
        }

        .pill-text-main {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .pill-text-sub {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* ── Scan button ── */
        .btn-scan {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ff9800, #e65100);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(255, 152, 0, 0.30);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity .18s, transform .18s;
        }

        .btn-scan:disabled {
            background: var(--gray-2);
            color: var(--text-muted);
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn-scan:not(:disabled):active {
            opacity: .88;
            transform: scale(.98);
        }

        /* ── Info box ── */
        .info-box {
            background: var(--blue-light);
            border-left: 3px solid var(--blue);
            border-radius: 10px;
            padding: 10px 12px;
            margin-top: 14px;
            font-size: 12px;
            color: #1a73e8;
            line-height: 1.6;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .info-box ion-icon {
            flex-shrink: 0;
            font-size: 16px;
            margin-top: 1px;
        }

        /* ── Alert ── */
        .alert {
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 12px;
        }

        /* ── Steps ── */
        .steps {
            display: flex;
            gap: 0;
            margin-bottom: 16px;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 16px;
            left: 24px;
            right: 24px;
            height: 2px;
            background: var(--gray-2);
            z-index: 0;
        }

        .step-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gray-2);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            border: 2px solid #fff;
        }

        .step-circle.done {
            background: var(--green);
            color: #fff;
        }

        .step-circle.active {
            background: var(--blue);
            color: #fff;
        }

        .step-label {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 600;
            text-align: center;
        }

        .step-label.active {
            color: var(--blue);
        }

        .step-label.done {
            color: var(--green);
        }

        /* Loading pulse on btn */
        @keyframes btnPulse {

            0%,
            100% {
                box-shadow: 0 4px 16px rgba(26, 115, 232, .3);
            }

            50% {
                box-shadow: 0 4px 28px rgba(26, 115, 232, .6);
            }
        }

        .btn-processing {
            animation: btnPulse 1s ease infinite;
        }

        /* ══════════════════════════════ */
        /* ✅ PASSWORD REQUIREMENT CHECKLIST (BARU) */
        /* ══════════════════════════════ */
        .pw-requirement-box {
            background: var(--gray);
            border-radius: 12px;
            padding: 12px 14px;
            margin: -4px 0 14px;
        }

        .pw-requirement-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .pw-req-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: var(--text-muted);
            padding: 4px 0;
            transition: color .18s ease;
        }

        .pw-req-item .pw-req-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid var(--gray-2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 11px;
            color: transparent;
            transition: all .18s ease;
        }

        .pw-req-item.valid {
            color: var(--green);
            font-weight: 600;
        }

        .pw-req-item.valid .pw-req-icon {
            background: var(--green);
            border-color: var(--green);
            color: #fff;
        }

        .pw-match-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            margin-top: 6px;
            font-weight: 600;
        }

        .pw-match-row.match {
            color: var(--green);
        }

        .pw-match-row.no-match {
            color: var(--red);
        }
    </style>

@endsection

@section('content')

    {{-- ── Hero ── --}}
    <div class="profile-hero">
        <div class="avatar-wrap">
            <div class="avatar-initial">
                {{ strtoupper(substr($siswa->nama_lengkap, 0, 2)) }}
            </div>
        </div>
        <div class="hero-name">{{ $siswa->nama_lengkap }}</div>
        <div class="hero-sub">NIS: {{ $siswa->nis }} &nbsp;·&nbsp; Kelas {{ $siswa->kelas }}</div>
    </div>

    {{-- ── Alert session ── --}}
    @if(Session::get('success') || Session::get('error'))
        <div style="padding: 0 16px; margin-top: 12px;">
            @if(Session::get('success'))
                <div class="alert alert-success">✅ {{ Session::get('success') }}</div>
            @endif
            @if(Session::get('error'))
                <div class="alert alert-danger">❌ {{ Session::get('error') }}</div>
            @endif
        </div>
    @endif

    {{-- ── Tab switcher ── --}}
    <div class="tab-switcher">
        <button class="tab-btn active" id="tabBtnInfo" onclick="switchTab('info')">
            <ion-icon name="person-outline"></ion-icon> Informasi Akun
        </button>
        <button class="tab-btn" id="tabBtnWajah" onclick="switchTab('wajah')">
            <ion-icon name="scan-outline"></ion-icon> Data Wajah
        </button>
    </div>


    {{-- ══════════════════════════════ --}}
    {{-- TAB 1 : INFORMASI AKUN --}}
    {{-- ══════════════════════════════ --}}
    <div class="tab-panel active" id="panelInfo">

        <form action="/attendance/{{ $siswa->nis }}/updateprofile" method="POST" id="formGantiPassword">
            @csrf

            <div class="profile-card">
                <div class="card-section-label">Data Diri</div>

                <div class="field-group">
                    <div class="field-label">
                        <ion-icon name="person-outline"></ion-icon>
                        Nama Lengkap
                    </div>

                    <input type="text" class="field-input" value="{{ $siswa->nama_lengkap }}" readonly>
                </div>

                <div class="field-group">
                    <div class="field-label">
                        <ion-icon name="person-outline"></ion-icon>
                        NIS
                    </div>

                    <input type="text" class="field-input" value="{{ $siswa->nis }}" readonly>
                </div>
            </div>

            <div class="profile-card">
                <div class="card-section-label">Keamanan Akun</div>

                @if(Auth::guard('siswa')->user()->is_default_password == 1)
                    <div class="alert alert-warning">
                        Password Anda masih menggunakan password default.
                        Silakan ganti password.
                    </div>
                @endif

                <div class="field-group">
                    <div class="field-label">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        Password Lama
                    </div>
                    <input type="password" class="field-input" name="password_lama" placeholder="Masukkan password lama">

                    @error('password_lama')
                        <div style="color:red;font-size:12px;margin-top:5px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="field-group">
                    <div class="field-label">
                        <ion-icon name="key-outline"></ion-icon>
                        Password Baru
                    </div>
                    <input type="password" class="field-input" name="password_baru" id="passwordBaru"
                        placeholder="Masukkan password baru" autocomplete="new-password">

                    @error('password_baru')
                        <div style="color:red;font-size:12px;margin-top:5px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- ✅ REQUIREMENT CHECKLIST PASSWORD (BARU) --}}
                <div class="pw-requirement-box">
                    <div class="pw-requirement-title">Password Anda Harus:</div>

                    <div class="pw-req-item" data-rule="length">
                        <span class="pw-req-icon"><ion-icon name="checkmark-outline"></ion-icon></span>
                        Minimal 8 karakter
                    </div>
                    <div class="pw-req-item" data-rule="number">
                        <span class="pw-req-icon"><ion-icon name="checkmark-outline"></ion-icon></span>
                        Mengandung minimal 1 angka
                    </div>
                    <div class="pw-req-item" data-rule="case">
                        <span class="pw-req-icon"><ion-icon name="checkmark-outline"></ion-icon></span>
                        Mengandung huruf besar dan huruf kecil
                    </div>
                    <div class="pw-req-item" data-rule="special">
                        <span class="pw-req-icon"><ion-icon name="checkmark-outline"></ion-icon></span>
                        Mengandung minimal 1 karakter spesial (~!@#$%^&amp;*()-_+=[]{}\:;"&lt;&gt;,.?/)
                    </div>
                </div>

                <div class="field-group">
                    <div class="field-label">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        Konfirmasi Password Baru
                    </div>
                    <input type="password" class="field-input" name="password_baru_confirmation" id="passwordKonfirmasi"
                        placeholder="Konfirmasi password baru" autocomplete="new-password">

                    <div class="pw-match-row" id="pwMatchRow" style="display:none;"></div>

                    @error('password_baru_confirmation')
                        <div style="color:red;font-size:12px;margin-top:5px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- ✅ Tombol Simpan Perubahan --}}
            <div style="padding: 0 16px 12px;">
                <button type="submit" class="btn-save" id="btnSimpanProfil">
                    <ion-icon name="save-outline" style="font-size:18px;"></ion-icon>
                    Simpan Perubahan
                </button>
            </div>

            {{-- ✅ Tombol Logout — merah, tepat di bawah Simpan --}}
            <div style="padding: 0 16px 80px;">
                <a href="{{ route('process-logout') }}" class="btn-logout"
                    onclick="return confirm('Yakin ingin keluar dari akun?')">
                    <ion-icon name="log-out-outline" style="font-size:18px;"></ion-icon>
                    Keluar dari Akun
                </a>
            </div>

        </form>

    </div>{{-- /panelInfo --}}



    {{-- ══════════════════════════════ --}}
    {{-- TAB 2 : DATA WAJAH --}}
    {{-- ══════════════════════════════ --}}
    <div class="tab-panel" id="panelWajah">

        <div class="profile-card">
            <div class="card-section-label">Panduan Registrasi</div>

            {{-- Step indicator --}}
            <div class="steps">
                <div class="step-item">
                    <div class="step-circle done" id="step1Circle">
                        <ion-icon name="checkmark-outline"></ion-icon>
                    </div>
                    <div class="step-label done">Buka Kamera</div>
                </div>
                <div class="step-item">
                    <div class="step-circle" id="step2Circle">2</div>
                    <div class="step-label" id="step2Label">Arahkan Wajah</div>
                </div>
                <div class="step-item">
                    <div class="step-circle" id="step3Circle">3</div>
                    <div class="step-label" id="step3Label">Simpan Data</div>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="card-section-label">Kamera</div>

            {{-- Webcam --}}
            <div class="webcam-box">
                <video id="videoWajah" autoplay muted playsinline></video>
                <div class="webcam-overlay">
                    <div class="scan-frame"><span></span></div>
                    <div class="scan-line"></div>
                </div>
                <div class="webcam-badge" id="statusDaftarModel">⏳ Memuat model...</div>
            </div>

            {{-- Status pill --}}
            <div class="face-status-pill" id="faceStatusPill">
                @if($siswa->face_descriptor)
                    <div class="pill-icon registered">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                    </div>
                    <div>
                        <div class="pill-text-main" id="faceStatusMain">Wajah Sudah Terdaftar</div>
                        <div class="pill-text-sub" id="faceStatusSub">Anda dapat melakukan absensi. Perbarui jika diperlukan.
                        </div>
                    </div>
                @else
                    <div class="pill-icon not-registered">
                        <ion-icon name="alert-circle-outline"></ion-icon>
                    </div>
                    <div>
                        <div class="pill-text-main" id="faceStatusMain">Wajah Belum Didaftarkan</div>
                        <div class="pill-text-sub" id="faceStatusSub">Daftarkan wajah agar bisa melakukan absensi.</div>
                    </div>
                @endif
            </div>

            {{-- Scan button --}}
            <button class="btn-scan" id="btnDaftarWajah" disabled>
                <ion-icon name="hourglass-outline" id="btnScanIcon"></ion-icon>
                <span id="btnScanText">Menunggu Model...</span>
            </button>

            {{-- Info tips --}}
            <div class="info-box">
                <ion-icon name="information-circle-outline"></ion-icon>
                <span>Pastikan wajah Anda berada di dalam bingkai, cahaya cukup terang, dan tidak menggunakan masker atau
                    kacamata hitam.</span>
            </div>
        </div>

        <div style="height: 40px;"></div>
    </div>{{-- /panelWajah --}}

@endsection

@push('myscript')
    <script>
        // ══════════════════════════════
        // TAB SWITCHING
        // ══════════════════════════════
        function switchTab(tab) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

            if (tab === 'info') {
                document.getElementById('panelInfo').classList.add('active');
                document.getElementById('tabBtnInfo').classList.add('active');
            } else {
                document.getElementById('panelWajah').classList.add('active');
                document.getElementById('tabBtnWajah').classList.add('active');
            }
        }

        // ══════════════════════════════
        // ✅ VALIDASI REQUIREMENT PASSWORD (BARU)
        // ══════════════════════════════
        const passwordBaru = document.getElementById('passwordBaru');
        const passwordKonfirmasi = document.getElementById('passwordKonfirmasi');
        const pwMatchRow = document.getElementById('pwMatchRow');
        const formGantiPassword = document.getElementById('formGantiPassword');

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
                const el = document.querySelector('.pw-req-item[data-rule="' + rule + '"]');
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

            if (confirm.length === 0) {
                pwMatchRow.style.display = 'none';
                return true;
            }

            pwMatchRow.style.display = 'flex';

            if (val === confirm) {
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

        passwordBaru.addEventListener('input', checkPasswordRequirements);
        passwordKonfirmasi.addEventListener('input', checkPasswordMatch);

        // Validasi sebelum submit — hanya jika user memang mengisi password baru
        formGantiPassword.addEventListener('submit', function (e) {
            const val = passwordBaru.value;
            const confirm = passwordKonfirmasi.value;

            // Kalau user tidak mengubah password, biarkan submit (misal hanya update data lain)
            if (val.length === 0 && confirm.length === 0) return;

            const requirementsOk = checkPasswordRequirements();
            const matchOk = checkPasswordMatch();

            if (!requirementsOk || !matchOk) {
                e.preventDefault();
                Swal.fire({
                    title: 'Password Belum Valid',
                    text: 'Pastikan password baru memenuhi semua syarat dan konfirmasi password sama.',
                    icon: 'warning',
                    confirmButtonText: 'Mengerti'
                });
            }
        });

        // ══════════════════════════════
        // FACE API — VARIABEL
        // ══════════════════════════════
        let wajahModelReady = false;
        const videoWajah = document.getElementById('videoWajah');
        const statusModel = document.getElementById('statusDaftarModel');
        const btnScan = document.getElementById('btnDaftarWajah');
        const btnScanIcon = document.getElementById('btnScanIcon');
        const btnScanText = document.getElementById('btnScanText');

        function setStep(n) {
            const circles = [null,
                document.getElementById('step1Circle'),
                document.getElementById('step2Circle'),
                document.getElementById('step3Circle'),
            ];
            const labels = [null, null,
                document.getElementById('step2Label'),
                document.getElementById('step3Label'),
            ];
            for (let i = 2; i <= 3; i++) {
                circles[i].classList.remove('active', 'done');
                if (labels[i]) labels[i].classList.remove('active', 'done');
                if (i < n) {
                    circles[i].classList.add('done');
                    circles[i].innerHTML = '<ion-icon name="checkmark-outline"></ion-icon>';
                    if (labels[i]) labels[i].classList.add('done');
                } else if (i === n) {
                    circles[i].classList.add('active');
                    circles[i].textContent = i;
                    if (labels[i]) labels[i].classList.add('active');
                } else {
                    circles[i].textContent = i;
                }
            }
        }

        // ══════════════════════════════
        // LOAD MODEL
        // ══════════════════════════════
        async function loadModelWajah() {
            const MODEL_URL = '/models';
            try {
                statusModel.textContent = '⏳ Memuat model...';
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                wajahModelReady = true;
                statusModel.textContent = '✅ Model siap';
                btnScan.disabled = false;
                btnScanIcon.setAttribute('name', 'scan-outline');
                btnScanText.textContent = '{{ $siswa->face_descriptor ? "Perbarui Data Wajah" : "Daftarkan Wajah Saya" }}';
                setStep(2);
                console.log("✅ Face API Ready");
            } catch (e) {
                statusModel.textContent = '❌ Gagal load model';
                btnScanText.textContent = 'Gagal Memuat';
                console.error("❌ Model gagal load:", e);
            }
        }

        // ══════════════════════════════
        // KAMERA
        // ══════════════════════════════
        async function startCameraWajah() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: 640, height: 480, facingMode: "user" },
                    audio: false
                });
                videoWajah.srcObject = stream;
            } catch (err) {
                statusModel.textContent = '❌ Kamera tidak bisa diakses';
                console.error("❌ Kamera error:", err);
                Swal.fire({
                    title: 'Izin Kamera Diperlukan',
                    text: 'Aktifkan izin kamera di browser Anda untuk mendaftarkan wajah.',
                    icon: 'warning',
                    confirmButtonText: 'Mengerti'
                });
            }
        }

        window.addEventListener('load', async () => {
            await loadModelWajah();
            await startCameraWajah();
        });

        // ══════════════════════════════
        // DAFTAR WAJAH
        // ══════════════════════════════
        btnScan.addEventListener('click', async function () {
            if (!wajahModelReady) {
                Swal.fire({ title: 'Belum Siap!', text: 'Model wajah belum selesai dimuat, tunggu sebentar.', icon: 'error' });
                return;
            }

            // UI: scanning state
            btnScan.disabled = true;
            btnScan.classList.add('btn-processing');
            btnScanIcon.setAttribute('name', 'hourglass-outline');
            btnScanText.textContent = 'Mendeteksi wajah...';
            setStep(2);

            const detection = await faceapi
                .detectSingleFace(videoWajah, new faceapi.TinyFaceDetectorOptions({
                    inputSize: 416,
                    scoreThreshold: 0.3
                }))
                .withFaceLandmarks()
                .withFaceDescriptor();

            btnScan.classList.remove('btn-processing');

            if (!detection) {
                Swal.fire({
                    title: 'Wajah Tidak Terdeteksi',
                    text: 'Pastikan wajah terlihat jelas, pencahayaan cukup, dan berada di dalam bingkai.',
                    icon: 'error',
                    confirmButtonText: 'Coba Lagi'
                });
                btnScan.disabled = false;
                btnScanIcon.setAttribute('name', 'scan-outline');
                btnScanText.textContent = 'Coba Lagi';
                return;
            }

            // Wajah terdeteksi → simpan
            setStep(3);
            btnScanText.textContent = 'Menyimpan data...';

            const descriptor = Array.from(detection.descriptor);

            $.ajax({
                url: '/siswa/save-descriptor',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    descriptor: JSON.stringify(descriptor)
                },
                success: function () {
                    // Update step 3 done
                    const c3 = document.getElementById('step3Circle');
                    c3.classList.remove('active');
                    c3.classList.add('done');
                    c3.innerHTML = '<ion-icon name="checkmark-outline"></ion-icon>';
                    document.getElementById('step3Label').classList.remove('active');
                    document.getElementById('step3Label').classList.add('done');

                    // Update status pill → registered
                    document.getElementById('faceStatusPill').innerHTML = `
                                                                                                                                        <div class="pill-icon registered">
                                                                                                                                            <ion-icon name="checkmark-circle-outline"></ion-icon>
                                                                                                                                        </div>
                                                                                                                                        <div>
                                                                                                                                            <div class="pill-text-main">Wajah Sudah Terdaftar</div>
                                                                                                                                            <div class="pill-text-sub">Data wajah berhasil disimpan. Anda siap absensi!</div>
                                                                                                                                        </div>
                                                                                                                                    `;

                    btnScan.disabled = false;
                    btnScanIcon.setAttribute('name', 'refresh-outline');
                    btnScanText.textContent = 'Perbarui Data Wajah';

                    Swal.fire({
                        title: 'Berhasil! ',
                        text: 'Data wajah berhasil didaftarkan. Sekarang Anda bisa absensi menggunakan face recognition.',
                        icon: 'success',
                        confirmButtonText: 'Siap Absen!',
                        confirmButtonColor: '#1a73e8'
                    });
                },
                error: function () {
                    btnScan.disabled = false;
                    btnScanIcon.setAttribute('name', 'scan-outline');
                    btnScanText.textContent = 'Coba Lagi';
                    Swal.fire({
                        title: 'Gagal Menyimpan',
                        text: 'Terjadi kesalahan saat menyimpan data wajah. Silakan coba lagi.',
                        icon: 'error'
                    });
                }
            });
        });
    </script>
@endpush