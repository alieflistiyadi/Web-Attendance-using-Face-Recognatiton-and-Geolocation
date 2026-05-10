@extends('layouts.admin.tabler')
@section('content')
<?php
    function selisih($jam_masuk, $jam_keluar){
        list($h, $m, $s) = explode(":", $jam_masuk);
        $dtAwal = mktime($h, $m, $s, "1", "1", "1");
        list($h, $m, $s) = explode(":", $jam_keluar);
        $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
        $dtSelisih = $dtAkhir - $dtAwal;
        $totalmenit = $dtSelisih / 60;
        $jam = explode(".", $totalmenit / 60);
        $sisamenit = ($totalmenit / 60) - $jam[0];
        $sisamenit2 = $sisamenit * 60;
        $jml_jam = $jam[0];
        return $jml_jam . ":" . round($sisamenit2);
    }
?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                Monitoring Attendance
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" /><path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" /><path d="M8 15h2v2h-2l0 -2" /></svg>
                                </span>
                                <input type="text" id="tanggal" name="tanggal" value="{{ date('d-m-Y') }}" class="form-control" placeholder="Tanggal Attendance" autocomplete="off">
                              </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Jurusan</th>
                                            <th>Jam Masuk</th>
                                            <th>Foto</th>
                                            <th>Jam Pulang</th>
                                            <th>Foto</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loadattendance"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof flatpickr !== "undefined") {
        flatpickr("#tanggal", {
            dateFormat: "d-m-Y",
            locale: "id",
            onChange: function(selectedDates, dateStr) {
                $.ajax({
                    type: 'POST',
                    url: '/getattendance',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggal: dateStr
                    },
                    beforeSend: function() {
                        $("#loadattendance").html("<tr><td colspan='8'>Loading...</td></tr>");
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadattendance").html(respond);
                    }
                });
            }
        });
    } else {
        console.error("Flatpickr belum ke-load");
    }
});
</script>
@endpush