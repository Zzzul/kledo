<?php

namespace App\Interfaces;

interface StatusRepositoryInterface
{
    public function getAll();
    public function getById(string|int $id);
    public function create(array $data);
    public function update(string|int $id, array $data);
    public function delete(string|int $id);
}