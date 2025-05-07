<?php

namespace App\Repositories;

use App\Enums\StatusEnum;
use App\Interfaces\Approvals\ApprovalRepositoryInterface;
use App\Interfaces\Expenses\ExpenseRepositoryInterface;
use App\Interfaces\StatusRepositoryInterface;
use App\Models\Approval;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function __construct(public ApprovalRepositoryInterface $approvalRepository, public StatusRepositoryInterface $statusRepository)
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
        return DB::transaction(callback: function () use ($data): Expense {
            $expense = Expense::create(attributes: [
                'amount' => $data['amount'],
                'status_id' => $this->statusRepository->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id,
            ]);

            return $expense;
        });
    }

    public function update(string|int $id, array $data): Builder|Expense
    {
        return DB::transaction(callback: function () use ($id, $data): Expense|Builder {
            $expense = $this->getById(id: $id);
            $approvedStatusId = $this->statusRepository->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id;

            if ($approvedStatusId != $expense->status_id) {
                $data['status_id'] = $this->statusRepository->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id;
            } else {
                $data['status_id'] = $this->statusRepository->getByName(name: StatusEnum::DISETUJUI->value)->id;
            }

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
            $expense->approvals()->delete();
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
