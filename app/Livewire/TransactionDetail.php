<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransactionDetail extends Component
{
    public Transaction $trx;

    public function mount(int $id): void
    {
        $this->trx = Transaction::where('user_id', Auth::id())->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.transaction-detail')
            ->layout('components.layouts.app')
            ->layoutData(['title' => 'Detail Transaksi']);
    }
}
