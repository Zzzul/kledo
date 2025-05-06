<?php

namespace App\Repositories;

use App\Models\Approver;
use Illuminate\Support\Facades\DB;
use App\Interfaces\ApproverRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ApproverRepository implements ApproverRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return Approver::paginate();
    }

    public function getById(string|int $id): Builder|Approver
    {
        return Approver::find(id: $id);
    }

    public function create(array $data): Approver
    {
        return DB::transaction(callback: function () use ($data): Approver {
            $approver = Approver::create(attributes: $data);

            return $approver;
        });
    }

    public function update(string|int $id, array $data): Builder|Approver
    {
        return DB::transaction(callback: function () use ($id, $data): Approver|Builder {
            $approver = $this->getById(id: $id);
            $approver->update($data);

            return $approver;
        });
    }

    public function delete(string|int $id): bool
    {
        return DB::transaction(callback: function () use ($id): bool {
            $approver = $this->getById(id: $id);
            $approver->delete();

            return true;
        });
    }
}
