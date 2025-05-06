<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Interfaces\StatusServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Statuses\StatusResource;
use App\Http\Resources\Statuses\StatusCollection;
use App\Http\Requests\Statuses\StoreStatusRequest;
use App\Http\Requests\Statuses\UpdateStatusRequest;

class StatusController extends Controller
{
    public function __construct(public StatusServiceInterface $statusService){
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): StatusCollection
    {
        $statuses = $this->statusService->findAll();

        return new StatusCollection(resource: $statuses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStatusRequest $request): JsonResponse
    {
        $status = $this->statusService->save(data: $request->validated());
        
        return (new StatusResource(resource: $status))->response()->setStatusCode(code: Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): StatusResource
    {
        $status = $this->statusService->findById(id: $id);

        return new StatusResource(resource: $status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, string|int $id): StatusResource
    {
        $status = $this->statusService->update(id: $id, data: $request->validated());

        return new StatusResource(resource: $status);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $this->statusService->delete(id: $id);

        return (new StatusResource(resource: null))->response()->setStatusCode(code: Response::HTTP_NO_CONTENT);
    }
}
