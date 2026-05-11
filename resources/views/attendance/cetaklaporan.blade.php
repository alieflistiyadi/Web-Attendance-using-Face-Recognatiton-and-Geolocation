<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>A4</title>

  <!-- Normalize or reset CSS with your favorite library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
  <style>
  @page { 
    size: A4 
}
    #title {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 18px;
        font-weight: bold;
    }

    .tabeldatasiswa {
        margin-top: 40px;
        
    }

    .tabeldatasiswa tr td {
        padding: 5px;
        
    }
    
    .tabelattendance {
        margin-top: 20px;
        width: 100%;
       border-collapse: collapse;
    }

    .tabelattendance tr th {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
        background-color: #dcdcdc;
    }

    .tabelattendance tr td {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
       font-size: 12px;
    }

    .foto {
        width: 60px;
        height: 80px;
        object-fit: cover;
    }  

  </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">

  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">

    <table style="width: 100%;">
        <tr>
            <td style="width: 30px;">
                <img src="{{ asset('assets/img/login/smart-logo.png') }}" width="70" height="70" alt="">
            </td>   
            <td>
                <span id="title">
                    LAPORAN PRESENSI SISWA <br>
                    PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br>
                    SMK SMART CIKARANG <br>
                </span>
                <span style="font-size: 12px;">
                    Perum vila Mutiara Cikarang 1 Blok D.8 No. 22, Ciantra, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17532
                </span>
            </td>
        </tr>
    </table>
    <table class="tabeldatasiswa">
        <tr>
            <td rowspan="6" style="width: 30%;">
                @php
                    $path = Storage::url('uploads/siswa/' .$siswa->foto);
                @endphp
                <img src="{{ url($path) }}" width="120" height="150" alt="">
            </td>
        </tr>
        <tr>
            <td> NIS</td>
            <td>:</td>
            <td> {{ $siswa->nis }} </td>
        </tr>
        <tr>
            <td> Nama Lengkap</td>
            <td>:</td>
            <td> {{ $siswa->nama_lengkap }} </td>
        </tr>
        <tr>
            <td> Kelas</td>
            <td>:</td>
            <td> {{ $siswa->kelas }} </td>
        </tr>
        <tr>
            <td> Jurusan</td>
            <td>:</td>
            <td> {{ $siswa->kode_jurusan }} </td>
        </tr>
        <tr>
            <td> No. Hp</td>
            <td>:</td>
            <td> {{ $siswa->no_hp }} </td>
        </tr>
    </table>
    <table class="tabelattendance">
        <tr>
            <th> No. </th>
            <th> Tanggal </th> 
            <th> Jam Masuk </th>
            <th> Foto </th>
            <th> Jam Pulang </th>
            <th> Foto </th>
            <th> Keterangan </th>
        </tr>
        @foreach ($attendance as $d)
                @php
                    $path_in = Storage::url('uploads/absensi/' .$d->foto_in);
                    $path_out = Storage::url('uploads/absensi/' .$d->foto_out);
                @endphp
            <tr>
                <td> {{ $loop->iteration }} </td>
                <td> {{ date('d-m-Y', strtotime($d->tgl_presensi)) }} </td>
                <td> {{ $d->jam_in }} </td>
                <td> <img src="{{ url($path_in) }}" class="foto" alt=""> </td>
                <td> {{ $d->jam_out != null ? $d->jam_out : 'Belum Absen' }} </td>
                <td> <img src="{{ url($path_out) }}" class="foto" alt""> </td>
                <td>
                    @if ($d->jam_in > '07:00')
                    Terlambat
                    @else
                    Tepat Waktu
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
  </section>

</body>

</html>