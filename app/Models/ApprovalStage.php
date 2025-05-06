<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalStage extends Model
{
    protected $table = 'approval_stages';

    protected $guarded = ['id'];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(related: Approver::class);
    }
}
