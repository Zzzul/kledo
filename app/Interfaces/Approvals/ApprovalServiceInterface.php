<?php

namespace App\Interfaces\Approvals;

interface ApprovalServiceInterface
{
    public function findAll();

    public function findById(string|int $id);

    public function save(array $data);

    public function update(string|int $id, array $data);

    public function delete(string|int $id);
}
