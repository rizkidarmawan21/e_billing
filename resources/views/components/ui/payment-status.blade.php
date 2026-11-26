@props(['status' => null])

@php($paid = $status === 'lunas')
<span @class(['badge payment-status', 'text-bg-success' => $paid, 'text-bg-danger' => !$paid])>{{ $paid ? 'Lunas' : 'Belum Bayar' }}</span>
