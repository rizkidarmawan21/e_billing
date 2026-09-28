<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Menampilkan semua paket.
     */
    public function index()
    {
        $packages = Package::orderBy('harga')->get();

        return view('packages.index', compact('packages'));
    }

    /**
     * Menampilkan form tambah paket.
     */
    public function create()
    {
        return view('packages.create');
    }

    /**
     * Menyimpan paket baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => [
                'required',
                'string',
                'max:255',
                'unique:packages,nama_paket',
            ],

            'kecepatan' => [
                'required',
                'string',
                'max:100',
            ],

            'harga' => [
                'required',
                'numeric',
                'min:0',
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],
        ]);

        Package::create($validated);

        return redirect()
            ->route('packages.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail paket.
     */
    public function show(Package $package)
    {
        return view('packages.show', compact('package'));
    }

    /**
     * Menampilkan form edit paket.
     */
    public function edit(Package $package)
    {
        return view('packages.edit', compact('package'));
    }

    /**
     * Mengupdate paket.
     */
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'nama_paket' => [
                'required',
                'string',
                'max:255',
                'unique:packages,nama_paket,' . $package->id,
            ],

            'kecepatan' => [
                'required',
                'string',
                'max:100',
            ],

            'harga' => [
                'required',
                'numeric',
                'min:0',
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],
        ]);

        $package->update($validated);

        return redirect()
            ->route('packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Menghapus paket.
     */
    public function destroy(Package $package)
    {
        if ($package->customers()->exists()) {
            return redirect()
                ->route('packages.index')
                ->with(
                    'error',
                    'Paket tidak dapat dihapus karena masih digunakan oleh customer.'
                );
        }

        if ($package->payments()->exists()) {
            return redirect()
                ->route('packages.index')
                ->with(
                    'error',
                    'Paket tidak dapat dihapus karena memiliki riwayat pembayaran.'
                );
        }

        $package->delete();

        return redirect()
            ->route('packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}