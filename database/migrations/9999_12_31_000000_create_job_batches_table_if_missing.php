<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ingestion is driven by Bus::batch(), which needs the framework's job_batches
 * table. Many applications configure batching in config/queue.php but never
 * publish the migration, so this creates it only when it is missing.
 *
 * Dated far in the future on purpose: it must run *after* every host migration,
 * so an application that publishes its own job_batches migration always wins and
 * this one no-ops. Run earlier, it would create the table first and make the
 * host's own migration fail with a duplicate-table error.
 *
 * This is the one place the package touches a table outside its own prefix,
 * which is why down() is deliberately a no-op: the table is not ours to drop.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_batches')) {
            return;
        }

        Schema::create('job_batches', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });
    }

    public function down(): void
    {
        // Intentionally empty -- see the class docblock.
    }
};
