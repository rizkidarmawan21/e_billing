<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar customer
     */
    public function index(Request $request)
    {
        $search = $request->search;

       $customers = Customer::with(['package', 'payments'])
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }


    /**
     * Menampilkan form tambah customer
     */
    public function create()
    {
        $packages = Package::orderBy('harga')->get();

        return view('customers.create', compact('packages'));
    }


    /**
     * Menyimpan customer baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pelanggan' => [
                'required',
                'string',
                'max:255',
                'unique:customers,kode_pelanggan',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

            'package_id' => [
                'nullable',
                'exists:packages,id',
            ],
        ]);

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data customer berhasil ditambahkan.');
    }


    /**
     * Menampilkan detail customer
     */
    public function show(Customer $customer)
{
    $customer->load(['package', 'payments']);

    return view('customers.show', compact('customer'));
}


    /**
     * Menampilkan form edit customer
     */
    public function edit(Customer $customer)
    {
        $packages = Package::orderBy('harga')->get();

        return view('customers.edit', compact('customer', 'packages'));
    }


    /**
     * Mengupdate data customer
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'kode_pelanggan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers', 'kode_pelanggan')
                    ->ignore($customer->id),
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

            'package_id' => [
                'nullable',
                'exists:packages,id',
            ],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data customer berhasil diperbarui.');
    }


    /**
     * Menghapus customer
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data customer berhasil dihapus.');
    }
}