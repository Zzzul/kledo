<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApprovalStageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_approval_stages(): void
    {
        $this->createApprovalStageThroughApi(data: ['approver_id' => 1]);

        $response = $this->getJson(uri: '/api/approval-stages');
        $response->assertOk()->assertJsonCount(count: 1, key: 'approval_stages');

        $this->withOutExceptionHandling();
    }

    public function test_can_get_a_approval_stage(): void
    {
        $approvalStage = $this->createApprovalStageThroughApi();

        $response = $this->getJson(uri: "/api/approval-stages/{$approvalStage['id']}");
        $response->assertOk();
    }

    public function test_can_create_approval_stage(): void
    {
        $this->createApproverThroughApi();

        $payload = [
            'approver_id' => 1,
        ];

        $response = $this->postJson(uri: '/api/approval-stages', data: $payload);
        $response->assertCreated();

        $this->assertDatabaseHas(table: 'approval_stages', data: $payload);
    }

    public function test_approver_id_is_required(): void
    {
        $payload = [
            'approver_id' => null,
        ];

        $response = $this->postJson(uri: '/api/approval-stages', data: $payload);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors(['approver_id']);
    }

    public function test_cant_create_approval_stages_because_approver_id_is_not_exist_in_database(): void
    {
        $payload = [
            'approver_id' => 99,
        ];

        $response = $this->postJson(uri: '/api/approval-stages', data: $payload);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors(['approver_id']);
    }

    public function test_can_update_approval_stage(): void
    {
        $approvalStage = $this->createApprovalStageThroughApi();

        $payload = [
            'approver_id' => 2,
        ];

        $response = $this->putJson(uri: "/api/approval-stages/{$approvalStage['id']}", data: $payload);
        $response->assertOk();

        $this->assertDatabaseHas(table: 'approval_stages', data: $payload);
    }

    public function test_can_delete_approval_stage(): void
    {
        $approvalStage = $this->createApprovalStageThroughApi();

        $response = $this->deleteJson(uri: "/api/approval-stages/{$approvalStage['id']}");
        $response->assertOk()->assertJson(value: ['approval_stage' => null, 'message' => 'Approval stage deleted successfully']);

        $this->assertDatabaseMissing(table: 'approval_stages', data: ['id' => $approvalStage['id']]);
    }

    public function test_cant_create_with_same_approver_id(): void
    {
        $this->createApprovalStageThroughApi(data: ['approver_id' => 1]);

        $response = $this->postJson(uri: '/api/approval-stages', data: ['approver_id' => 1]);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_cant_update_with_same_approver_id(): void
    {
        $this->createApprovalStageThroughApi();

        $response = $this->putJson(uri: '/api/approval-stages/1', data: ['approver_id' => 1]);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_approval_stages_approved_id_is_required(): void
    {
        $payload = [
            'approver_id' => null,
        ];

        $this->postJson(uri: '/api/approval-stages', data: $payload)
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['approver_id']);
    }
}
