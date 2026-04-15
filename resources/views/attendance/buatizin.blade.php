@extends('layouts.attendance')
@section('header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css" >
<!-- * App Header -->

<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascripts:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a> 
    </div>
    <div class="pageTitle">Form Izin</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
@endsection
@section('content')
<div class="row" style="margin-top:70px">
    <div class="col">
        <form method="POST" action="{{ route('storeizin') }}" id="frmIzin">
            @csrf
            <div class="class form-group">
                <input type="text" id="tanggal_izin" name="tanggal_izin" class="form-control datepicker" placeholder="Tanggal">
            </div>
            <div class="form-group">
                <select name="status" id="status" class="form-control">
                    <option value="">Izin / Sakit</option>
                    <option value="i">Izin</option>
                    <option value="s">Sakit</option>
                </select>
            </div>
            <div class="form-group">
                <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control" placeholder="Keterangan"></textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-primary w-100">Kirim</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('myscript')
<script>
    var currYear = (new Date()).getFullYear();

    $(document).ready(function() {
        $(".datepicker").datepicker({
            defaultDate: new Date(), // buka di hari ini (saat dibuka calender izin)
            setDefaultDate: true, // agar saat klik calender tanggal, bulan, tahun udah ke isi
            maxDate: new Date(new Date().setDate(new Date().getDate() + 3)), // izin/sakit sampai 3 hari ke depan
            yearRange: [1928, 2100],
            format: "yyyy/mm/dd"    
        });

        $("#frmIzin").submit(function() {
            var tanggal_izin = $("#tanggal_izin").val();
            var status = $("#status").val();
            var keterangan = $("#keterangan").val();
            if(tanggal_izin =="") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'Tanggal Harus Diisi',
                    icon: 'warning',
                });
                return false;
            } else if (status == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'Status Harus Diisi',
                    icon: 'warning',
                });
                return false;
            } else if (keterangan == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'Keterangan Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }
        });
    });
</script>
@endpush