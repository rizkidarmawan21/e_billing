<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnpaidBillsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'preset' => ['nullable', Rule::in(['all', 'current', 'month', 'range'])],
            'month' => ['nullable', 'required_if:preset,month', 'date_format:Y-m'],
            'start' => ['nullable', 'required_if:preset,range', 'date_format:Y-m'],
            'end' => ['nullable', 'required_if:preset,range', 'date_format:Y-m', 'after_or_equal:start'],
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthSql = 'CASE payments.periode '.collect($months)->map(fn ($name, $number) => "WHEN '$name' THEN $number")->implode(' ').' ELSE 0 END';
        $query = Payment::query()->join('customers', 'customers.id', '=', 'payments.customer_id')
            ->where('payments.status', 'belum_bayar')
            ->when(isset($filters['search']) && $filters['search'] !== '', fn ($q) => $q->where('customers.nama', 'like', '%'.$filters['search'].'%'));
        $preset = $filters['preset'] ?? 'all';
        if ($preset === 'current' || $preset === 'month') {
            $date = $preset === 'current' ? now()->format('Y-m') : $filters['month'];
            [$year, $month] = explode('-', $date);
            $query->where('payments.tahun', (int) $year)->where('payments.periode', $months[(int) $month]);
        } elseif ($preset === 'range') {
            [$startYear, $startMonth] = explode('-', $filters['start']);
            [$endYear, $endMonth] = explode('-', $filters['end']);
            $query->whereRaw("(payments.tahun * 100 + $monthSql) BETWEEN ? AND ?", [(int) $startYear * 100 + (int) $startMonth, (int) $endYear * 100 + (int) $endMonth]);
        }
        $count = (clone $query)->count();
        $total = (clone $query)->sum('payments.jumlah_tagihan');
        $payments = $query->select('payments.*')->with('customer')
            ->orderBy('payments.tahun')->orderByRaw($monthSql)->orderBy('customers.nama')->orderBy('payments.id')
            ->paginate(10)->withQueryString();

        return view('payments.unpaid', compact('payments', 'count', 'total', 'filters'));
    }
}
