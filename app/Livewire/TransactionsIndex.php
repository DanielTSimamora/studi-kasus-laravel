<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionsIndex extends Component
{
    use WithPagination;

    public string $q = '';
    public string $type = '';            // '', 'income', 'expense' (UI bisa kirim 'Pemasukan'/'Pengeluaran')
    public ?string $dateFrom = null;     // 'YYYY-MM-DD'
    public ?string $dateTo = null;       // 'YYYY-MM-DD'

    // Simpan state filter di URL
    protected $queryString = [
        'q'        => ['except' => ''],
        'type'     => ['except' => ''],
        'dateFrom' => ['except' => null],
        'dateTo'   => ['except' => null],
        'page'     => ['except' => 1],
    ];

    public function updated($name, $value): void
    {
        if (in_array($name, ['q','type','dateFrom','dateTo'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->q = '';
        $this->type = '';
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->resetPage();
    }

    #[On('confirmDelete')]
    public function delete(int $id): void
    {
        $trx = Transaction::where('user_id', Auth::id())->findOrFail($id);
        if ($trx->cover_path) @unlink(storage_path('app/public/'.$trx->cover_path));
        $trx->delete();
        session()->flash('ok', 'Data berhasil dihapus');
    }

    public function getChartDataProperty(): array
    {
        $driver = config('database.default');
        $groupExpr = $driver==='pgsql'
            ? "to_char(date,'YYYY-MM')"
            : ($driver==='sqlite' ? "strftime('%Y-%m',date)" : "DATE_FORMAT(date,'%Y-%m')");

        $rows = Transaction::selectRaw("$groupExpr ym,
                SUM(CASE WHEN type='income' THEN amount ELSE 0 END) income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) expense")
            ->where('user_id', Auth::id())
            ->when($this->dateFrom, fn($q)=>$q->whereDate('date','>=',$this->dateFrom))
            ->when($this->dateTo,   fn($q)=>$q->whereDate('date','<=',$this->dateTo))
            ->groupBy('ym')->orderBy('ym')->get();

        return [
            'labels'  => $rows->pluck('ym'),
            'income'  => $rows->pluck('income')->map(fn($v)=>(float)$v),
            'expense' => $rows->pluck('expense')->map(fn($v)=>(float)$v),
        ];
    }

    public function render()
    {
        // 1) Normalisasi nilai tipe dari UI -> DB
        $map = [
            'Pemasukan'   => 'income',
            'Pengeluaran' => 'expense',
            'income'      => 'income',
            'expense'     => 'expense',
        ];
        $normalizedType = $map[$this->type] ?? '';

        // 2) Search tahan banting (LOWER + COALESCE)
        $term = trim($this->q);
        $items = Transaction::where('user_id', Auth::id())
            ->when($term !== '', function ($q) use ($term) {
                $like = '%'.mb_strtolower($term).'%';
                $q->where(function ($s) use ($like) {
                    // pakai raw biar cross-DB dan aman NULL
                    $s->whereRaw('LOWER(title) LIKE ?', [$like])
                      ->orWhereRaw('LOWER(COALESCE(description, \'\')) LIKE ?', [$like]);
                });
            })
            ->when(in_array($normalizedType, ['income','expense']), fn($q)=>$q->where('type', $normalizedType))
            ->when($this->dateFrom, fn($q)=>$q->whereDate('date','>=',$this->dateFrom))
            ->when($this->dateTo,   fn($q)=>$q->whereDate('date','<=',$this->dateTo))
            ->orderByDesc('date')
            ->paginate(20);

        $summary = [
            'income'  => (float) Transaction::where('user_id',Auth::id())->where('type','income')->sum('amount'),
            'expense' => (float) Transaction::where('user_id',Auth::id())->where('type','expense')->sum('amount'),
        ];

        return view('livewire.transactions-index', [
            'items'   => $items,
            'summary' => $summary,
            'chart'   => $this->chartData,
        ])->layout('components.layouts.app');
    }
}
