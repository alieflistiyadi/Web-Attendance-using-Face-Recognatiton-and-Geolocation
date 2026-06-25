@extends('layouts.admin.tabler')
@section('content')

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Data Izin / Sakit
                    - Kelas {{ $kelas }}
            </h2>
          </div>
        </div>
      </div>
    </div>

    <div class="page-body">
      <div class="container-xl">

        @if (Session::get('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        @if (Session::get('warning'))
              <div class="alert alert-warning">{{ Session::get('warning') }}</div>
        @endif

        <div class="card mb-3">
          <div class="card-body">
            <form action="{{ url('/attendance/izinsakit/kelas/' . $kelas) }}" method="GET" autocomplete="off">
              <div class="row g-2">
                <div class="col-md-2">
                  <div class="input-icon">
                    <span class="input-icon-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icon-tabler-calendar-event">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" />
                        <path d="M16 3l0 4" /><path d="M8 3l0 4" />
                        <path d="M4 11l16 0" /><path d="M8 15h2v2h-2l0 -2" />
                      </svg>
                    </span>
                    <input type="text" id="dari" name="dari" class="form-control"
                      placeholder="Dari" value="{{ request('dari') }}">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-icon">
                    <span class="input-icon-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icon-tabler-calendar-event">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" />
                        <path d="M16 3l0 4" /><path d="M8 3l0 4" />
                        <path d="M4 11l16 0" /><path d="M8 15h2v2h-2l0 -2" />
                      </svg>
                    </span>
                    <input type="text" id="sampai" name="sampai" class="form-control"
                      placeholder="Sampai" value="{{ request('sampai') }}">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-icon">
                    <span class="input-icon-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icon-tabler-barcode">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 7v-1a2 2 0 0 1 2 -2h2" /><path d="M4 17v1a2 2 0 0 0 2 2h2" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v1" /><path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                        <path d="M5 11h1v2h-1l0 -2" /><path d="M10 11l0 2" />
                        <path d="M14 11h1v2h-1l0 -2" /><path d="M19 11l0 2" />
                      </svg>
                    </span>
                    <input type="text" name="nis" class="form-control"
                      placeholder="NIS" value="{{ request('nis') }}">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="input-icon">
                    <span class="input-icon-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icon-tabler-user">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                      </svg>
                    </span>
                    <input type="text" name="nama_lengkap" class="form-control"
                      placeholder="Nama Lengkap" value="{{ request('nama_lengkap') }}">
                  </div>
                </div>
                <div class="col-md-2">
                  <select name="status_approved" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="0" {{ request('status_approved') === '0' ? 'selected' : '' }}>Menunggu</option>
                    <option value="1" {{ request('status_approved') === '1' ? 'selected' : '' }}>Disetujui</option>
                    <option value="2" {{ request('status_approved') === '2' ? 'selected' : '' }}>Ditolak</option>
                  </select>
                </div>
                <div class="col-md-1">
                  <button type="submit" class="btn btn-primary w-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icon-tabler-search">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                      <path d="M21 21l-6 -6" />
                    </svg>
                    Cari
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>NIS</th>
                  <th>Nama Lengkap</th>
                  <th>Kelas</th>
                  <th>Jurusan</th>
                  <th>Status</th>
                  <th>Keterangan</th>
                  <th>Status Approved</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($izinsakit as $d)
                      <tr>
                        <td>{{ $loop->iteration + $izinsakit->firstItem() - 1 }}</td>
                        <td>{{ date('d-m-Y', strtotime($d->tanggal_izin)) }}</td>
                        <td>{{ $d->nis }}</td>
                        <td>{{ $d->nama_lengkap }}</td>
                        <td>{{ $d->kelas }}</td>
                        <td>{{ $d->kode_jurusan }}</td>
                        <td>{{ $d->status == 'i' ? 'Izin' : 'Sakit' }}</td>
                        <td>{{ $d->keterangan }}</td>
                        <td>
                          @if ($d->status_approved == 1)
                            <span class="badge bg-success">Disetujui</span>
                          @elseif ($d->status_approved == 2)
                            <span class="badge bg-danger">Ditolak</span>
                          @else
                            <span class="badge bg-warning">Menunggu</span>
                          @endif
                        </td>
                        <td>
                          @if ($d->status_approved == 0)
                            <a href="#" class="btn btn-sm btn-primary approved" id_izinsakit="{{ $d->id }}">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icon-tabler-external-link">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                <path d="M11 13l9 -9" /><path d="M15 4h5v5" />
                              </svg>
                              Proses
                            </a>
                          @else
                            <a href="/attendance/{{ $d->id }}/batalkanizinsakit" class="btn btn-sm btn-danger">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-square-x">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M19 2h-14a3 3 0 0 0 -3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3 -3v-14a3 3 0 0 0 -3 -3zm-9.387 6.21l.094 .083l2.293 2.292l2.293 -2.292a1 1 0 0 1 1.497 1.32l-.083 .094l-2.292 2.293l2.292 2.293a1 1 0 0 1 -1.32 1.497l-.094 -.083l-2.293 -2.292l-2.293 2.292a1 1 0 0 1 -1.497 -1.32l.083 -.094l2.292 -2.293l-2.292 -2.293a1 1 0 0 1 1.32 -1.497z" />
                              </svg>
                              Batalkan
                            </a>
                          @endif
                        </td>
                      </tr>
                @empty
                      <tr>
                        <td colspan="10" class="text-center text-muted py-3">Tidak ada data izin/sakit untuk kelas {{ $kelas }}</td>
                      </tr>
                @endforelse
              </tbody>
            </table>
            {{ $izinsakit->links('vendor.pagination.bootstrap-5') }}
          </div>
        </div>

      </div>
    </div>

    {{-- Modal Approve --}}
    <div class="modal modal-blur fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Proses Izin/Sakit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="{{ url('/attendance/approveizinsakit') }}" method="POST">
              @csrf
              <input type="hidden" id="id_izinsakit_form" name="id_izinsakit_form">
              <div class="row">
                <div class="col-12">
                  <select name="status_approved" id="status_select" class="form-select">
                    <option value="1">Disetujui</option>
                    <option value="2">Ditolak</option>
                  </select>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icon-tabler-send">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M10 14l11 -11" />
                      <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                    </svg>
                    Simpan
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

@endsection

@push('myscript')
    <script>
      $(function () {
        $(".approved").click(function (e) {
          e.preventDefault();
          var id_izinsakit = $(this).attr("id_izinsakit");
          $("#id_izinsakit_form").val(id_izinsakit);
          $("#modal-izinsakit").modal('show');
        });

        flatpickr("#dari", { dateFormat: "d-m-Y" });
        flatpickr("#sampai", { dateFormat: "d-m-Y" });
      });
    </script>
@endpush