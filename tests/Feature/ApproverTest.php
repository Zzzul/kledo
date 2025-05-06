<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApproverTest extends TestCase
{
    use RefreshDatabase;

    protected function createPostThroughApi(array $data = []): array
    {
        $payload = array_merge([
            'name' => 'Ana',
        ], $data);

        $response = $this->postJson(uri: '/api/approvers', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'approver');
    }

    public function test_can_get_all_approvers(): void
    {
        $this->createPostThroughApi(data: ['name' => 'Ana']);
        $this->createPostThroughApi(data: ['name' => 'Ani']);

        $response = $this->getJson(uri: '/api/approvers');
        $response->assertOk()->assertJsonCount(count: 2, key: 'approvers');
    }

    public function test_can_get_a_approver(): void
    {
        $approver = $this->createPostThroughApi();

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
        $approver = $this->createPostThroughApi();

        $payload = [
            'name' => 'Ani',
        ];

        $response = $this->putJson(uri: "/api/approvers/{$approver['id']}", data: $payload);
        $response->assertOk();

        $this->assertDatabaseHas(table: 'approvers', data: $payload);
    }

    public function test_can_delete_approver(): void
    {
        $approver = $this->createPostThroughApi();

        $response = $this->deleteJson(uri: "/api/approvers/{$approver['id']}");
        $response->assertOk()->assertJson(value: ['approver' => null, 'message' => 'Approver deleted successfully']);

        $this->assertDatabaseMissing(table: 'approvers', data: ['id' => $approver['id']]);
    }

    public function test_cant_create_with_same_name(): void{
      $this->createPostThroughApi(data: ['name' => 'Ana']);

      $response = $this->postJson(uri: '/api/approvers', data: ['name' => 'Ana']);
      $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_cant_update_with_same_name(): void{
        $this->createPostThroughApi(data: ['name' => 'Ani']);
        $approver = $this->createPostThroughApi(data: ['name' => 'Ana']);
  
        $response = $this->putJson(uri: "/api/approvers/{$approver['id']}", data: ['name' => 'Ani']);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
      }
}
