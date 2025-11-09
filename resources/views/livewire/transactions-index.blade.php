<div>
  {{-- ALERT BERHASIL --}}
  @if (session('ok'))
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({ title:'Sukses', text:@js(session('ok')), icon:'success', timer:1500, showConfirmButton:false })
      });
    </script>
  @endif

  {{-- FILTER BAR --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label class="form-label">Cari Judul / Keterangan</label>
          <input type="text" wire:model.live.debounce.300ms="q" class="form-control" placeholder="mis. gaji, kopi, listrik...">
        </div>

        <div class="col-6 col-md-2">
          <label class="form-label">Tipe</label>
          <select wire:model.live="type" class="form-select">
            <option value="">Semua</option>
            <option value="income">Pemasukan</option>
            <option value="expense">Pengeluaran</option>
          </select>
        </div>

        <div class="col-6 col-md-2">
          <label class="form-label">Dari</label>
          <input type="date" wire:model.live="dateFrom" class="form-control">
        </div>

        <div class="col-6 col-md-2">
          <label class="form-label">Sampai</label>
          <input type="date" wire:model.live="dateTo" class="form-control">
        </div>

        <div class="col-6 col-md-3 text-md-end">
          <label class="form-label d-none d-md-block">&nbsp;</label>
          <div class="d-grid d-md-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" wire:click="resetFilters">Reset</button>
            <a href="{{ route('app.transactions.create') }}" class="btn btn-primary">+ Tambah Catatan</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- SUMMARY --}}
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card shadow-sm text-bg-light">
        <div class="card-body">
          <div>Total Pemasukan</div>
          <h5 class="mt-2 number">Rp {{ number_format($summary['income'],0,',','.') }}</h5>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm text-bg-light">
        <div class="card-body">
          <div>Total Pengeluaran</div>
          <h5 class="mt-2 number">Rp {{ number_format($summary['expense'],0,',','.') }}</h5>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm text-bg-light">
        <div class="card-body">
          <div>Saldo Akhir</div>
          <h5 class="mt-2 number">
            Rp {{ number_format($summary['income'] - $summary['expense'],0,',','.') }}
          </h5>
        </div>
      </div>
    </div>
  </div>

  {{-- CHART --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h6 class="text-muted mb-3">Statistik Bulanan</h6>
      <div id="chart"></div>
    </div>
  </div>

  <script>
    document.addEventListener('livewire:navigated', drawChart);
    document.addEventListener('DOMContentLoaded', drawChart);
    function drawChart(){
      const el = document.getElementById('chart');
      if(!el) return;
      el.innerHTML = '';
      const chart = new ApexCharts(el, {
        chart:{ type:'line', height:300 },
        series:[
          { name:'Pemasukan',  data:@json($chart['income']) },
          { name:'Pengeluaran', data:@json($chart['expense']) }
        ],
        xaxis:{ categories:@json($chart['labels']) },
        colors:['#198754','#dc3545']
      });
      chart.render();
    }
  </script>

  {{-- TABEL --}}
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>Judul</th>
              <th>Tipe</th>
              <th class="text-end">Jumlah</th>
              <th>Gambar</th>
              <th style="width:160px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $trx)
              <tr>
                <td>{{ optional($trx->date)->format('d/m/Y') ?? \Illuminate\Support\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                <td>{{ $trx->title }}</td>
                <td>
                  <span class="badge {{ $trx->type=='income'?'bg-success':'bg-danger' }}">
                    {{ $trx->type=='income'?'Pemasukan':'Pengeluaran' }}
                  </span>
                </td>
                <td class="text-end number">Rp {{ number_format($trx->amount,0,',','.') }}</td>

                {{-- ====== KOLOM GAMBAR (PAKAI HELPER DARI MODEL) ====== --}}
               <td class="text-center">
                @php([$exists, $url] = $trx->coverFile())
                @if($exists && $url)
                  <img src="{{ $url }}" alt="cover" class="rounded"
                      style="width:40px;height:40px;object-fit:cover;"
                      onerror="this.replaceWith(document.createTextNode('-'));">
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('app.transactions.show',$trx->id) }}" class="btn btn-outline-primary">Detail</a>
                    <a href="{{ route('app.transactions.edit',$trx->id) }}" class="btn btn-outline-secondary">Edit</a>
                    <button class="btn btn-outline-danger"
                      onclick="Swal.fire({title:'Hapus data ini?',icon:'warning',showCancelButton:true,confirmButtonText:'Hapus'})
                        .then(r=>{ if(r.isConfirmed) Livewire.dispatch('confirmDelete',{{ $trx->id }}) })">
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($items->hasPages())
      <div class="card-footer">
        {{ $items->onEachSide(1)->links() }}
      </div>
    @endif
  </div>
</div>
