<?php

namespace App\Repositories;



class RoleRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return  "App\Models\Role";
    }
}
