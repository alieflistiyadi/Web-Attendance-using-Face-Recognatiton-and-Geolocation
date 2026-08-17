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
                                                PRESENCE - PER SUBJECT
                                                MINIMAL / ELEGANT
                                                ========================= */
        .presence-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .presence-card {
            min-height: 84px;
            border-radius: 18px;
            padding: 12px 13px;
            color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 11px;
            box-shadow: 0 5px 14px rgba(0, 0, 0, 0.07);
        }

        .presence-card::before {
            content: "";
            position: absolute;
            right: -38px;
            top: -48px;
            width: 105px;
            height: 105px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .presence-blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .presence-green {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .presence-orange {
            background: linear-gradient(135deg, #f97316, #ea580c);
        }

        .presence-purple {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .presence-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.17);
            overflow: hidden;
        }

        .presence-icon img {
            width: 42px;
            height: 42px;
            margin: 0;
            border: none;
            border-radius: 13px;
            object-fit: cover;
        }

        .presence-icon ion-icon {
            font-size: 22px;
            margin: 0;
        }

        .presence-main {
            min-width: 0;
            flex: 1;
            position: relative;
            z-index: 1;
            align-self: stretch;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .subject-name {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 2px;
        }

        .teacher-name {
            font-size: 10px;
            opacity: 0.82;
            margin-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .presence-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 7px;
        }

        .presence-time {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }

        .presence-time span {
            opacity: 0.7;
            font-size: 8px;
            font-weight: 500;
        }

        .presence-status {
            position: relative;
            z-index: 2;
            flex: 0 0 auto;
            align-self: center;
            background: rgba(255, 255, 255, 0.17);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 5px 8px;
            border-radius: 999px;
            font-size: 8px;
            line-height: 1;
            font-weight: 700;
            white-space: nowrap;
        }

        .empty-presence {
            background: white;
            border-radius: 18px;
            padding: 24px 16px;
            text-align: center;
            color: #6b7280;
            box-shadow: 0 5px 16px rgba(0, 0, 0, .04);
        }

        .empty-presence ion-icon {
            font-size: 38px;
            margin-bottom: 8px;
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
                                                        REKAP KEHADIRAN
                                                        ========================= */
        .rekap-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .rekap-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 15px;
            min-height: 112px;
            box-shadow: 0 5px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(17, 24, 39, 0.04);
        }

        .rekap-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .rekap-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 22px;
            flex-shrink: 0;
        }

        .rekap-number {
            font-size: 27px;
            line-height: 1;
            font-weight: 800;
            color: #111827;
        }

        .rekap-label {
            margin-top: 13px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
        }

        .rekap-unit {
            font-size: 10px;
            font-weight: 500;
            color: #9ca3af;
            margin-left: 2px;
        }

        .rekap-card:nth-child(1) .rekap-icon {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .rekap-card:nth-child(2) .rekap-icon {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .rekap-card:nth-child(3) .rekap-icon {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
        }

        .rekap-card:nth-child(4) .rekap-icon {
            background: linear-gradient(135deg, #f59e0b, #ea580c);
        }

        .rekap-card:nth-child(5) .rekap-icon {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        /* =========================
                                                        REKAP PER MATA PELAJARAN
                                                        ========================= */
        .subject-recap-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .subject-recap-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 16px;
            box-shadow: 0 5px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(17, 24, 39, 0.04);
        }

        .subject-recap-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .subject-recap-name {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .subject-recap-teacher {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .subject-recap-stats {
            display: flex;
            gap: 8px;
            margin-top: 13px;
        }

        .subject-stat {
            flex: 1;
            background: #f3f6fb;
            border-radius: 12px;
            padding: 10px;
            text-align: center;
        }

        .subject-stat-number {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
        }

        .subject-stat-label {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }

        @media (max-width: 380px) {
            .rekap-grid {
                gap: 9px;
            }

            .rekap-card {
                padding: 13px;
                min-height: 105px;
            }

            .rekap-icon {
                width: 42px;
                height: 42px;
                font-size: 20px;
            }

            .rekap-number {
                font-size: 24px;
            }
        }

        /* =========================
                                                                    REKAP PER SUBJECT
                                                                    ========================= */
        .subject-recap-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .subject-recap-card {
            background: white;
            border-radius: 18px;
            padding: 15px;
            box-shadow: 0 5px 16px rgba(0, 0, 0, 0.04);
        }

        .subject-recap-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .subject-recap-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .subject-recap-teacher {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }

        .subject-recap-stats {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .subject-stat {
            flex: 1;
            background: #f3f6fb;
            border-radius: 12px;
            padding: 9px;
            text-align: center;
        }

        .subject-stat-number {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .subject-stat-label {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
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

            .presence-time-value {
                font-size: 16px;
            }

            .presence-times {
                gap: 18px;
            }

            .subject-name {
                font-size: 16px;
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

                <a href="/attendance/create" class="menu-item">
                    <div class="menu-icon bg-red">
                        <ion-icon name="camera-outline"></ion-icon>
                    </div>

                    <div class="menu-label">
                        Camera
                    </div>
                </a>

            </div>

        </div>
    </div>

    <div class="content-wrapper">

        {{-- PRESENCE HARI INI PER SUBJECT --}}
        <div class="section-title">
            Presensi Hari Ini
        </div>

        <div class="presence-grid">

            @forelse ($attendanceHariIni as $attendance)

                @php
                    $colors = [
                        'presence-blue',
                        'presence-green',
                        'presence-orange',
                        'presence-purple'
                    ];

                    $colorClass =
                        $colors[$loop->index % count($colors)];

                    $jamIn = $attendance->jam_in
                        ? date('H:i', strtotime($attendance->jam_in))
                        : '-';

                    $jamOut = $attendance->jam_out
                        ? date('H:i', strtotime($attendance->jam_out))
                        : '-';

                    $fotoIn = null;

                    if (!empty($attendance->foto_in)) {
                        $fotoIn = Storage::url(
                            '/uploads/absensi/' . $attendance->foto_in
                        );
                    }

                    $isLate = false;

                    if (
                        !empty($attendance->jam_in) &&
                        !empty($attendance->batas_telat)
                    ) {
                        $isLate =
                            strtotime($attendance->jam_in) >
                            strtotime($attendance->batas_telat);
                    }
                @endphp

                <div class="presence-card {{ $colorClass }}">

                    <div class="presence-icon">
                        @if ($fotoIn)
                            <img src="{{ asset($fotoIn) }}" alt="Foto Presensi">
                        @else
                            <ion-icon name="book-outline"></ion-icon>
                        @endif
                    </div>

                    <div class="presence-main">

                        <div class="subject-name">
                            {{ $attendance->nama_mapel }}
                        </div>

                        <div class="teacher-name">
                            {{ $attendance->nama_guru }}
                            @if (!empty($attendance->nama_kelas))
                                • {{ $attendance->nama_kelas }}
                            @endif
                        </div>

                        <div class="presence-meta">
                            <div class="presence-time">
                                <span>Masuk</span>
                                {{ $jamIn }}
                            </div>

                            <div class="presence-time">
                                <span>Pulang</span>
                                {{ $jamOut }}
                            </div>
                        </div>

                    </div>

                    <div class="presence-status">
                        @if ($isLate)
                            ⚠ Telat
                        @elseif ($attendance->jam_out)
                            ✓ Selesai
                        @else
                            ✓ Hadir
                        @endif
                    </div>

                </div>

            @empty

                <div class="empty-presence">
                    <ion-icon name="calendar-outline"></ion-icon>

                    <div style="font-weight:600;">
                        Belum ada presensi hari ini
                    </div>

                    <div style="font-size:12px; margin-top:4px;">
                        Presensi setiap mata pelajaran akan muncul di sini.
                    </div>
                </div>

            @endforelse

        </div>

        {{-- REKAP BULANAN
        Hadir/Izin/Sakit/Alpa = per hari
        Telat = per subject/session
        --}}
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

                {{-- HISTORI PER SUBJECT --}}
                <div class="tab-pane fade show active" id="home">

                    @forelse ($historibulanini as $d)

                        @php
                            $jamIn = $d->jam_in
                                ? date('H:i', strtotime($d->jam_in))
                                : '-';

                            $jamOut = $d->jam_out
                                ? date('H:i', strtotime($d->jam_out))
                                : '-';

                            $isLate = false;

                            if (
                                !empty($d->jam_in) &&
                                !empty($d->batas_telat)
                            ) {
                                $isLate =
                                    strtotime($d->jam_in) >
                                    strtotime($d->batas_telat);
                            }
                        @endphp

                        <div class="history-card">

                            <div class="history-left">

                                <div class="history-icon">
                                    <ion-icon name="book-outline"></ion-icon>
                                </div>

                                <div>

                                    <div class="history-date">
                                        {{ date('d M Y', strtotime($d->tgl_presensi)) }}
                                    </div>

                                    <div class="history-time">
                                        {{ $d->nama_mapel }}
                                    </div>

                                    <div class="history-time">
                                        {{ $d->nama_guru }}
                                    </div>

                                    <div class="history-time">
                                        Masuk: {{ $jamIn }}
                                        &nbsp; • &nbsp;
                                        Pulang: {{ $jamOut }}
                                    </div>

                                </div>

                            </div>

                            <div>
                                <span class="badge-custom {{ $isLate ? 'badge-danger' : 'badge-success' }}">
                                    {{ $isLate ? 'Telat' : 'Hadir' }}
                                </span>
                            </div>

                        </div>

                    @empty

                        <div class="empty-presence">
                            Belum ada histori presensi bulan ini.
                        </div>

                    @endforelse

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
    @push('myscript')
        <script>
            (function () {
                const belumDaftarWajah = @json(empty(Auth::guard('siswa')->user()->face_descriptor));
                const passwordDefault = @json((bool) Auth::guard('siswa')->user()->is_default_password);

                // Nonaktifkan tombol take attendance kalau wajah belum terdaftar
                if (belumDaftarWajah) {
                    document.querySelectorAll('a[href="/attendance/create"], #btnTakeAttendance')
                        .forEach(el => {
                            el.classList.add('disabled');
                            el.style.pointerEvents = 'none';
                            el.style.opacity = '0.5';
                        });
                }

                // ── Prioritas: wajah belum terdaftar dulu (lebih kritikal), baru password default ──
                if (belumDaftarWajah) {
                    Swal.fire({
                        title: 'Wajah Belum Terdaftar',
                        html: 'Anda belum mendaftarkan wajah untuk keperluan absensi.<br>Silakan daftarkan wajah Anda terlebih dahulu sebelum bisa melakukan absensi.',
                        icon: 'warning',
                        confirmButtonText: 'Daftarkan Wajah Sekarang',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        window.location.href = '/editprofile';
                    });
                } else if (passwordDefault) {
                    Swal.fire({
                        title: 'Password Masih Default',
                        html: 'Demi keamanan akun Anda, silakan segera ganti password default Anda.',
                        icon: 'warning',
                        confirmButtonText: 'Ganti Password Sekarang',
                        showCancelButton: true,
                        cancelButtonText: 'Nanti Saja',
                        allowOutsideClick: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/editprofile';
                        }
                    });
                }
            })();
        </script>
    @endpush
@endsection