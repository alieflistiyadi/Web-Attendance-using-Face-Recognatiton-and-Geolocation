@extends('layouts.attendance')

@section('header')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">

    <style>
        body {
            background: #f4f7fb;
            font-family: 'Poppins', sans-serif;
        }

        .datepicker-modal {
            max-height: 430px !important;
        }

        .datepicker-date-display {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        }

        /* HEADER */
        .appHeader {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            box-shadow: 0 2px 12px rgba(37, 99, 235, 0.25);
        }

        /* WRAPPER */
        .izin-wrapper {
            margin-top: 75px;
            padding: 14px 14px 90px;
        }

        /* CARD */
        .izin-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 22px 18px;
            box-shadow:
                0 10px 30px rgba(15, 23, 42, 0.06),
                0 2px 8px rgba(15, 23, 42, 0.04);
        }

        /* HEADER CARD */
        .top-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .top-icon {
            width: 55px;
            height: 55px;
            border-radius: 18px;
            background: linear-gradient(135deg,
                    rgba(37, 99, 235, 0.15),
                    rgba(29, 78, 216, 0.08));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 24px;
            flex-shrink: 0;
        }

        .top-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .top-subtitle {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }

        /* INFO BOX */
        .info-box {
            background: linear-gradient(135deg, #eff6ff, #eef4ff);
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 14px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .info-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 16px;
            flex-shrink: 0;
        }

        .info-text {
            font-size: 12.5px;
            color: #1e3a8a;
            line-height: 1.6;
        }

        /* FORM */
        .form-group-custom {
            margin-bottom: 18px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        .custom-input,
        .custom-select,
        .custom-textarea {
            width: 100%;
            border: 1px solid #dbe1ea !important;
            background: #f9fafc !important;
            border-radius: 16px !important;
            box-sizing: border-box !important;
            padding: 0 16px !important;
            font-size: 14px !important;
            color: #111827 !important;
            transition: all 0.2s ease;
            margin: 0 !important;
        }

        .custom-input,
        .custom-select {
            height: 54px !important;
        }

        .custom-textarea {
            height: 120px !important;
            padding-top: 14px !important;
            resize: none;
        }

        .custom-input:focus,
        .custom-select:focus,
        .custom-textarea:focus {
            border-color: #2563eb !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08) !important;
        }

        /* FILE */
        .upload-wrapper {
            background: #fff7ed;
            border: 1px dashed #fb923c;
            border-radius: 18px;
            padding: 16px;
        }

        .upload-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #9a3412;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .custom-file {
            width: 100%;
            background: white;
            border: 1px solid #fed7aa;
            border-radius: 14px;
            padding: 12px;
            font-size: 13px;
        }

        .upload-note {
            margin-top: 10px;
            font-size: 11px;
            color: #9a3412;
            line-height: 1.5;
        }

        /* HELPER */
        .helper-text {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 6px;
        }

        /* BUTTON */
        .btn-submit {
            width: 100%;
            height: 56px;
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }

        /* MOBILE */
        @media (max-width: 480px) {
            .izin-card {
                padding: 18px 16px;
            }

            .top-title {
                font-size: 18px;
            }

            .top-subtitle {
                font-size: 12px;
            }
        }
    </style>

    <div class="appHeader text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>

        <div class="pageTitle">
            Edit Pengajuan
        </div>

        <div class="right"></div>
    </div>
@endsection

@section('content')
    <div class="izin-wrapper">

        <div class="izin-card">

            {{-- HEADER --}}
            <div class="top-header">

                <div class="top-icon">
                    <ion-icon name="document-text-outline"></ion-icon>
                </div>

                <div>
                    <div class="top-title">
                        Pengajuan Izin / Sakit
                    </div>

                    <div class="top-subtitle">
                        Lengkapi form berikut untuk melakukan pengajuan izin atau sakit.
                    </div>
                </div>
            </div>

            {{-- INFO --}}
            <div class="info-box">

                <div class="info-icon">
                    <ion-icon name="information-outline"></ion-icon>
                </div>

                <div class="info-text">
                    Pengajuan hanya dapat dilakukan maksimal
                    <b>3 hari ke depan</b>.
                    Jika memilih status <b>sakit</b>,
                    upload surat sakit wajib dilakukan.
                </div>
            </div>

            {{-- FORM --}}
            <form id="frmIzin" action="{{ route('attendance.updateizin', $izin->id) }}" method="POST"
                enctype="multipart/form-data">

                @csrf

                {{-- TANGGAL --}}
                <div class="form-group-custom">

                    <label class="form-label">
                        Tanggal Izin
                    </label>

                    <input type="text" id="tanggal_izin" name="tanggal_izin" class="custom-input datepicker"
                        value="{{ date('d-m-Y', strtotime($izin->tanggal_izin)) }}">
                </div>

                {{-- STATUS --}}
                <div class="form-group-custom">

                    <label class="form-label">
                        Status Pengajuan
                    </label>

                    <select name="status" id="status" class="browser-default custom-select">

                        <option value="">
                            Pilih Status
                        </option>

                        <option value="i" @if($izin->status == "i") selected @endif>
                            Izin
                        </option>

                        <option value="s" @if($izin->status == "s") selected @endif>
                            Sakit
                        </option>
                    </select>
                </div>

                {{-- SURAT --}}
                <!-- Upload Surat Izin -->
                <div class="form-group-custom" id="formSuratIzin" style="display:none;">
                    <div class="upload-wrapper">
                        <div class="upload-title">
                            <ion-icon name="document-attach-outline"></ion-icon>
                            Upload Surat Izin
                        </div>

                        <input type="file" name="surat_izin" id="surat_izin" class="custom-file">

                        @if($izin->surat_izin)
                            <div class="helper-text">
                                <a href="{{ asset('uploads/surat_izin/' . $d->surat_izin) }}" target="_blank">
                                    {{ $izin->surat_izin }}
                                </a>
                            </div>
                        @endif

                        <div class="upload-note">
                            Upload file JPG, PNG, atau PDF dengan ukuran maksimal 2MB.
                        </div>
                    </div>
                </div>

                <!-- Upload Surat Sakit -->
                <div class="form-group-custom" id="formSuratSakit" style="display:none;">

                    <div class="upload-wrapper">

                        <div class="upload-title">
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                            Upload Surat Sakit
                        </div>

                        <input type="file" name="surat_sakit" id="surat_sakit" class="custom-file">

                        @if($izin->surat_sakit)
                            <div class="helper-text">
                                File saat ini :
                                <a href="{{ asset('storage/uploads/surat_sakit/' . $izin->surat_sakit) }}" target="_blank">
                                    {{ $izin->surat_sakit }}
                                </a>
                            </div>
                        @endif

                        <div class="upload-note">
                            Upload file JPG, PNG, atau PDF
                            dengan ukuran maksimal 2MB.
                        </div>
                    </div>
                </div>

                {{-- KETERANGAN --}}
                <div class="form-group-custom">

                    <label class="form-label">
                        Keterangan
                    </label>

                    <textarea name="keterangan" id="keterangan" class="custom-textarea"
                        placeholder="Tulis alasan izin atau sakit...">{{ $izin->keterangan }}</textarea>

                    <div class="helper-text">
                        Contoh: Sakit demam, ada keperluan keluarga, dan lain-lain.
                    </div>
                </div>

                {{-- BUTTON --}}
                <div style="margin-top:28px;">

                    <button type="submit" class="btn-submit">

                        <ion-icon name="send-outline"></ion-icon>

                        Update Pengajuan
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(document).ready(function () {

            $(".datepicker").datepicker({
                defaultDate: new Date(),
                setDefaultDate: true,
                yearRange: [1928, 2100],
                format: "dd-mm-yyyy"
            });

            $("#status").change(function () {

                var status = $(this).val();

                if (status == "i") {
                    $("#formSuratIzin").slideDown(200);
                    $("#formSuratSakit").slideUp(200);
                }
                else if (status == "s") {
                    $("#formSuratSakit").slideDown(200);
                    $("#formSuratIzin").slideUp(200);
                }
                else {
                    $("#formSuratIzin").slideUp(200);
                    $("#formSuratSakit").slideUp(200);
                }

            });

            $('#status').trigger('change');

            $("#frmIzin").submit(function () {

                var tanggal_izin = $("#tanggal_izin").val();
                var status = $("#status").val();
                var keterangan = $("#keterangan").val();
                var surat_izin = $("#surat_izin").val();
                var surat_sakit = $("#surat_sakit").val();

                if (tanggal_izin == "") {

                    Swal.fire({
                        title: 'Oops!',
                        text: 'Tanggal harus diisi',
                        icon: 'Peringatan',
                    });

                    return false;

                } else if (status == "") {

                    Swal.fire({
                        title: 'Oops!',
                        text: 'Status harus dipilih',
                        icon: 'Peringatan',
                    });

                    return false;

                } else if (keterangan == "") {

                    Swal.fire({
                        title: 'Oops!',
                        text: 'Keterangan harus diisi',
                        icon: 'Peringatan',
                    });

                    return false;
                }
            });
        });
    </script>
@endpush

ini kode editizin.blade.php