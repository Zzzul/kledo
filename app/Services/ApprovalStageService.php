<?php

namespace App\Services;

use App\Interfaces\ApprovalStages\ApprovalStageRepositoryInterface;
use App\Interfaces\ApprovalStages\ApprovalStageServiceInterface;

class ApprovalStageService implements ApprovalStageServiceInterface
{
    public function __construct(public ApprovalStageRepositoryInterface $approvalStageRepository)
    {
        //
    }

    public function findAll()
    {
        return $this->approvalStageRepository->getAll();
    }

    public function findById(string|int $id)
    {
        return $this->approvalStageRepository->getById($id);
    }

    public function save(array $data)
    {
        return $this->approvalStageRepository->create($data);
    }

    public function update(string|int $id, array $data)
    {
        return $this->approvalStageRepository->update($id, $data);
    }

    public function delete(string|int $id)
    {
        return $this->approvalStageRepository->delete($id);
    }
}
