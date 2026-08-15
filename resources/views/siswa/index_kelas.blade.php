@extends('layouts.admin.tabler')

@section('content')

  {{-- ==========================================================
    PAGE HEADER
    =========================================================== --}}
    <div class="page-header d-print-none">

      <div class="container-xl">

        <div class="row g-2 align-items-center">

          <div class="col">

            <h2 class="page-title">
              Data Siswa Kelas {{ $kelas }}
            </h2>

          </div>

        </div>

      </div>

    </div>


    {{-- ==========================================================
    PAGE BODY
    =========================================================== --}}
    <div class="page-body">

      <div class="container-xl">

        <div class="row">

          <div class="col-12">

            <div class="card">

              <div class="card-body">


                {{-- ==================================================
                ALERT ERROR
                =================================================== --}}
                @if ($errors->any())

                  <div class="alert alert-danger">

                    {{ $errors->first() }}

                  </div>

                @endif


                {{-- ==================================================
                ALERT SUCCESS / WARNING
                =================================================== --}}
                <div class="row">

                  <div class="col-12">


                    {{-- SUCCESS --}}
                    @if (Session::get('success'))

                      <div class="alert alert-success">

                        {{ Session::get('success') }}

                      </div>

                    @endif


                    {{-- WARNING --}}
                    @if (Session::get('warning'))

                      <div class="alert alert-warning">

                        {{ Session::get('warning') }}

                      </div>

                    @endif


                    {{-- ==================================================
                    HASIL IMPORT
                    =================================================== --}}
                    @if (session('success_import'))

                      <div class="alert alert-success">

                        <strong>
                          Import berhasil!
                        </strong>

                        <br>

                        Berhasil:
                        {{ session('success_import') }}
                        data

                        <br>

                        Gagal:
                        {{ session('failed_import') }}
                        data

                      </div>

                    @endif


                    {{-- ==================================================
                    ERROR IMPORT
                    =================================================== --}}
                    @if (session('errors_import'))

                      <div class="alert alert-danger">

                        <strong>
                          Data yang gagal diimport:
                        </strong>

                        <ul class="mb-0">

                          @foreach (session('errors_import') as $error)

                            <li>
                              {{ $error }}
                            </li>

                          @endforeach

                        </ul>

                      </div>

                    @endif

                  </div>

                </div>


                {{-- ==================================================
                BUTTON
                =================================================== --}}
                <div class="row">

                  <div class="col-12">


                    {{-- TAMBAH DATA --}}
                    <a href="#"
                       class="btn btn-primary"
                       id="btnTambahsiswa">

                      <svg xmlns="http://www.w3.org/2000/svg"
                           width="24"
                           height="24"
                           viewBox="0 0 24 24"
                           fill="none"
                           stroke="currentColor"
                           stroke-width="2"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           class="icon icon-tabler icon-tabler-plus">

                        <path stroke="none"
                              d="M0 0h24v24H0z"
                              fill="none" />

                        <path d="M12 5l0 14" />

                        <path d="M5 12l14 0" />

                      </svg>

                      Tambah Data

                    </a>


                    {{-- IMPORT EXCEL --}}
                    <a href="#"
                       class="btn btn-success"
                       id="btnImport">

                      <svg xmlns="http://www.w3.org/2000/svg"
                           width="24"
                           height="24"
                           viewBox="0 0 24 24"
                           fill="none"
                           stroke="currentColor"
                           stroke-width="2"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           class="icon icon-tabler icon-tabler-file-import">

                        <path stroke="none"
                              d="M0 0h24v24H0z"
                              fill="none" />

                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />

                        <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v5" />

                        <path d="M12 17h7" />

                        <path d="M16 13l4 4l-4 4" />

                        <path d="M5 17v2a2 2 0 0 0 2 2h3" />

                      </svg>

                      Import Excel

                    </a>


                    {{-- DOWNLOAD TEMPLATE --}}
                    <a href="{{ route('siswa.template') }}"
                       class="btn btn-secondary">

                      Download Template

                    </a>

                  </div>

                </div>


                {{-- ==================================================
                SEARCH
                =================================================== --}}
                <div class="row mt-3">

                  <div class="col-12">

                    <form action="/siswa/kelas/{{ $kelas }}"
                          method="GET">

                      <div class="row">


                        {{-- INPUT SEARCH --}}
                        <div class="col-md-10">

                          <div class="form-group">

                            <input type="text"
                                   name="nama_lengkap"
                                   class="form-control"
                                   placeholder="Cari Nama Siswa"
                                   value="{{ Request('nama_lengkap') }}">

                          </div>

                        </div>


                        {{-- BUTTON SEARCH --}}
                        <div class="col-md-2">

                          <button type="submit"
                                  class="btn btn-primary w-100">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="24"
                                 height="24"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 class="icon icon-tabler icon-tabler-search">

                              <path stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none" />

                              <path d="M3 10a7 7 0 1 0 14 0a7 7 0 0 0 -14 0" />

                              <path d="M21 21l-6 -6" />

                            </svg>

                            Cari

                          </button>

                        </div>

                      </div>

                    </form>

                  </div>

                </div>


                {{-- ==================================================
                TABLE DATA SISWA
                =================================================== --}}
                <div class="row mt-3">

                  <div class="col-12">

                    <div class="table-responsive">

                      <table class="table table-bordered">

                        <thead class="table-light">

                          <tr>

                            <th class="fw-bold text-dark"
                                style="font-size:13px;">

                              No

                            </th>

                            <th class="fw-bold text-dark"
                                style="font-size:13px;">

                              NIS

                            </th>

                            <th class="fw-bold text-dark"
                                style="font-size:13px;">

                              Nama Lengkap

                            </th>

                            <th class="fw-bold text-dark"
                                style="font-size:13px;">

                              Kelas

                            </th>

                            <th class="fw-bold text-dark"
                                style="font-size:13px;">

                              Jurusan

                            </th>

                            <th class="fw-bold text-dark"
                                style="font-size:13px;">

                              No. HP

                            </th>

                            <th class="fw-bold text-dark"
                                style="font-size:13px;">

                              Aksi

                            </th>

                          </tr>

                        </thead>


                        <tbody>


                          @forelse ($siswa as $d)

                            <tr>


                              {{-- NO --}}
                              <td>

                                {{ $loop->iteration + $siswa->firstItem() - 1 }}

                              </td>


                              {{-- NIS --}}
                              <td>

                                {{ $d->nis }}

                              </td>


                              {{-- NAMA --}}
                              <td>

                                {{ $d->nama_lengkap }}

                              </td>


                              {{-- KELAS --}}
                              <td>

                                {{ $d->kelas }}

                              </td>


                              {{-- JURUSAN --}}
                              <td>

                                @if ($d->kode_jurusan == 'TJKT')

                                  Teknik Jaringan Komputer dan Telekomunikasi

                                @elseif ($d->kode_jurusan == 'TM')

                                  Teknik Mesin

                                @else

                                  -

                                @endif

                              </td>


                              {{-- NO HP --}}
                              <td>

                                {{ $d->no_hp }}

                              </td>


                              {{-- AKSI --}}
                              <td>

                                <div class="btn-group">


                                  {{-- EDIT --}}
                                  <a href="#"
                                     class="edit btn btn-info btn-sm"
                                     nis="{{ $d->nis }}"
                                     redirect_kelas="{{ $kelas }}"
                                     title="Edit">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         width="24"
                                         height="24"
                                         viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="2"
                                         stroke-linecap="round"
                                         stroke-linejoin="round"
                                         class="icon icon-tabler icon-tabler-edit">

                                      <path stroke="none"
                                            d="M0 0h24v24H0z"
                                            fill="none" />

                                      <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />

                                      <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" />

                                      <path d="M16 5l3 3" />

                                    </svg>

                                  </a>


                                  {{-- DELETE --}}
                                  <form action="/siswa/{{ $d->nis }}/delete"
                                        method="POST"
                                        style="margin-left:5px">

                                    @csrf

                                    <input type="hidden"
                                           name="redirect_kelas"
                                           value="{{ $kelas }}">

                                    <button type="submit"
                                            class="btn btn-danger btn-sm delete-confirm"
                                            title="Hapus">

                                      <svg xmlns="http://www.w3.org/2000/svg"
                                           width="24"
                                           height="24"
                                           viewBox="0 0 24 24"
                                           fill="none"
                                           stroke="currentColor"
                                           stroke-width="2"
                                           stroke-linecap="round"
                                           stroke-linejoin="round"
                                           class="icon icon-tabler icon-tabler-trash">

                                        <path stroke="none"
                                              d="M0 0h24v24H0z"
                                              fill="none" />

                                        <path d="M4 7l16 0" />

                                        <path d="M10 11l0 6" />

                                        <path d="M14 11l0 6" />

                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />

                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />

                                      </svg>

                                    </button>

                                  </form>

                                </div>

                              </td>

                            </tr>


                          @empty

                            <tr>

                              <td colspan="7"
                                  class="text-center text-muted py-4">

                                Tidak ada data siswa untuk kelas

                                <strong>
                                  {{ $kelas }}
                                </strong>.

                              </td>

                            </tr>

                          @endforelse


                        </tbody>

                      </table>

                    </div>


                    {{-- PAGINATION --}}
                    {{ $siswa->links('vendor.pagination.bootstrap-5') }}

                  </div>

                </div>


              </div>

            </div>

          </div>

        </div>

      </div>

    </div>



    {{-- ==========================================================
    MODAL TAMBAH SISWA
    =========================================================== --}}
    <div class="modal modal-blur fade"
         id="modal-inputsiswa"
         tabindex="-1"
         role="dialog"
         aria-hidden="true">

      <div class="modal-dialog modal-dialog-centered"
           role="document">

        <div class="modal-content">


          {{-- HEADER --}}
          <div class="modal-header">

            <h5 class="modal-title">

              Tambah Data Siswa Kelas {{ $kelas }}

            </h5>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">

            </button>

          </div>


          {{-- BODY --}}
          <div class="modal-body">


            <form action="{{ url('/siswa/store') }}"
                  method="POST"
                  id="formsiswa"
                  enctype="multipart/form-data">

              @csrf


              {{-- ==================================================
              KELAS
              =================================================== --}}
              <input type="hidden"
                     name="kelas"
                     value="{{ $kelas }}">


              {{-- REDIRECT --}}
              <input type="hidden"
                     name="redirect_kelas"
                     value="{{ $kelas }}">


              {{-- ==================================================
              KODE JURUSAN
              =================================================== --}}
              <input type="hidden"
                     name="kode_jurusan"
                     id="kode_jurusan_modal"
                     value="">



              {{-- ==================================================
              NIS
              =================================================== --}}
              <div class="row">

                <div class="col-12">

                  <div class="input-icon mb-3">

                    <span class="input-icon-addon">

                      <svg xmlns="http://www.w3.org/2000/svg"
                           width="24"
                           height="24"
                           viewBox="0 0 24 24"
                           fill="none"
                           stroke="currentColor"
                           stroke-width="2"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           class="icon icon-tabler icon-tabler-id-badge-2">

                        <path stroke="none"
                              d="M0 0h24v24H0z"
                              fill="none" />

                        <path d="M7 12h3v4h-3l0 -4" />

                        <path d="M10 6h-6a1 1 0 0 0 -1 1v12a2 2 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a2 2 0 0 0 -1 -1h-6" />

                        <path d="M10 4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -3" />

                        <path d="M14 16h2" />

                        <path d="M14 12h4" />

                      </svg>

                    </span>

                    <input type="text"
                           id="nis"
                           class="form-control"
                           name="nis"
                           placeholder="NIS">

                  </div>

                </div>

              </div>



              {{-- ==================================================
              NAMA
              =================================================== --}}
              <div class="row">

                <div class="col-12">

                  <div class="input-icon mb-3">

                    <span class="input-icon-addon">

                      <svg xmlns="http://www.w3.org/2000/svg"
                           class="icon"
                           width="24"
                           height="24"
                           viewBox="0 0 24 24"
                           stroke-width="2"
                           stroke="currentColor"
                           fill="none"
                           stroke-linecap="round"
                           stroke-linejoin="round">

                        <path stroke="none"
                              d="M0 0h24v24H0z"
                              fill="none"></path>

                        <path d="M12 7m-4 0a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>

                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>

                      </svg>

                    </span>

                    <input type="text"
                           id="nama_lengkap_modal"
                           class="form-control"
                           name="nama_lengkap"
                           placeholder="Nama Siswa">

                  </div>

                </div>

              </div>



              {{-- ==================================================
              KELAS
              =================================================== --}}
              <div class="row mt-2">

                <div class="col-12">

                  <label class="form-label">
                    Kelas
                  </label>

                  <input type="text"
                         class="form-control"
                         value="Kelas {{ $kelas }}"
                         disabled>

                </div>

              </div>



              {{-- ==================================================
              JURUSAN
              =================================================== --}}
              <div class="row mt-3">

                <div class="col-12">

                  <label class="form-label">
                    Jurusan
                  </label>

                  <input type="text"
                         class="form-control"
                         id="nama_jurusan_modal"
                         value=""
                         disabled>

                </div>

              </div>



              {{-- ==================================================
              NO HP
              =================================================== --}}
              <div class="row mt-3">

                <div class="col-12">

                  <div class="input-icon mb-3">

                    <span class="input-icon-addon">

                      <svg xmlns="http://www.w3.org/2000/svg"
                           width="24"
                           height="24"
                           viewBox="0 0 24 24"
                           fill="none"
                           stroke="currentColor"
                           stroke-width="2"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           class="icon icon-tabler icon-tabler-phone">

                        <path stroke="none"
                              d="M0 0h24v24H0z"
                              fill="none" />

                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />

                      </svg>

                    </span>

                    <input type="text"
                           id="no_hp_modal"
                           class="form-control"
                           name="no_hp"
                           placeholder="+628xxxxxxxxxx"
                           maxlength="18">

                  </div>

                </div>

              </div>



              {{-- ==================================================
              SIMPAN
              =================================================== --}}
              <div class="row mt-2">

                <div class="col-12">

                  <button type="submit"
                          class="btn btn-primary w-100">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="24"
                         height="24"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         class="icon icon-tabler icon-tabler-send">

                      <path stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none" />

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



    {{-- ==========================================================
    MODAL IMPORT EXCEL
    =========================================================== --}}
    <div class="modal fade"
         id="modal-import"
         tabindex="-1">

      <div class="modal-dialog">

        <div class="modal-content">


          <form action="{{ route('siswa.import') }}"
                method="POST"
                enctype="multipart/form-data">

            @csrf


            <div class="modal-header">

              <h5 class="modal-title">
                Import Data Siswa
              </h5>

              <button type="button"
                      class="btn-close"
                      data-bs-dismiss="modal"
                      aria-label="Close">

              </button>

            </div>


            <div class="modal-body">

              <div class="mb-3">

                <label class="form-label">
                  File Excel
                </label>

                <input type="file"
                       name="file"
                       class="form-control"
                       accept=".xlsx,.xls"
                       required>

              </div>

              <div class="text-muted small">

                Pastikan format Excel sesuai dengan template yang telah
                disediakan.

              </div>

            </div>


            <div class="modal-footer">

              <button type="button"
                      class="btn btn-secondary"
                      data-bs-dismiss="modal">

                Batal

              </button>


              <button type="submit"
                      class="btn btn-success">

                Import

              </button>

            </div>

          </form>

        </div>

      </div>

    </div>



    {{-- ==========================================================
    MODAL EDIT SISWA
    =========================================================== --}}
    <div class="modal modal-blur fade"
         id="modal-editsiswa"
         tabindex="-1"
         role="dialog"
         aria-hidden="true">

      <div class="modal-dialog modal-dialog-centered"
           role="document">

        <div class="modal-content">


          <div class="modal-header">

            <h5 class="modal-title">
              Edit Data Siswa
            </h5>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">

            </button>

          </div>


          <div class="modal-body"
               id="loadeditform">

          </div>


        </div>

      </div>

    </div>

@endsection



{{-- ==========================================================
JAVASCRIPT
=========================================================== --}}
@push('myscript')

  <script>

  $(function () {


      /*
      |--------------------------------------------------------------------------
      | KELAS HALAMAN SEKARANG
      |--------------------------------------------------------------------------
      */

      var kelasSekarang = "{{ $kelas }}";


      /*
      |--------------------------------------------------------------------------
      | MAPPING KELAS -> JURUSAN
      |--------------------------------------------------------------------------
      |
      | SESUAI DENGAN KELAS DI SIDEBAR
      |
      |--------------------------------------------------------------------------
      */

      var mappingJurusan = {


          /*
          |--------------------------------------------------------------------------
          | KELAS X
          |--------------------------------------------------------------------------
          */

          "X TJKT 1": {

              kode: "TJKT",

              nama: "Teknik Jaringan Komputer dan Telekomunikasi"

          },


          "X TJKT 2": {

              kode: "TJKT",

              nama: "Teknik Jaringan Komputer dan Telekomunikasi"

          },


          "X TM": {

              kode: "TM",

              nama: "Teknik Mesin"

          },


          /*
          |--------------------------------------------------------------------------
          | KELAS XI
          |--------------------------------------------------------------------------
          */

          "XI TJKT": {

              kode: "TJKT",

              nama: "Teknik Jaringan Komputer dan Telekomunikasi"

          },


          "XI TM": {

              kode: "TM",

              nama: "Teknik Mesin"

          },


          /*
          |--------------------------------------------------------------------------
          | KELAS XII
          |--------------------------------------------------------------------------
          */

          "XII TJKT": {

              kode: "TJKT",

              nama: "Teknik Jaringan Komputer dan Telekomunikasi"

          },


          "XII TM": {

              kode: "TM",

              nama: "Teknik Mesin"

          }

      };


      /*
      |--------------------------------------------------------------------------
      | NORMALISASI KELAS
      |--------------------------------------------------------------------------
      */

      var kelasNormal = kelasSekarang

          .trim()

          .replace(/\s+/g, " ")

          .toUpperCase();



      /*
      |--------------------------------------------------------------------------
      | SET JURUSAN OTOMATIS
      |--------------------------------------------------------------------------
      */

      function setJurusan() {


          var dataJurusan = mappingJurusan[kelasNormal];


          /*
          |--------------------------------------------------------------------------
          | JIKA KELAS DITEMUKAN
          |--------------------------------------------------------------------------
          */

          if (dataJurusan) {


              /*
              | KODE JURUSAN
              */

              $("#kode_jurusan_modal")

                  .val(dataJurusan.kode);


              /*
              | NAMA JURUSAN
              */

              $("#nama_jurusan_modal")

                  .val(dataJurusan.nama);


              console.log(

                  "Kelas:",

                  kelasNormal,

                  "| Kode Jurusan:",

                  dataJurusan.kode,

                  "| Jurusan:",

                  dataJurusan.nama

              );

          }


          /*
          |--------------------------------------------------------------------------
          | JIKA KELAS TIDAK DITEMUKAN
          |--------------------------------------------------------------------------
          */

          else {


              $("#kode_jurusan_modal")

                  .val("");


              $("#nama_jurusan_modal")

                  .val(
                      "Jurusan belum dikonfigurasi"
                  );


              console.warn(

                  "Jurusan belum dikonfigurasi untuk kelas:",

                  kelasNormal

              );

          }

      }



      /*
      |--------------------------------------------------------------------------
      | JALANKAN SAAT HALAMAN LOAD
      |--------------------------------------------------------------------------
      */

      setJurusan();



      /*
      |--------------------------------------------------------------------------
      | JALANKAN SAAT MODAL DIBUKA
      |--------------------------------------------------------------------------
      */

      $("#modal-inputsiswa").on(

          "shown.bs.modal",

          function () {

              setJurusan();

          }

      );



      /*
      |--------------------------------------------------------------------------
      | MODAL TAMBAH SISWA
      |--------------------------------------------------------------------------
      */

      $("#btnTambahsiswa").click(

          function (e) {


              e.preventDefault();


              /*
              | Pastikan jurusan diisi
              */

              setJurusan();


              /*
              | Reset input
              */

              $("#nis").val("");

              $("#nama_lengkap_modal").val("");

              $("#no_hp_modal").val("");


              /*
              | Buka modal
              */

              $("#modal-inputsiswa").modal("show");

          }

      );



      /*
      |--------------------------------------------------------------------------
      | MODAL IMPORT
      |--------------------------------------------------------------------------
      */

      $("#btnImport").click(

          function (e) {


              e.preventDefault();


              $("#modal-import").modal("show");

          }

      );



      /*
      |--------------------------------------------------------------------------
      | FORMAT NOMOR HP
      |--------------------------------------------------------------------------
      */

      $("#no_hp_modal").on(

          "input",

          function () {


              var nomor = $(this).val();


              /*
              |--------------------------------------------------------------------------
              | HANYA ANGKA
              |--------------------------------------------------------------------------
              */

              nomor = nomor.replace(/\D/g, "");


              /*
              |--------------------------------------------------------------------------
              | JIKA 620...
              |--------------------------------------------------------------------------
              */

              if (nomor.startsWith("620")) {


                  nomor =

                      "62" +

                      nomor.substring(3);

              }


              /*
              |--------------------------------------------------------------------------
              | JIKA 0...
              |--------------------------------------------------------------------------
              */

              else if (nomor.startsWith("0")) {


                  nomor =

                      "62" +

                      nomor.substring(1);

              }


              /*
              |--------------------------------------------------------------------------
              | JIKA BELUM 62
              |--------------------------------------------------------------------------
              */

              else if (

                  nomor !== "" &&

                  !nomor.startsWith("62")

              ) {


                  nomor =

                      "62" +

                      nomor;

              }


              /*
              |--------------------------------------------------------------------------
              | TAMBAHKAN +
              |--------------------------------------------------------------------------
              */

              if (nomor !== "") {


                  $(this).val(

                      "+" + nomor

                  );

              }

          }

      );



      /*
      |--------------------------------------------------------------------------
      | HANYA IZINKAN ANGKA
      |--------------------------------------------------------------------------
      */

      $("#no_hp_modal").keypress(

          function (e) {


              var char =

                  String.fromCharCode(e.which);


              if (!/[0-9]/.test(char)) {


                  return false;

              }

          }

      );



      /*
      |--------------------------------------------------------------------------
      | EDIT SISWA
      |--------------------------------------------------------------------------
      */

      $(".edit").click(

          function (e) {


              e.preventDefault();


              var nis =

                  $(this).attr("nis");


              var redirect_kelas =

                  $(this).attr(
                      "redirect_kelas"
                  );


              /*
              |--------------------------------------------------------------------------
              | AJAX
              |--------------------------------------------------------------------------
              */

              $.ajax({


                  type: "POST",


                  url: "/siswa/edit",


                  cache: false,


                  data: {


                      _token:

                          "{{ csrf_token() }}",


                      nis: nis,


                      redirect_kelas:

                          redirect_kelas

                  },


                  /*
                  |--------------------------------------------------------------------------
                  | SUCCESS
                  |--------------------------------------------------------------------------
                  */

                  success: function (response) {


                      $("#loadeditform")

                          .html(response);

                  },


                  /*
                  |--------------------------------------------------------------------------
                  | ERROR
                  |--------------------------------------------------------------------------
                  */

                  error: function () {


                      Swal.fire({


                          title: "Error!",


                          text:

                              "Gagal mengambil data siswa.",


                          icon: "error",


                          confirmButtonText: "OK"

                      });

                  }

              });


              /*
              |--------------------------------------------------------------------------
              | BUKA MODAL EDIT
              |--------------------------------------------------------------------------
              */

              $("#modal-editsiswa")

                  .modal("show");

          }

      );



      /*
      |--------------------------------------------------------------------------
      | DELETE SISWA
      |--------------------------------------------------------------------------
      */

      $(".delete-confirm").click(

          function (e) {


              e.preventDefault();


              var form =

                  $(this).closest("form");


              Swal.fire({


                  title:

                      "Apakah Anda yakin?",


                  text:

                      "Anda tidak akan dapat mengembalikan data ini!",


                  icon: "warning",


                  showCancelButton: true,


                  confirmButtonColor:

                      "#3085d6",


                  cancelButtonColor:

                      "#d33",


                  confirmButtonText:

                      "Ya, hapus!",


                  cancelButtonText:

                      "Batal"


              }).then(

                  (result) => {


                      if (result.isConfirmed) {


                          form.submit();

                      }

                  }

              );

          }

      );



      /*
      |--------------------------------------------------------------------------
      | VALIDASI FORM TAMBAH SISWA
      |--------------------------------------------------------------------------
      */

      $("#formsiswa").submit(

          function (e) {


              /*
              |--------------------------------------------------------------------------
              | AMBIL DATA
              |--------------------------------------------------------------------------
              */

              var nis =

                  $("#nis")

                      .val()

                      .trim();


              var nama_lengkap =

                  $("#nama_lengkap_modal")

                      .val()

                      .trim();


              var no_hp =

                  $("#no_hp_modal")

                      .val()

                      .trim();


              var kode_jurusan =

                  $("#kode_jurusan_modal")

                      .val();



              /*
              |--------------------------------------------------------------------------
              | VALIDASI NIS
              |--------------------------------------------------------------------------
              */

              if (nis == "") {


                  e.preventDefault();


                  Swal.fire({


                      title: "Warning!",


                      text:

                          "NIS Harus Diisi",


                      icon: "warning",


                      confirmButtonText: "OK"


                  }).then(

                      () => {


                          $("#nis").focus();

                      }

                  );


                  return false;

              }



              /*
              |--------------------------------------------------------------------------
              | VALIDASI NAMA
              |--------------------------------------------------------------------------
              */

              if (nama_lengkap == "") {


                  e.preventDefault();


                  Swal.fire({


                      title: "Warning!",


                      text:

                          "Nama Lengkap Harus Diisi",


                      icon: "warning",


                      confirmButtonText: "OK"


                  }).then(

                      () => {


                          $("#nama_lengkap_modal")

                              .focus();

                      }

                  );


                  return false;

              }



              /*
              |--------------------------------------------------------------------------
              | VALIDASI NO HP
              |--------------------------------------------------------------------------
              */

              if (no_hp == "") {


                  e.preventDefault();


                  Swal.fire({


                      title: "Warning!",


                      text:

                          "No HP Harus Diisi",


                      icon: "warning",


                      confirmButtonText: "OK"


                  }).then(

                      () => {


                          $("#no_hp_modal")

                              .focus();

                      }

                  );


                  return false;

              }



              /*
              |--------------------------------------------------------------------------
              | VALIDASI JURUSAN
              |--------------------------------------------------------------------------
              */

              if (kode_jurusan == "") {


                  e.preventDefault();


                  Swal.fire({


                      title: "Warning!",


                      text:

                          "Jurusan untuk kelas " +

                          kelasSekarang +

                          " belum terkonfigurasi.",


                      icon: "warning",


                      confirmButtonText: "OK"

                  });


                  return false;

              }



              /*
              |--------------------------------------------------------------------------
              | FORMAT HP
              |--------------------------------------------------------------------------
              */

              var hp =

                  no_hp.replace(/\+/g, "");



              /*
              |--------------------------------------------------------------------------
              | HARUS 62
              |--------------------------------------------------------------------------
              */

              if (!hp.startsWith("62")) {


                  e.preventDefault();


                  Swal.fire({


                      title: "Warning!",


                      text:

                          "Nomor HP harus menggunakan format +62.",


                      icon: "warning",


                      confirmButtonText: "OK"

                  }).then(

                      () => {


                          $("#no_hp_modal")

                              .focus();

                      }

                  );


                  return false;

              }



              /*
              |--------------------------------------------------------------------------
              | HILANGKAN 62
              |--------------------------------------------------------------------------
              */

              var nomor =

                  hp.substring(2);



              /*
              |--------------------------------------------------------------------------
              | PANJANG NOMOR
              |--------------------------------------------------------------------------
              */

              if (

                  nomor.length < 9 ||

                  nomor.length > 15

              ) {


                  e.preventDefault();


                  Swal.fire({


                      title: "Warning!",


                      text:

                          "Nomor HP harus terdiri dari 9 sampai 15 digit.",


                      icon: "warning",


                      confirmButtonText: "OK"

                  }).then(

                      () => {


                          $("#no_hp_modal")

                              .focus();

                      }

                  );


                  return false;

              }



              /*
              |--------------------------------------------------------------------------
              | SEMUA VALID
              |--------------------------------------------------------------------------
              */

              return true;

          }

      );

  });

  </script>

@endpush