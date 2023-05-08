<?php

namespace App\Http\Services;

use App\Exceptions\ErrorResponse;
use App\Repositories\UserRepository;

class UserService extends BaseService {

    public function __construct(private UserRepository $user) {
        parent::__construct($user);
    }

    public function updateUser(string $uuid, array $payload,) {
        if (!$uuid) throw new ErrorResponse('uuid is required');

        $user = $this->user->findByField('uuid', $uuid)->first();
        if (!$user) throw new ErrorResponse('user not found', 404);

        return $user->update($payload);
    }

    public function deleteUser(string $uuid) {
        if (!$uuid) throw new ErrorResponse('uuid is required');

        $user = $this->user->findByField('uuid', $uuid)->first();
        if (!$user) throw new ErrorResponse('user not found', 404);

        return $user->delete();
    }
}
