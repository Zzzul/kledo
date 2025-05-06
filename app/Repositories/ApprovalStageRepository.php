<?php

namespace App\Repositories;

use App\Models\ApprovalStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Interfaces\ApprovalStages\ApprovalStageRepositoryInterface;

class ApprovalStageRepository implements ApprovalStageRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return ApprovalStage::paginate();
    }

    public function getById(string|int $id): Builder|ApprovalStage
    {
        return ApprovalStage::find(id: $id);
    }

    public function create(array $data): ApprovalStage
    {
        return DB::transaction(callback: function () use ($data): ApprovalStage {
            $approvalStage = ApprovalStage::create(attributes: $data);

            return $approvalStage;
        });
    }

    public function update(string|int $id, array $data): Builder|ApprovalStage
    {
        return DB::transaction(callback: function () use ($id, $data): ApprovalStage|Builder {
            $approvalStage = $this->getById(id: $id);
            $approvalStage->update($data);

            return $approvalStage;
        });
    }

    public function delete(string|int $id): bool
    {
        return DB::transaction(callback: function () use ($id): bool {
            $approvalStage = $this->getById(id: $id);
            $approvalStage->delete();

            return true;
        });
    }
}
