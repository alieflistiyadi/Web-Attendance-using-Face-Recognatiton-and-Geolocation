@extends('layouts.attendance')

@section('header')
    <div class="appHeader text-light" style="background: linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%);
                box-shadow:0 2px 16px rgba(26,115,232,.18);">

        <div class="left">
            <a href="javascript:;" class="headerButton goBack" style="color:#fff;">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>

        <div class="pageTitle" style="font-weight:700;">
            Data Izin / Sakit
        </div>

        <div class="right"></div>
    </div>
@endsection

@section('content')

    <style>
        :root {
            --blue: #1a73e8;
            --blue-dark: #0d47a1;
            --green: #16a34a;
            --orange: #f59e0b;
            --red: #ef4444;
            --bg: #f4f7fc;
            --text: #1e293b;
            --muted: #64748b;
            --shadow: 0 6px 20px rgba(15, 23, 42, .06);
        }

        body {
            background: var(--bg) !important;
        }

        .izin-wrapper {
            padding: 82px 16px 100px;
        }

        /* ===== HERO ===== */
        .hero-card {
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            border-radius: 24px;
            padding: 22px 20px;
            color: white;
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(26, 115, 232, .22);
        }

        .hero-card::before {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            top: -50px;
            right: -40px;
        }

        .hero-title {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 6px;
            position: relative;
            z-index: 2;
        }

        .hero-sub {
            font-size: 13px;
            line-height: 1.7;
            opacity: .9;
            position: relative;
            z-index: 2;
        }

        /* ===== ALERT ===== */
        .custom-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            margin-bottom: 16px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ===== CARD ===== */
        .izin-card {
            background: #fff;
            border-radius: 22px;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            transition: .2s ease;
        }

        .izin-card:active {
            transform: scale(.99);
        }

        .izin-left {
            display: flex;
            gap: 14px;
            flex: 1;
            min-width: 0;
        }

        /* ===== ICON ===== */
        .izin-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }

        .icon-sakit {
            background: #fee2e2;
            color: var(--red);
        }

        .icon-izin {
            background: #fff7ed;
            color: var(--orange);
        }

        /* ===== CONTENT ===== */
        .izin-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .izin-date {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }

        .izin-type {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1;
        }

        .type-sakit {
            background: #fee2e2;
            color: #dc2626;
        }

        .type-izin {
            background: #ffedd5;
            color: #ea580c;
        }

        .izin-desc {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
            word-break: break-word;
        }

        /* ===== RIGHT ACTION ===== */
        .izin-action {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
            gap: 10px;
            min-height: 85px;
        }

        /* ===== STATUS ===== */
        .status-badge {
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
            letter-spacing: .3px;
        }

        .status-wait {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-approved {
            background: #dcfce7;
            color: #15803d;
        }

        .status-declined {
            background: #fee2e2;
            color: #dc2626;
        }

        /* ===== FILE RIGHT ===== */
        .izin-file-right a {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            color: var(--blue);
            background: #eff6ff;
            padding: 6px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }

        /* ===== EMPTY ===== */
        .empty-state {
            background: white;
            border-radius: 24px;
            padding: 42px 24px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .empty-icon {
            font-size: 60px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        .empty-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 6px;
        }

        .empty-sub {
            font-size: 13px;
            line-height: 1.7;
            color: var(--muted);
        }

        /* ===== FAB ===== */
        .custom-fab {
            position: fixed;
            right: 20px;
            bottom: 90px;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(26, 115, 232, .30);
            z-index: 999;
            transition: .2s ease;
        }

        .custom-fab:active {
            transform: scale(.94);
        }
    </style>

    <div class="izin-wrapper">

        {{-- HERO --}}
        <div class="hero-card">
            <div class="hero-title">
                Riwayat Izin & Sakit
            </div>

            <div class="hero-sub">
                Lihat seluruh pengajuan izin dan sakit Anda beserta status persetujuannya.
            </div>
        </div>

        {{-- ALERT --}}
        @php
            $messagesuccess = Session::get('success');
            $messagerror = Session::get('error');
        @endphp

        @if (Session::get('success'))
            <div class="custom-alert alert-success">
                ✅ {{ $messagesuccess }}
            </div>
        @endif

        @if (Session::get('error'))
            <div class="custom-alert alert-danger">
                ❌ {{ $messagerror }}
            </div>
        @endif

        {{-- DATA --}}
        @forelse ($dataizin as $d)

            <div class="izin-card">

                {{-- LEFT --}}
                <div class="izin-left">

                    <div class="izin-icon {{ $d->status == 's' ? 'icon-sakit' : 'icon-izin' }}">
                        <ion-icon name="{{ $d->status == 's' ? 'medkit-outline' : 'document-text-outline' }}">
                        </ion-icon>
                    </div>

                    <div class="izin-content">

                        <div class="izin-date">
                            {{ date('d M Y', strtotime($d->tanggal_izin)) }}
                        </div>

                        <div class="izin-type {{ $d->status == 's' ? 'type-sakit' : 'type-izin' }}">
                            {{ $d->status == 's' ? 'Sakit' : 'Izin' }}
                        </div>

                        <div class="izin-desc">
                            {{ $d->keterangan }}
                        </div>

                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="izin-action">

                    {{-- STATUS --}}
                    @if ($d->status_approved == 0)
                        <div class="status-badge status-wait">
                            Menunggu
                        </div>

                    @elseif ($d->status_approved == 1)
                        <div class="status-badge status-approved">
                            Disetujui
                        </div>

                    @elseif ($d->status_approved == 2)
                        <div class="status-badge status-declined">
                            Ditolak
                        </div>
                    @endif

                    {{-- FILE --}}
                    @if($d->surat_sakit != null)
                        <div class="izin-file-right">
                            <a href="{{ asset('storage/uploads/surat_sakit/' . $d->surat_sakit) }}" target="_blank">

                                <ion-icon name="document-attach-outline"></ion-icon>
                                Surat
                            </a>
                        </div>
                    @endif

                </div>

            </div>

        @empty

            <div class="empty-state">

                <div class="empty-icon">
                    <ion-icon name="documents-outline"></ion-icon>
                </div>

                <div class="empty-title">
                    Belum Ada Data Izin
                </div>

                <div class="empty-sub">
                    Anda belum pernah mengajukan izin atau sakit.
                </div>

            </div>

        @endforelse

    </div>

    {{-- FAB --}}
    <a href="/attendance/buatizin" class="custom-fab">
        <ion-icon name="add-outline"></ion-icon>
    </a>

@endsection