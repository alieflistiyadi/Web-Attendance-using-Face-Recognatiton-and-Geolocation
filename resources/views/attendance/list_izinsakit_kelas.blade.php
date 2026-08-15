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
                      <path d="M16 3l0 4" />
                      <path d="M8 3l0 4" />
                      <path d="M4 11l16 0" />
                      <path d="M8 15h2v2h-2l0 -2" />
                    </svg>
                  </span>
                  <input type="text" id="dari" name="dari" class="form-control" placeholder="Dari"
                    value="{{ request('dari') }}">
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
                      <path d="M16 3l0 4" />
                      <path d="M8 3l0 4" />
                      <path d="M4 11l16 0" />
                      <path d="M8 15h2v2h-2l0 -2" />
                    </svg>
                  </span>
                  <input type="text" id="sampai" name="sampai" class="form-control" placeholder="Sampai"
                    value="{{ request('sampai') }}">
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
                  <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap"
                    value="{{ request('nama_lengkap') }}">
                </div>
              </div>
              <div class="col-md-2">
                <select name="kode_jurusan" class="form-select">
                  <option value="">Semua Jurusan</option>

                  @foreach($jurusan as $d)
                    <option value="{{ $d->kode_jurusan }}" {{ request('kode_jurusan') == $d->kode_jurusan ? 'selected' : '' }}>
                      {{ $d->nama_jurusan }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="status_approved" class="form-select">
                  <option value="">Semua Status</option>
                  <option value="0" {{ request('status_approved') === '0' ? 'selected' : '' }}>
                    Menunggu
                  </option>
                  <option value="1" {{ request('status_approved') === '1' ? 'selected' : '' }}>
                    Disetujui
                  </option>
                  <option value="2" {{ request('status_approved') === '2' ? 'selected' : '' }}>
                    Ditolak
                  </option>
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

<td>{{ $d->nama_jurusan }}</td>

<td>{{ $d->status == 'i' ? 'Izin' : 'Sakit' }}</td>

<td>{{ $d->keterangan }}</td>
                  <td>
                    @if ($d->detail_status_approved == 0)
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif ($d->detail_status_approved == 1)
                        <span class="badge bg-success">Disetujui</span>
                    @elseif ($d->detail_status_approved == 2)
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                  </td>
                  <td>
                    @if ($d->detail_status_approved == 0)
                      @php
                          $dokumen = $d->status == 'i'
                              ? $d->surat_izin
                              : $d->surat_sakit;
                          $folderDokumen = $d->status == 'i'
                              ? 'surat_izin'
                              : 'surat_sakit';
                          $urlDokumen = $dokumen
                              ? asset('storage/uploads/' . $folderDokumen . '/' . $dokumen)
                              : '';
                      @endphp
                      <a href="#"
                        class="btn btn-sm btn-primary approved"
                        id_izinsakit="{{ $d->id }}"
                        data-nama="{{ $d->nama_lengkap }}"
                        data-nis="{{ $d->nis }}"
                        data-tanggal="{{ date('d-m-Y', strtotime($d->tanggal_izin)) }}"
                        data-jenis="{{ $d->status == 'i' ? 'Izin' : 'Sakit' }}"
                        data-keterangan="{{ $d->keterangan }}"
                        data-dokumen="{{ $urlDokumen }}"
                        data-nama-dokumen="{{ $dokumen }}">
                          <svg xmlns="http://www.w3.org/2000/svg"
                              width="24"
                              height="24"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              class="icon icon-tabler icon-tabler-eye">
                              <path stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none"/>
                              <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                              <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6
                                      c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                          </svg>
                          Proses
                      </a>
                    @else
                      <a href="/attendance/{{ $d->id }}/batalkanizinsakit" class="btn btn-sm btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                          class="icon icon-tabler icons-tabler-filled icon-tabler-square-x">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path
                            d="M19 2h-14a3 3 0 0 0 -3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3 -3v-14a3 3 0 0 0 -3 -3zm-9.387 6.21l.094 .083l2.293 2.292l2.293 -2.292a1 1 0 0 1 1.497 1.32l-.083 .094l-2.292 2.293l2.292 2.293a1 1 0 0 1 -1.32 1.497l-.094 -.083l-2.293 -2.292l-2.293 2.292a1 1 0 0 1 -1.497 -1.32l.083 -.094l2.292 -2.293l-2.292 -2.293a1 1 0 0 1 1.32 -1.497z" />
                        </svg>
                        Batalkan
                      </a>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center text-muted py-3">Tidak ada data izin/sakit untuk kelas {{ $kelas }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
          {{ $izinsakit->links('vendor.pagination.bootstrap-5') }}
        </div>
      </div>

    </div>
  </div>

  {{-- Modal Proses Izin/Sakit --}}
  <div class="modal modal-blur fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">
                      <svg xmlns="http://www.w3.org/2000/svg"
                          width="24"
                          height="24"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          class="icon icon-tabler icon-tabler-file-search">
                          <path stroke="none"
                                d="M0 0h24v24H0z"
                                fill="none"/>
                          <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                          <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14
                                  a2 2 0 0 1 2 -2h7l5 5v11
                                  a2 2 0 0 1 -2 2"/>
                          <path d="M11.5 14.5
                                  a2.5 2.5 0 1 0 5 0
                                  a2.5 2.5 0 0 0 -5 0"/>
                          <path d="M13.3 16.3l1.7 1.7"/>
                      </svg>
                      Proses Izin/Sakit
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">

                  {{-- Detail Siswa --}}
                  <div class="card mb-3">
                      <div class="card-header">
                          <h3 class="card-title">
                              Detail Pengajuan
                          </h3>
                      </div>
                      <div class="card-body">
                          <div class="row">
                              <div class="col-md-6 mb-3">
                                  <label class="form-label text-muted">
                                      Nama Siswa
                                  </label>
                                  <div class="fw-bold"
                                      id="detail_nama">
                                  </div>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label class="form-label text-muted">
                                      NIS
                                  </label>
                                  <div class="fw-bold"
                                      id="detail_nis">
                                  </div>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label class="form-label text-muted">
                                      Tanggal
                                  </label>
                                  <div class="fw-bold"
                                      id="detail_tanggal">
                                  </div>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label class="form-label text-muted">
                                      Jenis
                                  </label>
                                  <div>
                                      <span class="badge bg-blue"
                                            id="detail_jenis">
                                      </span>
                                  </div>
                              </div>
                              <div class="col-12">
                                  <label class="form-label text-muted">
                                      Keterangan
                                  </label>
                                  <div class="alert alert-secondary mb-0"
                                      id="detail_keterangan">
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  {{-- Dokumen --}}
                  <div class="card mb-3">
                      <div class="card-header">
                          <h3 class="card-title">
                              Dokumen / Bukti
                          </h3>
                      </div>
                      <div class="card-body">
                          <div id="dokumen_container">
                              {{-- Diisi melalui Javascript --}}
                          </div>
                      </div>
                  </div>

                  {{-- Approval --}}
                  <form action="{{ url('/attendance/approveizinsakit') }}" method="POST">
                      @csrf
                      <input type="hidden" id="id_izinsakit_form" name="id_izinsakit_form">
                      <div class="mb-3">
                          <label class="form-label">
                              Status Persetujuan
                          </label>
                          <select name="status_approved" id="status_select" class="form-select">
                              <option value="1">
                                  Disetujui
                              </option>
                              <option value="2">
                                  Ditolak
                              </option>
                          </select>
                      </div>
                      <div class="alert alert-warning">
                          <svg xmlns="http://www.w3.org/2000/svg"
                              width="24"
                              height="24"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              class="icon icon-tabler icon-tabler-alert-triangle">
                              <path stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none"/>
                              <path d="M10.24 3.75l-7.5 13
                                      a2 2 0 0 0 1.76 3h15
                                      a2 2 0 0 0 1.76 -3l-7.5 -13
                                      a2 2 0 0 0 -3.52 0"/>
                              <path d="M12 9v4"/>
                              <path d="M12 17h.01"/>
                          </svg>
                          Pastikan dokumen/bukti pengajuan telah diperiksa sebelum memberikan persetujuan.
                      </div>
                      <button class="btn btn-primary w-100" type="submit">
                          <svg xmlns="http://www.w3.org/2000/svg"
                              width="24"
                              height="24"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              class="icon icon-tabler icon-tabler-check">
                              <path stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none"/>
                              <path d="M5 12l5 5l10 -10"/>
                          </svg>
                          Simpan Keputusan
                      </button>
                  </form>
              </div>
          </div>
      </div>
  </div>

@endsection

@push('myscript')
  <script>
      $(document).ready(function () {

          $('.approved').on('click', function (e) {

              e.preventDefault();

              var button = $(this);

              var id = button.attr('id_izinsakit');
              var nama = button.attr('data-nama');
              var nis = button.attr('data-nis');
              var tanggal = button.attr('data-tanggal');
              var jenis = button.attr('data-jenis');
              var keterangan = button.attr('data-keterangan');
              var dokumen = button.attr('data-dokumen');
              var namaDokumen = button.attr('data-nama-dokumen');


              // ==========================
              // DATA SISWA
              // ==========================

              $('#id_izinsakit_form').val(id);
              $('#detail_nama').text(nama);
              $('#detail_nis').text(nis);
              $('#detail_tanggal').text(tanggal);
              $('#detail_jenis').text(jenis);
              $('#detail_keterangan').text(keterangan);


              // ==========================
              // DOKUMEN
              // ==========================

              var container = $('#dokumen_container');

              container.empty();


              if (!dokumen || dokumen === '') {

                  container.html(
                      '<div class="alert alert-danger mb-0">' +
                      '<strong>Dokumen tidak ditemukan.</strong><br>' +
                      'Siswa belum mengunggah dokumen bukti.' +
                      '</div>'
                  );

              } else {

                  var extension = '';

                  if (namaDokumen && namaDokumen.indexOf('.') !== -1) {

                      extension = namaDokumen
                          .split('.')
                          .pop()
                          .toLowerCase();

                  }


                  // ==========================
                  // GAMBAR
                  // ==========================

                  if (
                      extension === 'jpg' ||
                      extension === 'jpeg' ||
                      extension === 'png' ||
                      extension === 'webp'
                  ) {

                      container.html(
                          '<div class="text-center">' +

                          '<div class="mb-3">' +

                          '<img src="' + dokumen + '"' +
                          ' class="img-fluid rounded border"' +
                          ' style="max-height:500px;">' +

                          '</div>' +

                          '<a href="' + dokumen + '"' +
                          ' target="_blank"' +
                          ' class="btn btn-outline-primary">' +

                          'Buka Ukuran Penuh' +

                          '</a>' +

                          '</div>'
                      );

                  }


                  // ==========================
                  // PDF
                  // ==========================

                  else if (extension === 'pdf') {

                      container.html(
                          '<div>' +

                          '<iframe src="' + dokumen + '"' +
                          ' width="100%"' +
                          ' height="500"' +
                          ' style="border:1px solid #dee2e6;' +
                          'border-radius:8px;">' +

                          '</iframe>' +

                          '<div class="text-center mt-3">' +

                          '<a href="' + dokumen + '"' +
                          ' target="_blank"' +
                          ' class="btn btn-outline-primary">' +

                          'Buka PDF' +

                          '</a>' +

                          '</div>' +

                          '</div>'
                      );

                  }


                  // ==========================
                  // FILE LAIN
                  // ==========================

                  else {

                      container.html(
                          '<div class="text-center">' +

                          '<div class="mb-3">' +

                          '<i class="ti ti-file" ' +
                          'style="font-size:60px;"></i>' +

                          '</div>' +

                          '<p class="text-muted">' +
                          namaDokumen +
                          '</p>' +

                          '<a href="' + dokumen + '"' +
                          ' target="_blank"' +
                          ' class="btn btn-primary">' +

                          'Buka Dokumen' +

                          '</a>' +

                          '</div>'
                      );

                  }

              }


              // ==========================
              // BUKA MODAL BOOTSTRAP 5
              // ==========================

              var modalElement = document.getElementById('modal-izinsakit');

              if (modalElement) {

                  var modal = bootstrap.Modal.getOrCreateInstance(modalElement);

                  modal.show();

              } else {

                  console.error('Modal #modal-izinsakit tidak ditemukan.');

              }

          });


          // ==========================
          // FLATPICKR
          // ==========================

          if (document.getElementById('dari')) {

              flatpickr('#dari', {
                  dateFormat: 'd-m-Y'
              });

          }

          if (document.getElementById('sampai')) {

              flatpickr('#sampai', {
                  dateFormat: 'd-m-Y'
              });

          }

      });
  </script>
@endpush