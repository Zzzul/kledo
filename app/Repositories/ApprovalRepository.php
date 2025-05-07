<?php

namespace App\Repositories;

use App\Interfaces\Approvals\ApprovalRepositoryInterface;
use App\Models\Approval;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ApprovalRepository implements ApprovalRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return Approval::with(relations: $this->relations())->paginate();
    }

    public function getById(string|int $id): Builder|Approval
    {
        return Approval::with(relations: $this->relations())->find(id: $id);
    }

    public function create(array $data): Approval
    {
        return DB::transaction(callback: function () use ($data): Approval {
            $approval = Approval::create(attributes: [
                'approver_id' => $data['approver_id'],
                'status_id' => $data['status_id'],
                'expense_id' => $data['expense_id'],
            ]);

            return $approval;
        });
    }

    public function update(string|int $id, array $data): Builder|Approval
    {
        return DB::transaction(callback: function () use ($id, $data): Approval|Builder {
            $approval = $this->getById(id: $id);
            $approval->update([
                'approver_id' => $data['approver_id'],
                'status_id' => $data['status_id'],
                'expense_id' => $data['expense_id'],
            ]);

            return $approval;
        });
    }

    public function delete(string|int $id): bool
    {
        return DB::transaction(callback: function () use ($id): bool {
            $approval = $this->getById(id: $id);
            $approval->delete();

            return true;
        });
    }

    public function relations(): array
    {
        return ['approver:id,name', 'expense:id,amount', 'status:id,name'];
    }
}
