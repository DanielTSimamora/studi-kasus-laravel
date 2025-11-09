<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class TransactionForm extends Component
{
    use WithFileUploads;

    public ?Transaction $trx = null;

    #[Rule('required|string|max:255')]
    public string $title = '';

    #[Rule('required|in:income,expense')]
    public string $type = 'income';

    #[Rule('required|numeric|min:0')]
    public $amount = 0;

    #[Rule('required|date')]
    public string $date;

    #[Rule('nullable|string')]
    public ?string $description = null;

    // file upload (opsional)
    #[Rule('nullable|image|max:4096')]
    public $cover;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->trx = Transaction::where('user_id', Auth::id())->findOrFail($id);
            $this->fill($this->trx->only('title','type','amount','date','description'));
            $this->dispatch('refresh-trix', $this->description);
        } else {
            $this->date = now()->toDateString();
        }
    }

    public function save()
    {
        $data = $this->validate();
        $data['user_id'] = Auth::id();

        if (!empty($this->cover)) {
            // Pastikan ada ekstensi
            $ext  = $this->cover->getClientOriginalExtension() ?: 'jpg';
            $name = uniqid('img_', true).'.'.$ext;

            // Simpan ke storage/app/public/covers/...
            $path = $this->cover->storeAs('covers', $name, 'public'); // hasil: covers/img_....jpg
            $data['cover_path'] = $path;

            // Hapus file lama kalau update
            if ($this->trx?->cover_path) {
                @unlink(storage_path('app/public/'.$this->trx->cover_path));
            }
        }

        if ($this->trx) {
            $this->trx->update($data);
        } else {
            $this->trx = Transaction::create($data);
        }

        session()->flash('ok', 'Data berhasil disimpan');
        return redirect()->route('app.transactions.index');
    }

    public function render()
    {
        return view('livewire.transaction-form')
            ->layout('components.layouts.app');
    }
}
