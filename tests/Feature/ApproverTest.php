<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApproverTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_approvers(): void
    {
        $this->createApproverThroughApi(data: ['name' => 'Ana']);
        $this->createApproverThroughApi(data: ['name' => 'Ani']);

        $response = $this->getJson(uri: '/api/approvers');
        $response->assertOk()->assertJsonCount(count: 2, key: 'approvers');
    }

    public function test_can_get_a_approver(): void
    {
        $approver = $this->createApproverThroughApi();

        $response = $this->getJson(uri: "/api/approvers/{$approver['id']}");
        $response->assertOk();
    }

    public function test_can_create_approver(): void
    {
        $payload = [
            'name' => 'Ana',
        ];

        $response = $this->postJson(uri: '/api/approvers', data: $payload);
        $response->assertCreated();

        $this->assertDatabaseHas(table: 'approvers', data: $payload);
    }

    public function test_can_update_approver(): void
    {
        $approver = $this->createApproverThroughApi();

        $payload = [
            'name' => 'Ani',
        ];

        $response = $this->putJson(uri: "/api/approvers/{$approver['id']}", data: $payload);
        $response->assertOk();

        $this->assertDatabaseHas(table: 'approvers', data: $payload);
    }

    public function test_can_delete_approver(): void
    {
        $approver = $this->createApproverThroughApi();

        $response = $this->deleteJson(uri: "/api/approvers/{$approver['id']}");
        $response->assertOk()->assertJson(value: ['approver' => null, 'message' => 'Approver deleted successfully']);

        $this->assertDatabaseMissing(table: 'approvers', data: ['id' => $approver['id']]);
    }

    public function test_cant_create_with_same_name(): void
    {
        $this->createApproverThroughApi(data: ['name' => 'Ana']);

        $response = $this->postJson(uri: '/api/approvers', data: ['name' => 'Ana']);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_cant_update_with_same_name(): void
    {
        $this->createApproverThroughApi(data: ['name' => 'Ani']);
        $approver = $this->createApproverThroughApi(data: ['name' => 'Ana']);

        $response = $this->putJson(uri: "/api/approvers/{$approver['id']}", data: ['name' => 'Ani']);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_approver_name_is_required(): void
    {
        $payload = [
            'name' => null,
        ];

        $this->postJson(uri: '/api/approvers', data: $payload)
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_approver_name_cant_more_than_255(): void
    {
        $payload = [
            'name' => str()->random(256),
        ];

        $this->postJson(uri: '/api/approvers', data: $payload)
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }
}
