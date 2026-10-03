<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Outbox untuk realtime sync ke Prism Bill.
     * Setiap perubahan Package/Customer/Payment dicatat di sini
     * dalam transaction yang sama dengan perubahan datanya —
     * jadi tidak ada event yang pernah hilang. Queue worker
     * membaca tabel ini dan mengirim ke API Prism
     * (POST /api/sync/events).
     *
     * event_id = UUID identitas row. event_id yang dikirim
     * ke Prism berbeda: stabil per entity ("<entity>:<id>")
     * — dihitung di SyncToPrismJob, kunci idempotency
     * ledger sync_events di sisi Prism.
     */
    public function up(): void
    {
        Schema::create('sync_outbox', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 20);       // package | customer | payment
            $table->string('event', 20);        // created | updated | deleted
            $table->unsignedBigInteger('old_id');
            $table->string('event_id', 64)->unique();
            $table->json('payload');
            $table->boolean('processed')->default(false);
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['processed', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_outbox');
    }
};
