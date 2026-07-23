@extends('layouts.attendance')

@section('content')

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f3f6fb;
            font-family: 'Poppins', sans-serif;
            color: #111827;
        }

        /* =========================
                                                                                                                                            PROFILE HEADER
                                                                                                                                        ========================= */
        .profile-section {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            padding: 28px 18px 85px;
            border-radius: 0 0 30px 30px;
            position: relative;
            overflow: hidden;
        }

        .profile-section::before {
            content: "";
            position: absolute;
            right: -40px;
            top: -40px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .profile-card {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 2;
        }

        .profile-avatar img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
        }

        .profile-avatar .avatar-initial {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #42a5f5, #1565c0);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 700;
            border: 4px solid rgba(255, 255, 255, .25);
            box-shadow: 0 6px 18px rgba(0, 0, 0, .18);
            text-transform: uppercase;
        }

        .profile-name {
            font-size: 20px;
            font-weight: 700;
            color: white;
            line-height: 1.2;
        }

        .profile-class {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            margin-top: 4px;
        }

        /* =========================
                                                                                                                                            MENU
                                                                                                                                        ========================= */
        .menu-wrapper {
            margin-top: -55px;
            padding: 0 16px;
            position: relative;
            z-index: 5;
        }

        .menu-card {
            background: white;
            border-radius: 26px;
            padding: 18px 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .menu-item {
            text-align: center;
            text-decoration: none;
        }

        .menu-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 24px;
            color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .menu-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        .bg-blue {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .bg-orange {
            background: linear-gradient(135deg, #fb923c, #f97316);
        }

        .bg-green {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .bg-red {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        /* =========================
                                                                                                                                            CONTENT
                                                                                                                                        ========================= */
        .content-wrapper {
            padding: 22px 16px 110px;
        }

        .section-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 14px;
            color: #111827;
        }

        /* =========================
                                                                                                                                            PRESENCE
                                                                                                                                        ========================= */
        .presence-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .presence-card {
            border-radius: 24px;
            padding: 18px;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 140px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .presence-card::before {
            content: "";
            position: absolute;
            right: -20px;
            top: -20px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.10);
        }

        .presence-blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .presence-red {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .presence-icon img {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.25);
            margin-bottom: 14px;
        }

        .presence-icon ion-icon {
            font-size: 42px;
            margin-bottom: 12px;
        }

        .presence-title {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 6px;
        }

        .presence-time {
            font-size: 21px;
            font-weight: 700;
            line-height: 1.2;
        }

        /* =========================
                                                                                                                REKAP MODERN
                                                                                                            ========================= */
        .rekap-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .rekap-card {
            background: linear-gradient(145deg, #ffffff, #ffffff);
            border-radius: 24px;
            padding: 18px;
            min-height: 120px;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 8px 20px rgba(255, 255, 255, 0.18),
                inset 0 1px 0 rgba(255, 255, 255, 0.03);
        }

        .rekap-card::before {
            content: "";
            position: absolute;
            right: -25px;
            bottom: -25px;
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        .rekap-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .rekap-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .rekap-number {
            font-size: 42px;
            font-weight: 700;
            color: black;
            line-height: 1;
        }

        .rekap-label {
            font-size: 16px;
            font-weight: 600;
            color: black;
        }

        /* ICON COLORS */
        .icon-primary {
            background: #1877f2;
        }

        .icon-success {
            background: #ff4d4f;
        }

        .icon-warning {
            background: #9ca3af;
        }

        .icon-danger {
            background: #f59e0b;
        }

        /* =========================
                                                                                                                                            TABS
                                                                                                                                        ========================= */
        .custom-tabs {
            background: white;
            border-radius: 18px;
            padding: 5px;
            display: flex;
            box-shadow: 0 5px 16px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 18px;
        }

        .custom-tabs .nav-item {
            flex: 1;
        }

        .custom-tabs .nav-link {
            border: none !important;
            border-radius: 14px !important;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
            padding: 12px;
        }

        .custom-tabs .nav-link.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white !important;
            box-shadow: 0 5px 12px rgba(37, 99, 235, 0.25);
        }

        /* =========================
                                                                                                                                            HISTORY
                                                                                                                                        ========================= */
        .history-card {
            background: white;
            border-radius: 20px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            box-shadow: 0 5px 16px rgba(0, 0, 0, 0.04);
        }

        .history-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .history-icon {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            background: rgba(37, 99, 235, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 22px;
        }

        .history-date {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        .history-time {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .badge-custom {
            min-width: 70px;
            text-align: center;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }

        .badge-success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            box-shadow: 0 4px 10px rgba(34, 197, 94, 0.25);
        }

        .badge-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
        }

        /* =========================
                                                                                                                                            MOBILE FIX
                                                                                                                                        ========================= */
        @media (max-width: 380px) {

            .presence-time {
                font-size: 17px;
            }

            .menu-icon {
                width: 52px;
                height: 52px;
                font-size: 21px;
            }

            .menu-label {
                font-size: 11px;
            }
        }
    </style>

    {{-- PROFILE --}}
    <div class="profile-section">

        <div class="profile-card">

            <div class="profile-avatar">

                <div class="avatar-initial">
                    {{ strtoupper(substr(Auth::guard('siswa')->user()->nama_lengkap, 0, 2)) }}
                </div>
            </div>
            <div>
                <div class="profile-name">
                    {{ Auth::guard('siswa')->user()->nama_lengkap }}
                </div>

                <div class="profile-class">
                    {{ Auth::guard('siswa')->user()->kelas }}
                    -
                    {{ Auth::guard('siswa')->user()->kode_jurusan }}
                </div>
            </div>

        </div>
    </div>

    {{-- MENU --}}
    <div class="menu-wrapper">

        <div class="menu-card">

            <div class="menu-grid">

                <a href="/editprofile" class="menu-item">
                    <div class="menu-icon bg-blue">
                        <ion-icon name="person-outline"></ion-icon>
                    </div>

                    <div class="menu-label">
                        Profil
                    </div>
                </a>

                <a href="/attendance/izin" class="menu-item">
                    <div class="menu-icon bg-orange">
                        <ion-icon name="calendar-outline"></ion-icon>
                    </div>

                    <div class="menu-label">
                        Izin
                    </div>
                </a>

                <a href="/attendance/histori" class="menu-item">
                    <div class="menu-icon bg-green">
                        <ion-icon name="document-text-outline"></ion-icon>
                    </div>

                    <div class="menu-label">
                        Histori
                    </div>
                </a>

                <a href="#" class="menu-item">
                    <div class="menu-icon bg-red">
                        <ion-icon name="location-outline"></ion-icon>
                    </div>

                    <div class="menu-label">
                        Lokasi
                    </div>
                </a>

            </div>

        </div>
    </div>

    <div class="content-wrapper">

        {{-- PRESENCE --}}
        <div class="section-title">
            Presensi Hari Ini
        </div>

        <div class="presence-grid">

            {{-- MASUK --}}
            <div class="presence-card presence-blue">

                <div class="presence-icon">
                    @if ($attendancehariini != null)
                        @php
                            $path = Storage::url('/uploads/absensi/' . $attendancehariini->foto_in);
                        @endphp

                        <img src="{{ asset($path) }}">
                    @else
                        <ion-icon name="camera-outline"></ion-icon>
                    @endif
                </div>

                <div class="presence-title">
                    Jam Masuk
                </div>

                <div class="presence-time">
                    {{ $attendancehariini != null ? $attendancehariini->jam_in : 'Belum Absen' }}
                </div>

            </div>

            {{-- PULANG --}}
            <div class="presence-card presence-red">

                <div class="presence-icon">
                    @if ($attendancehariini != null && $attendancehariini->jam_out != null)
                        @php
                            $path = Storage::url('/uploads/absensi/' . $attendancehariini->foto_out);
                        @endphp

                        <img src="{{ asset($path) }}">
                    @else
                        <ion-icon name="camera-outline"></ion-icon>
                    @endif
                </div>

                <div class="presence-title">
                    Jam Pulang
                </div>

                <div class="presence-time">
                    {{ $attendancehariini != null && $attendancehariini->jam_out != null ? $attendancehariini->jam_out : 'Belum Absen' }}
                </div>

            </div>

        </div>

        {{-- REKAP --}}
        <div class="section-title mt-4">
            Rekap {{ $namabulan[$bulanini] }} {{ $tahunini }}
        </div>

        <div class="rekap-grid">

            {{-- HADIR --}}
            <div class="rekap-card">

                <div class="rekap-top">
                    <div class="rekap-icon icon-primary">
                        <ion-icon name="checkmark-done-outline"></ion-icon>
                    </div>

                    <div class="rekap-number">
                        {{ $rekapattendance->jmlhadir ?? '-' }}
                    </div>
                </div>

                <div class="rekap-label">
                    Hadir
                </div>

            </div>

            {{-- IZIN --}}
            <div class="rekap-card">

                <div class="rekap-top">
                    <div class="rekap-icon icon-success">
                        <ion-icon name="document-text-outline"></ion-icon>
                    </div>

                    <div class="rekap-number">
                        {{ $rekapizin->jmlizin ?? '-' }}
                    </div>
                </div>

                <div class="rekap-label">
                    Izin
                </div>

            </div>

            {{-- SAKIT --}}
            <div class="rekap-card">

                <div class="rekap-top">
                    <div class="rekap-icon icon-warning">
                        <ion-icon name="medkit-outline"></ion-icon>
                    </div>

                    <div class="rekap-number">
                        {{ $rekapizin->jmlsakit ?? '-' }}
                    </div>
                </div>

                <div class="rekap-label">
                    Sakit
                </div>

            </div>

            {{-- TELAT --}}
            <div class="rekap-card">

                <div class="rekap-top">
                    <div class="rekap-icon icon-danger">
                        <ion-icon name="alarm-outline"></ion-icon>
                    </div>

                    <div class="rekap-number">
                        {{ $rekapattendance->jmlterlambat ?? '-' }}
                    </div>
                </div>

                <div class="rekap-label">
                    Telat
                </div>

            </div>

            {{-- ALPA --}}
            <div class="rekap-card">

                <div class="rekap-top">
                    <div class="rekap-icon icon-success">
                        <ion-icon name="close-circle-outline"></ion-icon>
                    </div>

                    <div class="rekap-number">
                        {{ $alpa }}
                    </div>
                </div>

                <div class="rekap-label">
                    Alpa
                </div>

            </div>

        </div>
        {{-- TABS --}}
        <div class="mt-4">

            <ul class="nav nav-tabs custom-tabs" role="tablist">

                <li class="nav-item w-50">
                    <a class="nav-link active" data-toggle="tab" href="#home">
                        Bulan Ini
                    </a>
                </li>

                <li class="nav-item w-50">
                    <a class="nav-link" data-toggle="tab" href="#leaderboard">
                        Leaderboard
                    </a>
                </li>

            </ul>

            <div class="tab-content">

                {{-- HISTORI --}}
                <div class="tab-pane fade show active" id="home">

                    @foreach ($historibulanini as $d)

                        <div class="history-card">

                            <div class="history-left">

                                <div class="history-icon">
                                    <ion-icon name="calendar-outline"></ion-icon>
                                </div>

                                <div>
                                    <div class="history-date">
                                        {{ date('d M Y', strtotime($d->tgl_presensi)) }}
                                    </div>

                                    <div class="history-time">
                                        Masuk:
                                        {{ $d->jam_in }}
                                    </div>
                                </div>

                            </div>

                            <div>
                                <span class="badge-custom badge-success">
                                    {{ $d->jam_in }}
                                </span>
                            </div>

                        </div>

                    @endforeach

                </div>

                {{-- LEADERBOARD --}}
                <div class="tab-pane fade" id="leaderboard">

                    @foreach ($leaderboard as $d)

                        <div class="history-card">

                            <div class="history-left">

                                <div class="history-icon">
                                    <ion-icon name="person-outline"></ion-icon>
                                </div>

                                <div>

                                    <div class="history-date">
                                        {{ $d->nama_lengkap }}
                                    </div>

                                    <div class="history-time">
                                        {{ $d->kelas }}
                                        -
                                        {{ $d->kode_jurusan }}
                                    </div>

                                </div>

                            </div>

                            <div>
                                <span class="badge-custom {{ $d->jam_in < '07:00' ? 'badge-success' : 'badge-danger' }}">

                                    {{ $d->jam_in }}

                                </span>
                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

@endsection