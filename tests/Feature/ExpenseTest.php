<?php

namespace Tests\Feature;

use App\Enums\StatusEnum;
use App\Repositories\StatusRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createStatusThroughApi(data: ['name' => StatusEnum::MENUNGGU_PERSETUJUAN->value]);
        $this->createStatusThroughApi(data: ['name' => StatusEnum::DISETUJUI->value]);

        $this->createApproverThroughApi(data: ['name' => 'Ani']);
        $this->createApproverThroughApi(data: ['name' => 'Ana']);
        $this->createApproverThroughApi(data: ['name' => 'Ina']);

        $this->createApprovalStageWithoutApproverThroughApi(data: ['approver_id' => 1]);
        $this->createApprovalStageWithoutApproverThroughApi(data: ['approver_id' => 2]);
        $this->createApprovalStageWithoutApproverThroughApi(data: ['approver_id' => 3]);

    }

    public function createExpenseThroughApi(array $data = []): array
    {
        $payload = array_merge([
            'amount' => 5_000,
        ], $data);

        $response = $this->postJson(uri: '/api/expenses', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'expense');
    }

    public function test_can_get_all_expenses(): void
    {
        $this->createExpenseThroughApi(data: ['amount' => 1]);

        $response = $this->getJson(uri: '/api/expenses');
        $response->assertOk()->assertJsonCount(count: 1, key: 'expenses');

        $this->withOutExceptionHandling();
    }

    public function test_can_get_expense_by_id(): void
    {
        $expense = $this->createExpenseThroughApi();

        $response = $this->getJson(uri: "/api/expenses/{$expense['id']}");
        $response->assertOk();
    }

    public function test_can_create_expense(): void
    {
        $payload = [
            'amount' => 1_000,
        ];

        $response = $this->postJson(uri: '/api/expenses', data: $payload);
        $response->assertCreated();

        $this->assertDatabaseHas(table: 'expenses', data: $payload);
    }

    public function test_expense_amount_is_required(): void
    {
        $payload = [
            'amount' => null,
        ];

        $this->postJson(uri: '/api/expenses', data: $payload)
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_can_update_expense(): void
    {
        $expense = $this->createExpenseThroughApi();

        $payload = [
            'amount' => 2_000,
        ];

        $response = $this->putJson(uri: "/api/expenses/{$expense['id']}", data: $payload);
        $response->assertOk();

        $this->assertDatabaseHas(table: 'expenses', data: $payload);
    }

    public function test_can_delete_expense(): void
    {
        $expense = $this->createExpenseThroughApi();

        $response = $this->deleteJson(uri: "/api/expenses/{$expense['id']}");
        $response->assertOk()->assertJson(value: ['expense' => null, 'message' => 'Expense deleted successfully']);

        $this->assertDatabaseMissing(table: 'expenses', data: ['id' => $expense['id']]);
    }

    public function test_amount_minimum_is_1()
    {
        $payload = [
            'amount' => 1,
        ];

        $response = $this->postJson(uri: '/api/expenses', data: $payload);
        $response->assertCreated();

        $this->assertDatabaseHas(table: 'expenses', data: $payload);
    }

    public function test_amount_cant_be_0_or_less()
    {
        $payload = [
            'amount' => 0,
        ];

        $payload2 = [
            'amount' => -1,
        ];

        $this->postJson(uri: '/api/expenses', data: $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['amount']);

        $this->postJson(uri: '/api/expenses', data: $payload2)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_amount_cant_be_string()
    {
        $payload = [
            'amount' => 'string',
        ];

        $this->postJson(uri: '/api/expenses', data: $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['amount']);
    }

    protected function createStatus(string $name): int
    {
        $response = $this->postJson('/api/statuses', ['name' => $name]);

        return $response->json('id');
    }

    protected function createApprovers(int $count): array
    {
        $ids = [];
        for ($i = 1; $i <= $count; $i++) {
            $response = $this->postJson('/api/approvers', ['name' => "Approver $i"]);
            $ids[] = $response->json('approver.id');
        }

        return $ids;
    }

    protected function createExpense(): int
    {
        $response = $this->postJson('/api/expenses', [
            'amount' => 10000,
        ]);

        return $response->json('expense.id');
    }

    protected function createApprovals(array $approverIds): void
    {
        foreach ($approverIds as $approverId) {
            $this->postJson('/api/approval-stages', [
                'approver_id' => $approverId,
            ]);
        }
    }

    public function test_all_approver_approved_from_three_approvals(): void
    {
        $statusId = (new StatusRepository)->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id;
        $this->createApprovers(3);

        $expenseId = $this->createExpense();
        $expenseApprovals = $this->getJson("/api/expenses/{$expenseId}")->json('expense.approvals');

        foreach ($expenseApprovals as $expenseApproval) {
            $this->patchJson("/api/expenses/{$expenseId}/approve", [
                'approver_id' => $expenseApproval['approver_id'],
            ])->assertOk();
        }

        $expense = $this->getJson("/api/expenses/{$expenseId}")->json('expense');

        $this->assertNotEquals($statusId, $expense['status_id']);
        $this->assertNotEquals($statusId, $expense['approvals'][0]['status_id']);
        $this->assertNotEquals($statusId, $expense['approvals'][1]['status_id']);
        $this->assertNotEquals($statusId, $expense['approvals'][2]['status_id']);
    }

    public function test_only_two_approver_approved_from_three_approvals(): void
    {
        $statusId = (new StatusRepository)->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id;
        $this->createApprovers(3);

        $expenseId = $this->createExpense();
        $expenseApprovals = $this->getJson("/api/expenses/{$expenseId}")->json('expense.approvals');

        foreach (array_slice($expenseApprovals, 0, 2) as $expenseApproval) {
            $this->patchJson("/api/expenses/{$expenseId}/approve", [
                'approver_id' => $expenseApproval['approver_id'],
            ])->assertOk();
        }

        $expense = $this->getJson("/api/expenses/{$expenseId}")->json('expense');

        $this->assertEquals($statusId, $expense['status_id']);
        $this->assertNotEquals($statusId, $expense['approvals'][0]['status_id']);
        $this->assertNotEquals($statusId, $expense['approvals'][1]['status_id']);
        $this->assertEquals($statusId, $expense['approvals'][2]['status_id']);
    }

    public function test_only_one_approver_approved_from_three_approvals(): void
    {
        $statusId = (new StatusRepository)->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id;
        $this->createApprovers(3);

        $expenseId = $this->createExpense();
        $expenseApprovals = $this->getJson("/api/expenses/{$expenseId}")->json('expense.approvals');

        foreach (array_slice($expenseApprovals, 0, 1) as $expenseApproval) {
            $this->patchJson("/api/expenses/{$expenseId}/approve", [
                'approver_id' => $expenseApproval['approver_id'],
            ])->assertOk();
        }

        $expense = $this->getJson("/api/expenses/{$expenseId}")->json('expense');

        $this->assertEquals($statusId, $expense['status_id']);
        $this->assertNotEquals($statusId, $expense['approvals'][0]['status_id']);
        $this->assertEquals($statusId, $expense['approvals'][1]['status_id']);
        $this->assertEquals($statusId, $expense['approvals'][2]['status_id']);
    }

    public function test_all_approver_not_approved_from_three_approvals(): void
    {
        $statusId = (new StatusRepository)->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id;
        $approverIds = $this->createApprovers(3);
        $expenseId = $this->createExpense();
        $this->createApprovals($approverIds);

        $expense = $this->getJson("/api/expenses/{$expenseId}")->json('expense');
        $this->assertEquals($statusId, $expense['status_id']);
        $this->assertEquals($statusId, $expense['approvals'][0]['status_id']);
        $this->assertEquals($statusId, $expense['approvals'][1]['status_id']);
        $this->assertEquals($statusId, $expense['approvals'][2]['status_id']);
    }

    public function test_cant_approve_expense_before_previous_approvals_are_approved(): void
    {
        $statusId = (new StatusRepository)->getByName(name: StatusEnum::MENUNGGU_PERSETUJUAN->value)->id;
        $this->createApprovers(3);

        $expenseId = $this->createExpense();
        $expenseApprovals = $this->getJson("/api/expenses/{$expenseId}")->json('expense.approvals');

        $this->patchJson("/api/expenses/{$expenseId}/approve", [
            'approver_id' => $expenseApprovals[0]['approver_id'],
        ])->assertOk();

        $this->patchJson("/api/expenses/{$expenseId}/approve", [
            'approver_id' => $expenseApprovals[2]['approver_id'],
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $expense = $this->getJson("/api/expenses/{$expenseId}")->json('expense');

        $this->assertEquals($statusId, $expense['status_id']);
        $this->assertNotEquals($statusId, $expense['approvals'][0]['status_id']);
        $this->assertEquals($statusId, $expense['approvals'][1]['status_id']);
        $this->assertEquals($statusId, $expense['approvals'][2]['status_id']);
    }

    public function test_expense_approval_approver_id_is_required(): void
    {
        $this->createApprovers(3);

        $expenseId = $this->createExpense();

        $this->patchJson("/api/expenses/{$expenseId}/approve", [
            'approver_id' => null,
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['approver_id']);
    }

    public function test_expense_approval_approver_id_is_must_be_exist_in_database(): void
    {
        $this->createApprovers(3);

        $expenseId = $this->createExpense();

        $this->patchJson("/api/expenses/{$expenseId}/approve", [
            'approver_id' => 9999,
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['approver_id']);
    }
}
