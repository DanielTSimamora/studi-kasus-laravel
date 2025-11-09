<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="/logo.png" type="image/x-icon"/>
  <title>{{ $title ?? 'Catatan Keuangan' }}</title>

  {{-- Styles --}}
  @livewireStyles
  <link rel="stylesheet" href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  {{-- Aktifkan Trix Editor --}}
  <link rel="stylesheet" href="https://unpkg.com/trix/dist/trix.css">

  <style>
    body { background:#f8f9fa }
    .number { font-variant-numeric: tabular-nums }
    trix-toolbar [data-trix-button-group="file-tools"] { display: none; } /* sembunyi tombol upload file */
  </style>
</head>
<body>
  {{-- NAVBAR --}}
  <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="{{ route('app.transactions.index') }}">Catatan Keuangan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('app.transactions.index') ? 'active' : '' }}"
               href="{{ route('app.transactions.index') }}">Transaksi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('app.transactions.create') ? 'active' : '' }}"
               href="{{ route('app.transactions.create') }}">Tambah</a>
          </li>
        </ul>
        <a href="{{ route('auth.logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  {{-- CONTENT --}}
  <main class="container py-4">
    {{ $slot }}
  </main>

  {{-- Scripts --}}
  <script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  {{-- Aktifkan Trix Editor --}}
  <script src="https://unpkg.com/trix/dist/trix.umd.min.js"></script>

  @livewireScripts
</body>
</html>
