@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">
          Rekap Presensi
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row">

      <div class="col-6">
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
                <div class="col-6">
                  <button type="submit" name="cetak" class="btn btn-primary w-100">
                    📄 Cetak
                  </button>
                </div>

                <div class="col-6">
                  <button type="submit" name="exportexcel" class="btn btn-success w-100">
                    📥 Excel
                  </button>
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