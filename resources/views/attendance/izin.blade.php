@extends('layouts.attendance')
@section('header')
<!-- * App Header -->

<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascripts:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a> 
    </div>
    <div class="pageTitle">Data Izin / Sakit</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
@endsection
@section('content')
<div class="row" style="margin-top:70px">
    <div class="col">
         @php
        $messagesuccess = Session::get('success');  
        $messagerror = Session::get('error');
        @endphp
        @if (Session::get('success'))
        <div class="alert alert-success">
            {{ $messagesuccess }}
        </div>
        @endif
        @if (Session::get('error'))
        <div class="alert alert-danger">
            {{ $messagerror }}
        </div>
        @endif
    </div>
</div>
<div class="fab-button bottom-right" style="margin-bottom:70px">
    <a href="/attendance/buatizin" class="fab">
        <ion-icon name="add-outline"></ion-icon>
    </a>
</div>
@endsection

