<?php

namespace App\Repositories;



class UserRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\Models\User";
    }
}
