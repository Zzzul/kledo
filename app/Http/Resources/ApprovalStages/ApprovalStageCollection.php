<?php

namespace App\Http\Resources\ApprovalStages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApprovalStageCollection extends ResourceCollection
{
    public static $wrap = 'approval_stages';

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray(request: $request);
    }
}
