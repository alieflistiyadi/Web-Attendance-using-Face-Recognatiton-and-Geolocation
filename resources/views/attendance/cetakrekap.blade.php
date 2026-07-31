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
        font-size: 10px;
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
<body class="A4 landscape">
    
    <?php
    function selisih($jam_masuk, $jam_keluar)
    {
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
  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">

    <table style="width: 100%;">
        <tr>
            <td style="width: 30px;">
                <img src="{{ asset('assets/img/login/smksmart.png') }}" width="70" height="70" alt="">
            </td>   
            <td>
                <span id="title">
                    REKAP PRESENSI SISWA <br>
                    PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br>
                    SMK SMART CIKARANG <br>
                </span>
                <span style="font-size: 12px;">
                    Perum vila Mutiara Cikarang 1 Blok D.8 No. 22, Ciantra, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17532
                </span>
            </td>
        </tr>
    </table>
    
    <table class="tabelattendance">
        <tr>
            <th rowSpan="2">NIS</th>
            <th rowSpan="2">Nama Lengkap</th>
            <th colSpan="31">Tanggal</th>
            <th rowSpan="2">TH</th>
            <th rowSpan="2">TL</th>
        </tr>
        <tr>
           <?php
              for ($i = 1; $i <= 31; $i++) {
                echo "<th>" . $i . "</th>";
              }
              ?>
        </tr>
        @foreach ($rekap as $d)
        <tr>
            <td>{{ $d->nis }}</td>
            <td>{{ $d->nama_lengkap }}</td>
            <?php
            $totalhadir = 0;
            $totalterlambat = 0;
              for ($i = 1; $i <= 31; $i++) {
                $tgl = 'tgl_' . $i;
                if (empty($d->$tgl)) {
                    $hadir = ['', ''];
                    $totalhadir += 0;
                } else {
                $hadir = explode("-", $d->$tgl);
                $totalhadir += 1;
                if ($hadir[0] > "07:00:00") {
                    $totalterlambat += 1;
                }
                }
              ?>

              <td> 
                <span style="color: {{ $hadir[0] > "07:00:00" ? "red" : '' }}">{{ $hadir[0] }}</span>
                <span style="color: {{ $hadir[1] < "16:00:00" ? "red" : '' }}">{{ $hadir[1] }}</span>
            </td>
              <?php
                }
              ?>
            <td>{{ $totalhadir }}</td>
            <td>{{ $totalterlambat }}</td>
        </tr>
        @endforeach
    </table>

    <table width="100%" style="margin-top:100px">
    <tr>
        <td style="text-align: right">
            Cikarang, {{ date('d-m-Y') }}
            <br><br><br><br>

            <u>Nama Kepala Sekolah</u><br>
            <i><b>Kepala Sekolah</b></i>
        </td>
    </tr>
</table>
  </section>

</body>

</html>