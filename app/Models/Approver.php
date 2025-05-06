<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Approver extends Model
{
    protected $table = 'approvers';

    protected $guarded = ['id'];

    public function approval_stages(): HasMany
    {
        return $this->hasMany(related: ApprovalStage::class);
    }

    public function approval(): HasMany
    {
        return $this->hasMany(related: Approval::class);
    }
}
