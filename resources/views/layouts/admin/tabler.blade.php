<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta17
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Dashboard - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
  <!-- CSS files -->
  <link href="{{ asset('tabler/dist/css/tabler.min.css?1674944402')}}" rel="stylesheet" />
  <link href="{{ asset('tabler/dist/css/tabler-flags.min.css?1674944402')}}" rel="stylesheet" />
  <link href="{{ asset('tabler/dist/css/tabler-payments.min.css?1674944402')}}" rel="stylesheet" />
  <link href="{{ asset('tabler/dist/css/tabler-vendors.min.css?1674944402')}}" rel="stylesheet" />
  <link href="{{ asset('tabler/dist/css/demo.min.css?1674944402')}}" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <link rel="stylesheet"
  href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>
  <link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
      font-feature-settings: "cv03", "cv04", "cv11";
    }
  </style>
</head>

<body>
  <script src="{{ asset('tabler/dist/js/demo-theme.min.js?1674944402')}}"></script>
  <div class="page">

    {{-- Sidebar --}}
    @include('layouts.admin.sidebar')

    <div class="page-wrapper">

      {{-- Navbar --}}
      @include('layouts.admin.navbar')

      {{-- Content --}}
      @yield('content')

      {{-- Footer --}}
      @include('layouts.admin.footer')
    </div>
  </div>

  <!-- Libs JS -->

  <script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js?1674944402')}}" defer></script>
  <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1674944402')}}" defer></script>
  <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world.js?1674944402')}}" defer></script>
  <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world-merc.js?1674944402')}}" defer></script>
  <!-- Tabler Core -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="{{ asset('tabler/dist/js/tabler.min.js?1674944402')}}" defer></script>
  <script src="{{ asset('tabler/dist/js/demo.min.js?1674944402')}}" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
  <!-- Make sure you put this AFTER Leaflet's CSS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
  @stack('myscript')
  @include('layouts.admin.chatbot')
  <script src="{{ asset('assets/js/chatbot.js') }}"></script>
</body>

</html>