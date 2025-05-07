<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Interfaces\Approvals\ApprovalRepositoryInterface;
use App\Interfaces\ApprovalStages\ApprovalStageRepositoryInterface;
use App\Interfaces\Expenses\ExpenseRepositoryInterface;
use App\Interfaces\Expenses\ExpenseServiceInterface;
use App\Interfaces\StatusRepositoryInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ExpenseService implements ExpenseServiceInterface
{
    public function __construct(
        public ExpenseRepositoryInterface $expenseRepository,
        public StatusRepositoryInterface $statusRepository,
        public ApprovalRepositoryInterface $approvalRepository,
        public ApprovalStageRepositoryInterface $approvalStageRepository
    ) {
        //
    }

    public function findAll()
    {
        return $this->expenseRepository->getAll();
    }

    public function findById(string|int $id)
    {
        return $this->expenseRepository->getById($id);
    }

    public function save(array $data)
    {
        $data['status_id'] = $this->statusRepository->getByName(StatusEnum::MENUNGGU_PERSETUJUAN->value)?->id ?? 1;

        $expense = $this->expenseRepository->create($data);

        $this->createExpenseApprovalsByApprovalStages(expenseId: $expense->id, data: $data);

        return $expense;
    }

    public function update(string|int $id, array $data)
    {
        return $this->expenseRepository->update($id, $data);
    }

    public function delete(string|int $id)
    {
        return $this->expenseRepository->delete($id);
    }

    public function approve(string|int $expenseId, string|int $approverId)
    {
        $expense = $this->expenseRepository->getById(id: $expenseId);

        if (! $this->checkIfExpenseHasApprovals(id: $expenseId)) {
            $expenseApprovalsCreated = $this->createExpenseApprovalsByApprovalStages(expenseId: $expenseId, data: [
                'status_id' => $this->statusRepository->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id,
                'approver_id' => $approverId,
                'expense_id' => $expenseId,
            ]);

            if (! $expenseApprovalsCreated) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Approval stage not found.',
                ], Response::HTTP_NOT_FOUND));
            }

            $expense = $this->expenseRepository->getById(id: $expenseId);
        }

        $approvals = $expense->approvals->sortBy('approval_stage_id')->values();

        $currentApprovalIndex = $approvals->search(function ($approval) use ($approverId): bool {
            return $approval->approver_id == $approverId;
        });

        if ($currentApprovalIndex === false) {
            throw new HttpResponseException(response()->json([
                'message' => 'Approval not found for this approver.',
            ], Response::HTTP_NOT_FOUND));
        }

        $currentApproval = $approvals[$currentApprovalIndex];
        $approvedStatusId = $this->statusRepository->getByName(name: StatusEnum::DISETUJUI->value)->id;

        if ($currentApproval->status_id === $approvedStatusId) {
            throw new HttpResponseException(response()->json([
                'message' => 'This approval has already been approved.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        for ($i = 0; $i < $currentApprovalIndex; $i++) {
            if ($approvals[$i]->status_id !== $approvedStatusId) {
                throw new HttpResponseException(response()->json([
                    'message' => 'You cannot approve this expense before previous approvals are completed.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY));
            }
        }

        $this->expenseRepository->updateApprovalStatus(
            approverId: $approverId,
            statusId: $approvedStatusId
        );

        $expense = $this->expenseRepository->getById(id: $expenseId);
        $approvals = $expense->approvals;

        $isAllApproved = $approvals->every(function ($approval) use ($approvedStatusId) {
            return $approval->status_id === $approvedStatusId;
        });

        if ($isAllApproved) {
            $this->expenseRepository->update(id: $expenseId, data: [
                'status_id' => $approvedStatusId,
                'amount' => $expense->amount,
            ]);
        }

        return $this->expenseRepository->getById(id: $expenseId);
    }

    public function checkIfExpenseHasApprovals(string|int $id)
    {
        return $this->expenseRepository->getById($id)->approvals->count() > 0;
    }

    public function createExpenseApprovalsByApprovalStages(string|int $expenseId, array $data): bool
    {
        if ($this->approvalStageRepository->countAll() > 0) {
            $approvalStages = $this->approvalStageRepository->getAll();
            $data['status_id'] = $this->statusRepository->getByName(StatusEnum::MENUNGGU_PERSETUJUAN->value)?->id ?? 1;

            foreach ($approvalStages as $approvalStage) {
                $this->approvalRepository->create([
                    'approver_id' => $approvalStage->approver_id,
                    'status_id' => $data['status_id'],
                    'expense_id' => $expenseId,
                ]);
            }

            return true;
        }

        return false;
    }
}
