@extends('layouts.admin.tabler')
@section('content')
  <style>
    .dashboard-card {
      height: 130px;
    }

    .dashboard-card .card-body {
      height: 100%;
      display: flex;
      align-items: center;
    }

    .dashboard-card .avatar {
      width: 52px;
      height: 52px;
      min-width: 52px;
    }

    .dashboard-card .stat-number {
      font-size: 30px;
      font-weight: 700;
      line-height: 1;
      margin-bottom: 5px;
    }

    .dashboard-card .stat-label {
      font-size: 14px;
      line-height: 1.2;
      min-height: 34px;
      display: flex;
      align-items: center;
    }

    .dashboard-card:hover {
      transform: translateY(-3px);
      transition: .3s;
      box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
    }
  </style>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Ringkasan</div>
          <h2 class="page-title">Dashboard</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row row-cards">
        <div class="col-sm-6 col-lg-4 col-xl-2">
          <div class="card card-sm h-100">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-primary text-white avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icon-tabler-users">

                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M9 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                      <path d="M17 11v.01" />
                      <path d="M13 15h-4a4 4 0 0 0 -4 4v1h12v-1a4 4 0 0 0 -4 -4z" />
                      <path d="M21 12a4 4 0 0 0 -3 -3.87" />
                      <path d="M21 20v-1a4 4 0 0 0 -3 -3.85" />
                    </svg>
                  </span>
                </div>
                <div class="col">
                  <div class="stat-number">
                    {{ $totalSiswa }}
                  </div>
                  <div class="stat-label">
                    Total Siswa
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {{-- HADIR --}}
        <div class="col-sm-6 col-lg-4 col-xl-2">
          <div class="card card-sm h-100">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-success text-white avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icon-tabler-fingerprint">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3" />
                      <path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6" />
                      <path d="M12 11v2a14 14 0 0 0 2.5 8" />
                      <path d="M8 15a18 18 0 0 0 1.8 6" />
                      <path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95" />
                    </svg>
                  </span>
                </div>
                <div class="col">
                  <div class="stat-number">{{ $rekapattendance->jmlhadir ?? 0 }}</div>
                  <div class="stat-label">Siswa Hadir</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- IZIN --}}
        <div class="col-sm-6 col-lg-4 col-xl-2">
          <div class="card card-sm h-100">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-info text-white avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icon-tabler-file-invoice">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                      <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
                      <path d="M9 7l1 0" />
                      <path d="M9 13l6 0" />
                      <path d="M13 17l2 0" />
                    </svg>
                  </span>
                </div>
                <div class="col">
                  <div class="stat-number">{{ $jmlizin ?? 0 }}</div>
                  <div class="stat-label">Siswa Izin</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- SAKIT --}}
        <div class="col-sm-6 col-lg-4 col-xl-2">
          <div class="card card-sm h-100">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-warning text-white avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icon-tabler-mood-sick">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M12 21a9 9 0 1 1 0 -18a9 9 0 0 1 0 18" />
                      <path d="M9 10h-.01" />
                      <path d="M15 10h-.01" />
                      <path d="M8 16l1 -1l1.5 1l1.5 -1l1.5 1l1.5 -1l1 1" />
                    </svg>
                  </span>
                </div>
                <div class="col">
                  <div class="stat-number">{{ $rekapizin->jmlsakit ?? 0 }}</div>
                  <div class="stat-label">Siswa Sakit</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- TERLAMBAT --}}
        <div class="col-sm-6 col-lg-4 col-xl-2">
          <div class="card card-sm h-100">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-danger text-white avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icon-tabler-alarm">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M5 13a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                      <path d="M12 10l0 3l2 0" />
                      <path d="M7 4l-2.75 2" />
                      <path d="M17 4l2.75 2" />
                    </svg>
                  </span>
                </div>
                <div class="col">
                  <div class="stat-number">{{ $rekapattendance->jmlterlambat ?? 0 }}</div>
                  <div class="stat-label">Siswa Telat</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- ALPA --}}
        <div class="col-sm-6 col-lg-4 col-xl-2">
          <div class="card card-sm h-100">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-dark text-white avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icon-tabler-user-x">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                      <path d="M6 21v-2a4 4 0 0 1 4 -4h3" />
                      <path d="M22 22l-5 -5" />
                      <path d="M17 22l5 -5" />
                    </svg>
                  </span>
                </div>
                <div class="col">
                  <div class="stat-number">{{ $alpa ?? 0 }}</div>
                  <div class="stat-label">Siswa Alpa</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>{{-- end .row --}}

      {{-- PILIH JURUSAN --}}
      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                Rekap Statistik Absensi
              </h3>
            </div>

            <div class="card-body">
              <div class="row">

                @foreach($jurusanDashboard as $jurusan)

                  <div class="col-md-4 mb-3">
                    <a href="{{ url('/panel/jurusan/' . $jurusan->kode_jurusan) }}" class="text-decoration-none">

                      <div class="card card-sm h-100">
                        <div class="card-body text-center">

                          <h2 class="mb-1">
                            {{ $jurusan->nama_jurusan }}
                          </h2>

                          <div class="text-secondary">
                            {{ $jurusan->total }} Siswa
                          </div>

                        </div>
                      </div>

                    </a>
                  </div>

                @endforeach

              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">

        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                Progress Kehadiran Hari Ini
              </h3>
            </div>

            <div class="card-body">

              <h1 class="mb-3">
                {{ $persentaseKehadiran }}%
              </h1>

              <div class="progress progress-lg">
                <div class="progress-bar bg-success" style="width: {{ $persentaseKehadiran }}%">
                </div>
              </div>

              <div class="mt-3 stat-label">
                {{ $jmlhadir }} dari {{ $totalSiswa }}
                siswa sudah melakukan absensi
              </div>

            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
            <div class="card-body text-center">

              <h1 class="text-warning">
                {{ $pendingIzin }}
              </h1>

              <div class="h3">
                Menunggu Persetujuan Izin
              </div>

              <small class="stat-label">
                Menunggu verifikasi admin
              </small>

            </div>
          </div>
        </div>
        <div class="row mt-4">

          <div class="col-md-7">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  Aktivitas Absensi Terbaru
                </h3>
              </div>

              <div class="table-responsive">
                <table class="table table-vcenter">

                  <thead>
                    <tr>
                      <th>Nama</th>
                      <th>Tanggal</th>
                      <th>Jam Masuk</th>
                    </tr>
                  </thead>

                  <tbody>

                    @foreach($aktivitasTerbaru as $row)
                      <tr>
                        <td>{{ $row->nama_lengkap }}</td>
                        <td>{{ $row->tgl_presensi }}</td>
                        <td>{{ $row->jam_in }}</td>
                      </tr>
                    @endforeach

                  </tbody>

                </table>
              </div>

            </div>
          </div>

          <div class="col-md-5">
            <div class="card">

              <div class="card-header">
                <h3 class="card-title">
                  Top 5 Siswa Terlambat Bulan Ini
                </h3>
              </div>

              <div class="table-responsive">
                <table class="table">

                  <thead>
                    <tr>
                      <th>Nama</th>
                      <th>Total</th>
                    </tr>
                  </thead>

                  <tbody>

                    @foreach($topTerlambat as $item)

                      <tr>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>
                          <span class="badge bg-danger">
                            {{ $item->total }}x
                          </span>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>

                </table>
              </div>

            </div>
          </div>

        </div>
      </div>

    </div>{{-- end .container-xl --}}
  </div>{{-- end .page-body --}}

@endsection