<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\SyncOutbox;
use Illuminate\Http\JsonResponse;

/**
 * Endpoint stats untuk reconcile backstop di Prism.
 * Prism membandingkan count ini dengan count per tabel di
 * sisi Prism — drift berarti ada event yang terlewat.
 */
class MigrationStatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'packages' => [
                'count' => Package::count(),
                'max_updated_at' => Package::max('updated_at'),
            ],
            'customers' => [
                'count' => Customer::count(),
                'max_updated_at' => Customer::max('updated_at'),
            ],
            'payments' => [
                'count' => Payment::count(),
                'max_updated_at' => Payment::max('updated_at'),
            ],
            'outbox_pending' => SyncOutbox::where('processed', false)->count(),
        ]);
    }
}
