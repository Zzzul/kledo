<?php

namespace App\Repositories;

use App\Interfaces\StatusRepositoryInterface;
use App\Models\Status;

class StatusRepository implements StatusRepositoryInterface {
    public function getAll(){
        return Status::paginate();
    }

    public function getById(string|int $id){
        return Status::find($id);
    }
    
    public function create(array $data){
        $status = Status::create($data);

        return $status;
    }

    public function update(string|int $id, array $data){
        $status = $this->getById($id);

        $status->update($data);

        return $status;
    }

    public function delete(string|int $id){
        $status = $this->getById($id);

        return $status->delete();
    }
}