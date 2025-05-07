<?php

namespace App\Interfaces\Expenses;

interface ExpenseServiceInterface
{
    public function findAll();

    public function findById(string|int $id);

    public function save(array $data);

    public function update(string|int $id, array $data);

    public function delete(string|int $id);

    public function approve(string|int $expenseId, string|int $approverId);

    public function checkIfExpenseHasApprovals(string|int $id);

    public function createExpenseApprovalsByApprovalStages(string|int $expenseId, array $data);
}
