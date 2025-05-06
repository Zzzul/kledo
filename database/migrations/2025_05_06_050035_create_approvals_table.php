<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(table: 'approvals', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'expense_id')->constrained(table: 'expenses');
            $table->foreignId(column: 'approver_id')->constrained(table: 'approvers');
            $table->foreignId(column: 'status_id')->constrained(table: 'statuses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'approvals');
    }
};
