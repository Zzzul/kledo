<?php

namespace App\Http\Resources\Approvers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApproverResource extends JsonResource
{
    public static $wrap = 'approver';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray(request: $request);
    }
}
