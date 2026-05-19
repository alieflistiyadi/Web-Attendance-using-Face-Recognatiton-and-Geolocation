@extends('layouts.attendance')

@section('header')
    <div class="appHeader text-light" style="background: linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%);
                   box-shadow:0 2px 16px rgba(26,115,232,.18);">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack" style="color:#fff;">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>

        <div class="pageTitle" style="font-weight:700; letter-spacing:.3px;">
            Histori Presensi
        </div>

        <div class="right"></div>
    </div>
@endsection

@section('content')

    <style>
        :root {
            --blue: #1a73e8;
            --blue-dark: #0d47a1;
            --blue-light: #e8f0fe;
            --bg: #f3f6fd;
            --text: #1a1a2e;
            --muted: #8a93a2;
            --radius: 18px;
            --shadow: 0 4px 24px rgba(26, 115, 232, .10);
        }

        body {
            background: var(--bg) !important;
        }

        .histori-wrapper {
            padding: 84px 16px 24px;
        }

        /* ───── Header Card ───── */
        .hero-card {
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            border-radius: 24px;
            padding: 24px 18px;
            color: #fff;
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(26, 115, 232, .28);
        }

        .hero-card::before {
            content: '';
            position: absolute;
            right: -30px;
            top: -30px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .hero-title {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .hero-sub {
            font-size: 13px;
            opacity: .85;
            line-height: 1.6;
        }

        /* ───── Filter Card ───── */
        .filter-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 18px;
            box-shadow: var(--shadow);
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .custom-select {
            width: 100%;
            height: 52px;
            border-radius: 14px;
            border: 2px solid #e4e9f2;
            background: #f8fbff;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            outline: none;
            transition: .2s ease;
            margin-bottom: 14px;
        }

        .custom-select:focus {
            border-color: var(--blue);
            background: var(--blue-light);
        }

        /* ───── Button ───── */
        .btn-search {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 18px rgba(26, 115, 232, .28);
            transition: .2s ease;
        }

        .btn-search:active {
            transform: scale(.98);
            opacity: .9;
        }

        /* ───── Result ───── */
        #showhistori {
            min-height: 120px;
        }

        .empty-state {
            background: #fff;
            border-radius: var(--radius);
            padding: 32px 20px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .empty-icon {
            font-size: 52px;
            color: #c3cad8;
            margin-bottom: 10px;
        }

        .empty-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .empty-sub {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        /* Loading */
        .loading-box {
            background: #fff;
            border-radius: var(--radius);
            padding: 24px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .loading-box ion-icon {
            font-size: 42px;
            color: var(--blue);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="histori-wrapper">

        {{-- Hero --}}
        <div class="hero-card">
            <div class="hero-title">Riwayat Kehadiran</div>
            <div class="hero-sub">
                Lihat histori presensi berdasarkan bulan dan tahun yang dipilih.
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-card">

            <div class="section-title">
                Filter Data Presensi
            </div>

            <select name="bulan" id="bulan" class="custom-select">
                <option value="">📅 Pilih Bulan</option>

                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                        {{ $namabulan[$i] }}
                    </option>
                @endfor
            </select>

            <select name="tahun" id="tahun" class="custom-select">
                <option value="">📆 Pilih Tahun</option>

                @php
                    $tahunmulai = 2022;
                    $tahunskrg = date('Y');
                @endphp

                @for ($tahun = $tahunmulai; $tahun <= $tahunskrg; $tahun++)
                    <option value="{{ $tahun }}" {{ date('Y') == $tahun ? 'selected' : '' }}>
                        {{ $tahun }}
                    </option>
                @endfor
            </select>

            <button class="btn-search" id="getdata">
                <ion-icon name="search-outline"></ion-icon>
                Tampilkan Histori
            </button>
        </div>

        {{-- Result --}}
        <div id="showhistori">

            <div class="empty-state">
                <div class="empty-icon">
                    <ion-icon name="calendar-outline"></ion-icon>
                </div>

                <div class="empty-title">
                    Belum Ada Data Ditampilkan
                </div>

                <div class="empty-sub">
                    Pilih bulan dan tahun terlebih dahulu,
                    lalu tekan tombol <b>Tampilkan Histori</b>.
                </div>
            </div>

        </div>

    </div>

@endsection

@push('myscript')
    <script>
        $(function () {

            $("#getdata").click(function () {

                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();

                if (bulan == "" || tahun == "") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Filter Belum Lengkap',
                        text: 'Silakan pilih bulan dan tahun terlebih dahulu.'
                    });
                    return;
                }

                $("#showhistori").html(`
                        <div class="loading-box">
                            <ion-icon name="reload-outline"></ion-icon>
                            <div style="margin-top:10px; font-size:14px; font-weight:600; color:#1a73e8;">
                                Memuat histori presensi...
                            </div>
                        </div>
                    `);

                $.ajax({
                    type: 'POST',
                    url: '/gethistori',
                    data: {
                        _token: "{{ csrf_token() }}",
                        bulan: bulan,
                        tahun: tahun
                    },
                    cache: false,

                    success: function (respond) {
                        $("#showhistori").html(respond);
                    },

                    error: function () {
                        $("#showhistori").html(`
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <ion-icon name="alert-circle-outline"></ion-icon>
                                    </div>

                                    <div class="empty-title">
                                        Gagal Memuat Data
                                    </div>

                                    <div class="empty-sub">
                                        Terjadi kesalahan saat mengambil histori presensi.
                                    </div>
                                </div>
                            `);
                    }
                });

            });

        });
    </script>
@endpush