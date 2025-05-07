<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $guarded = ['id'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(related: Status::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(related: Approval::class);
    }
}
