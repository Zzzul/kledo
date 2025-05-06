<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $guarded = ['id'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(related: Status::class);
    }
}
