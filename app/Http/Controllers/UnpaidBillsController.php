<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnpaidBillsController extends Controller
{
    public function index(Request $request)
    {
        $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $filters = $request->validate([
            'filter_active' => ['nullable', 'boolean'],
            'months' => ['nullable', 'array'],
            'months.*' => ['string', Rule::in(array_values($months))],
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $selectedMonths = array_values(array_unique($filters['months'] ?? []));
        $filterActive = (bool) ($filters['filter_active'] ?? false);
        $monthSql = 'CASE payments.periode '.collect($months)->map(fn ($name, $number) => "WHEN '$name' THEN $number")->implode(' ').' ELSE 0 END';
        $query = Payment::query()->join('customers', 'customers.id', '=', 'payments.customer_id')
            ->where('payments.status', 'belum_bayar')
            ->when($filterActive && $selectedMonths, fn ($q) => $q->where('payments.tahun', now()->year)->whereIn('payments.periode', $selectedMonths))
            ->when(isset($filters['search']) && $filters['search'] !== '', fn ($q) => $q->where('customers.nama', 'like', '%'.$filters['search'].'%'));
        $count = (clone $query)->count();
        $total = (clone $query)->sum('payments.jumlah_tagihan');
        $payments = $query->select('payments.*')->with('customer')
            ->orderBy('payments.tahun')->orderByRaw($monthSql)->orderBy('customers.nama')->orderBy('payments.id')
            ->paginate(10)->appends($filters);

        return view('payments.unpaid', compact('payments', 'count', 'total', 'filters', 'selectedMonths', 'filterActive', 'months'));
    }
}
