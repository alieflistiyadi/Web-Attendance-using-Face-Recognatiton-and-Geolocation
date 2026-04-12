@extends('layouts.attendance')
@section('header')

    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Edit Profile</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

@endsection

@section('content')
<div class="row" style="margin-top:4rem">
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
    <form action="/attendance/{{ $siswa->nis }}/updateprofile" method="POST" enctype="multipart/form-data" style="margin-top: 4rem;">
        @csrf
        <div class="col">
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="text" class="form-control" value="{{ $siswa->nama_lengkap }}" name="nama_lengkap" placeholder="Nama Lengkap"
                        autoxomplete="off">
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="text" class="form-control" value="{{ $siswa->no_hp }}" name="no_hp" placeholder="No. HP" autoxomplete="off">
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password" placeholder="Password" autoxomplete="off">
                </div>
            </div>

            <div class="custom-file-upload" id="fileupload1">
                <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg">
                <label for="fileuploadInput">
                    <span>
                        <strong>
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                            <i>Upload Foto Profile</i>
                        </strong>
                    </span>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
                </div>
            </div>
    </form>
@endsection