<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = 'statuses';

    protected $guarded = ['id'];

    public function expenses(): HasMany
    {
        return $this->hasMany(related: Expense::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(related: Approval::class);
    }
}
