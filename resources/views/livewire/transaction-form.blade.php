<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>{{ $trx ? 'Edit Catatan' : 'Tambah Catatan' }}</strong>
        <a href="{{ route('app.transactions.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
      </div>

      <div class="card-body">
        <form wire:submit.prevent="save" enctype="multipart/form-data" class="row g-3">
          <div class="col-12">
            <label class="form-label">Judul</label>
            <input type="text" wire:model="title" class="form-control" placeholder="mis. Gaji, Makan Siang, Listrik">
            @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4">
            <label class="form-label">Tipe</label>
            <select wire:model="type" class="form-select">
              <option value="income">Pemasukan</option>
              <option value="expense">Pengeluaran</option>
            </select>
            @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4">
            <label class="form-label">Jumlah (Rp)</label>
            <input type="number" step="0.01" wire:model="amount" class="form-control">
            @error('amount') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4">
            <label class="form-label">Tanggal</label>
            <input type="date" wire:model="date" class="form-control">
            @error('date') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Gambar (opsional)</label>
            <input type="file" wire:model="cover" accept="image/*" class="form-control">
            @error('cover') <div class="text-danger small">{{ $message }}</div> @enderror
            <div class="mt-2">
              @if($cover)
                <img src="{{ $cover->temporaryUrl() }}" class="rounded" style="height:90px;object-fit:cover;">
              @elseif($trx?->cover_url)
                <img src="{{ $trx->cover_url }}" class="rounded" style="height:90px;object-fit:cover;">
              @endif
            </div>
          </div>

          {{-- Deskripsi pakai TRIX --}}
          <div class="col-md-6" wire:ignore>
            <label class="form-label">Keterangan</label>
            <input id="desc-input" type="hidden" value="{{ $description ?? '' }}">
            <trix-editor input="desc-input" class="form-control"></trix-editor>
            @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('app.transactions.index') }}" class="btn btn-outline-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // sinkron TRIX <-> Livewire
  document.addEventListener('livewire:initialized', () => {
    const hidden = document.getElementById('desc-input');
    const editor = document.querySelector('trix-editor[input="desc-input"]');
    if (!hidden || !editor) return;
    editor.addEventListener('trix-change', () => { $wire.$set('description', hidden.value) });
    $wire.on('refresh-trix', (val) => {
      hidden.value = val || '';
      editor.editor.loadHTML(val || '');
    });
  });
</script>
