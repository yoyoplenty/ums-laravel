<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository as Repository;


abstract class BaseRepository extends Repository {

    public abstract function model();

    /**
     * @param string $column
     * @param string $direction
     * @return mixed
     */
    public function orderBy($column = 'id', $direction = 'desc') {
        return $this->model->orderBy($column, $direction);
    }

    /**
     * @param array $columns
     * @param array $data
     * @return mixed
     */


    public function updateEntity($data, $id) {
        try {
            return tap($this->model::where('id', $id)->first())->update($data)->fresh();
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function updateEntityUuid($data, $uuid) {
        try {
            return tap($this->model::where('uuid', $uuid)->first())->update($data)->fresh();
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function _delete($column, $value) {
        return $this->model->where($column, '=', $value)->delete();
    }
}
