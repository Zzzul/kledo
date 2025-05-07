<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\Expenses\ExpenseRepositoryInterface;
use App\Interfaces\Approvals\ApprovalRepositoryInterface;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function __construct(public ApprovalRepositoryInterface $approvalRepository)
    {
        //
    }

    public function getAll(): LengthAwarePaginator
    {
        return Expense::with(relations: $this->relations())->paginate();
    }

    public function getById(string|int $id): Builder|Expense
    {
        return Expense::with(relations: $this->relations())->findOrFail(id: $id);
    }

    public function create(array $data): Expense
    {
        Log::debug('data', $data);

        return DB::transaction(callback: function () use ($data): Expense {
            $expense = Expense::create(attributes: [
                'amount' => $data['amount'],
                'status_id' => $data['status_id'],
            ]);

            return $expense;
        });
    }

    public function update(string|int $id, array $data): Builder|Expense
    {
        return DB::transaction(callback: function () use ($id, $data): Expense|Builder {
            $expense = $this->getById(id: $id);
            $expense->update([
                'amount' => $data['amount'],
                'status_id' => $data['status_id'],
            ]);

            return $expense;
        });
    }

    public function delete(string|int $id): bool
    {
        return DB::transaction(callback: function () use ($id): bool {
            $expense = $this->getById(id: $id);
            $expense->delete();

            return true;
        });
    }

    public function approve(string|int $id, array $data): Builder|Expense|Approval
    {
        return DB::transaction(callback: function () use ($id, $data): Expense|Builder|Approval {
            $approval = $this->approvalRepository->create([
                'approver_id' => $data['approver_id'],
                'status_id' => $data['status_id'],
                'expense_id' => $id,
            ]);

            return $approval;
        });
    }

    public function updateApprovalStatus(string|int $approverId, string|int $statusId): Approval|Builder
    {
        $approval = Approval::where('approver_id', $approverId)->firstOrFail();

        $approval->update([
            'status_id' => $statusId,
        ]);

        return $approval;
    }

    public function relations(): array
    {
        return [
            'status:id,name',
            'approvals:id,expense_id,approver_id,status_id',
            'approvals.approver:id,name',
            'approvals.status:id,name',
        ];
    }
}
