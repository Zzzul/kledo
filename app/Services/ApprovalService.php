<?php

namespace App\Services;

use App\Interfaces\Approvals\ApprovalRepositoryInterface;
use App\Interfaces\Approvals\ApprovalServiceInterface;

class ApprovalService implements ApprovalServiceInterface
{
    public function __construct(public ApprovalRepositoryInterface $approvalRepository)
    {
        //
    }

    public function findAll()
    {
        return $this->approvalRepository->getAll();
    }

    public function findById(string|int $id)
    {
        return $this->approvalRepository->getById($id);
    }

    public function save(array $data)
    {
        return $this->approvalRepository->create($data);
    }

    public function update(string|int $id, array $data)
    {
        return $this->approvalRepository->update($id, $data);
    }

    public function delete(string|int $id)
    {
        return $this->approvalRepository->delete($id);
    }
}
