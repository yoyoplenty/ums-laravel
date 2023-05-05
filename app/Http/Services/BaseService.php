<?php

namespace App\Http\Services;

use App\Exceptions\ErrorResponse;

class BaseService {
    protected $repository;
    protected string $name;

    public function __construct($repository, string $name = 'Data') {
        $this->repository = $repository;
        $this->name = $name;
    }

    public function create(array $payload) {
        $data =  $this->repository->create($payload);
        if (!$data) throw new ErrorResponse('unable to create ' . $this->name);

        return $data;
    }

    public function findAll($query = null) {
        $data = $query ? $this->repository->findWhere($query) : $this->repository->all();
        if (!$data) throw new ErrorResponse('unable to fetch ' . $this->name, 404);

        return $data;
    }

    public function paginate() {
        $data = $this->repository->paginate();
        if (!$data) throw new ErrorResponse('unable to fetch ' . $this->name, 404);

        return $data;
    }

    public function find(int $key) {
        $data =  $this->repository->find($key);
        if (!$data) throw new ErrorResponse('unable to fetch ' . $this->name, 404);

        return $data;
    }

    public function findOne(array $query) {
        $data = $this->repository->findWhere($query)->first();
        if (!$data) throw new ErrorResponse('unable to fetch ' . $this->name, 404);

        return $data;
    }

    public function update(int $key, array $payload) {
        $data = $this->repository->find($key);
        if (!$data) throw new ErrorResponse('unable to update ' . $this->name, 404);

        return $data->update($payload);
    }

    public function delete(int $key) {
        $data = $this->repository->find($key);
        if (!$data) throw new ErrorResponse('unable to delete ' . $this->name, 404);

        return $data->delete();
    }
}
