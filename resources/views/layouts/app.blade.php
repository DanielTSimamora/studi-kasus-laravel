<!doctype html>
<html lang="id">
<head>
    {{-- Meta --}}
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Icon --}}
    <link rel="icon" href="/logo.png" type="image/x-icon" />

    {{-- Title --}}
    <title>{{ $title ?? 'Catatan Keuangan' }}</title>

    {{-- Styles --}}
    @livewireStyles
    <link rel="stylesheet" href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    {{-- (Opsional) Trix editor --}}
    {{-- <link rel="stylesheet" href="https://unpkg.com/trix/dist/trix.css"> --}}
</head>
<body class="bg-light">
    <div class="container-fluid py-3">
        {{ $slot }} {{-- konten komponen Livewire dirender di sini --}}
    </div>

    {{-- Scripts --}}
    <script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- (Opsional) Trix editor --}}
    {{-- <script src="https://unpkg.com/trix/dist/trix.umd.min.js"></script> --}}

    @livewireScripts
</body>
</html>
