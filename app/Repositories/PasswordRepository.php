<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Eloquent\BaseRepository;


class PasswordRepository extends BaseRepository implements CacheableInterface {

    use CacheableRepository;

    public function model() {
        return 'App\Models\Password';
    }
}
