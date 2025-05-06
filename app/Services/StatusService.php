<?php

namespace App\Services;

use App\Interfaces\StatusRepositoryInterface;
use App\Interfaces\StatusServiceInterface;

class StatusService implements StatusServiceInterface
{
    public function __construct(public StatusRepositoryInterface $statusRepository)
    {
        //
    }

    public function findAll()
    {
        return $this->statusRepository->getAll();
    }

    public function findById(string|int $id)
    {
        return $this->statusRepository->getById($id);
    }

    public function save(array $data)
    {
        return $this->statusRepository->create($data);
    }

    public function update(string|int $id, array $data)
    {
        return $this->statusRepository->update($id, $data);
    }

    public function delete(string|int $id)
    {
        return $this->statusRepository->delete($id);
    }
}
