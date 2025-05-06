<?php

namespace App\Repositories;

use App\Interfaces\StatusRepositoryInterface;
use App\Models\Status;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StatusRepository implements StatusRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return Status::paginate();
    }

    public function getById(string|int $id): Builder|Status
    {
        return Status::find(id: $id);
    }

    public function create(array $data): Status
    {
        return DB::transaction(callback: function () use ($data): Status {
            $status = Status::create(attributes: $data);

            return $status;
        });
    }

    public function update(string|int $id, array $data): Builder|Status
    {
        return DB::transaction(callback: function () use ($id, $data): Status|Builder {
            $status = $this->getById(id: $id);
            $status->update($data);

            return $status;
        });
    }

    public function delete(string|int $id): bool
    {
        return DB::transaction(callback: function () use ($id): bool {
            $status = $this->getById($id);
            $status->delete();

            return true;
        });
    }
}
