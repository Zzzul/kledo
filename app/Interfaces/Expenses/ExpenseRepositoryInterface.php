<?php

namespace App\Interfaces\Expenses;

use App\Models\Approval;
use Illuminate\Database\Eloquent\Builder;

interface ExpenseRepositoryInterface
{
    public function getAll();

    public function getById(string|int $id);

    public function create(array $data);

    public function update(string|int $id, array $data);

    public function delete(string|int $id);

    public function relations(): array;

    public function updateApprovalStatus(string|int $approverId, string|int $statusId): Approval|Builder;
}
