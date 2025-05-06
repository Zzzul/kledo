<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    protected $table = 'approvals';

    protected $guarded = ['id'];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(related: Approver::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(related: Status::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(related: Expense::class);
    }
}
