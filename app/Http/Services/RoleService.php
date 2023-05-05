<?php

namespace App\Http\Services;

use App\Repositories\RoleRepository;



class RoleService extends BaseService {

    public function __construct(private RoleRepository $role) {
        parent::__construct($role);
    }
}
