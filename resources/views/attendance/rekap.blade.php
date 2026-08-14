@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">
          Rekap Presensi Bulanan
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row">

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <form action="/attendance/cetakrekap" target="_blank" method="POST">
              @csrf

              {{-- JURUSAN --}}
              <div class="mb-2">
                <select name="jurusan" id="jurusan" class="form-select" required>
                    <option value="">Pilih Jurusan</option>

                    @foreach($jurusan as $j)
                        <option value="{{ $j->kode_jurusan }}">
                            {{ $j->nama_jurusan }}
                        </option>
                    @endforeach

                </select>
            </div>

              {{-- KELAS --}}
              <div class="mb-2">
                <select name="kelas" id="kelas" class="form-select" required>
                    <option value="">Pilih Kelas</option>
                    @for ($i = 10; $i <= 12; $i++)
                        <option value="{{ $i }}">Kelas {{ $i }}</option>
                    @endfor
                </select>
              </div>

              {{-- MATA PELAJARAN --}}
              <div class="mb-2">
                  <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="form-select" required>
                      <option value="">Pilih Mata Pelajaran</option>

                      @foreach($mataPelajaran as $m)
                          <option value="{{ $m->id }}">
                              {{ $m->kode_mapel }} - {{ $m->nama_mapel }}
                          </option>
                      @endforeach
                  </select>
              </div>

              {{-- BULAN --}}
              <div class="mb-2">
                <select name="bulan" id="bulan" class="form-select" required>
                    <option value="">Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                            {{ $namabulan[$i] }}
                        </option>
                    @endfor
                </select>
              </div>

              {{-- TAHUN --}}
              <div class="mb-3">
                <select name="tahun" id="tahun" class="form-select" required>
                    <option value="">Tahun</option>
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
              </div>

              {{-- BUTTON --}}
                <div class="row">
                  <div class="col-12 d-flex justify-content-center">
                    <button type="submit" name="cetak" class="btn btn-primary px-5">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                            <path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4" />
                                        </svg>
                      Cetak Laporan
                    </button>
                  </div>
                </div>
              </div>

            </form>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection