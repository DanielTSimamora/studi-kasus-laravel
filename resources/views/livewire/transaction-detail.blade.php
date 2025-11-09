<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Detail Transaksi</strong>
        <div class="d-flex gap-2">
          <a href="{{ route('app.transactions.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
          <a href="{{ route('app.transactions.edit',$trx->id) }}" class="btn btn-sm btn-primary">Edit</a>
        </div>
      </div>

      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-4">Tanggal</dt>
          <dd class="col-sm-8">
            {{ optional($trx->date)->format('d/m/Y') ?? \Illuminate\Support\Carbon::parse($trx->date)->format('d/m/Y') }}
          </dd>

          <dt class="col-sm-4">Judul</dt>
          <dd class="col-sm-8">{{ $trx->title }}</dd>

          <dt class="col-sm-4">Tipe</dt>
          <dd class="col-sm-8">
            <span class="badge {{ $trx->type=='income'?'bg-success':'bg-danger' }}">
              {{ $trx->type=='income'?'Pemasukan':'Pengeluaran' }}
            </span>
          </dd>

          <dt class="col-sm-4">Jumlah</dt>
          <dd class="col-sm-8">Rp {{ number_format($trx->amount,0,',','.') }}</dd>

          <dt class="col-sm-4">Deskripsi</dt>
          <dd class="col-sm-8">{!! $trx->description ?: '<span class="text-muted">-</span>' !!}</dd>

          <dt class="col-sm-4">Gambar</dt>
          <dd class="col-sm-8">
            @php([$exists, $url] = $trx->coverFile())
            <dt class="col-sm-4">Gambar</dt>
            <dd class="col-sm-8">
              @if($exists && $url)
                <img src="{{ $url }}" alt="cover" class="rounded border"
                    style="max-height:220px;object-fit:cover;"
                    onerror="this.replaceWith(document.createTextNode('-'));">
              @else
                <span class="text-muted">-</span>
              @endif
            </dd>
        </dl>
      </div>
    </div>
  </div>
</div>
