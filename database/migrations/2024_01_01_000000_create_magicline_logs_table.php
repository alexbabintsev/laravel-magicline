<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('magicline.logging.database.table', 'magicline_logs');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('resource_type');
            $table->string('resource_id')->nullable();
            $table->string('action');
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->index(['resource_type', 'resource_id']);
            $table->index('synced_at');
        });
    }

    public function down(): void
    {
        $tableName = config('magicline.logging.database.table', 'magicline_logs');

        Schema::dropIfExists($tableName);
    }
};
