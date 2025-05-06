<?php

namespace App\Http\Resources\Statuses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StatusCollection extends ResourceCollection
{
    public static $wrap = 'statuses';

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
