<?php

namespace App\Services;

use App\Interfaces\ApproverRepositoryInterface;
use App\Interfaces\ApproverServiceInterface;

class ApproverService implements ApproverServiceInterface
{
    public function __construct(public ApproverRepositoryInterface $approverRepository)
    {
        //
    }

    public function findAll()
    {
        return $this->approverRepository->getAll();
    }

    public function findById(string|int $id)
    {
        return $this->approverRepository->getById($id);
    }

    public function save(array $data)
    {
        return $this->approverRepository->create($data);
    }

    public function update(string|int $id, array $data)
    {
        return $this->approverRepository->update($id, $data);
    }

    public function delete(string|int $id)
    {
        return $this->approverRepository->delete($id);
    }
}
