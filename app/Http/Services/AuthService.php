<?php

namespace App\Http\Services;

use App\Exceptions\ErrorResponse;
use App\Http\Trait\UserDetailsTrait;
use Illuminate\Support\Facades\Hash;

class AuthService {

    use UserDetailsTrait;

    public function __construct(
        private UserService $userService,
        private RoleService $roleService,
        private PasswordService $passwordService,
    ) {
    }

    public function register(array $registerPayload) {
        $payload = $this->generateUserDetails($registerPayload);

        $role = $this->roleService->findOne(['slug' => 'user']);
        $payload['role_id'] = $role->id;

        $user = $this->userService->create($payload);

        $activation_link = env('APP_URL') . '/api/v1/' . $user->email . '/verify/' . $user->verification_code;
        //TODO SEND EMAIL
        return $activation_link;
    }

    public function login(array $payload) {
        ['email' => $email, 'password' => $password, 'role_id' => $roleId] = $payload;

        $user = $this->userService->findOne(['email' => $email]);
        if (!Hash::check($password, $user->password)) throw new ErrorResponse("invalid password provided");

        if (!$user->verified) throw new ErrorResponse('Please check your email to verify your account!!!', 400);
        if ($user->role_id !== $roleId) throw new ErrorResponse('invalid id provided', 400);

        $token = $user->createToken('smsApiToken')->plainTextToken;

        return array('user' => $user, 'token' => $token);
    }

    public function verifyEmail(string $email, $code) {
        $user = $this->userService->findOne(['email' => $email]);
        if ($user->verification_code !== $code) throw new ErrorResponse("Code does not match", 400);

        if ($user->verified) throw new ErrorResponse('User already verified', 400);
        if (!$this->verifyTimeDiff($user->verification_code_generated_at))
            throw new ErrorResponse('Activation link has expired. Please request a new activation link', 400);

        $this->userService->update($user->id, [
            'verified' => 1, 'verified_at' => now()
        ]);

        $token = $user->createToken('smsApiToken')->plainTextToken;

        return array('user' => $user, 'token' => $token);
    }

    public function resendVerificationEmail(string $email) {
        $user = $this->userService->findOne(['email' => $email]);
        if ($user->verified) throw new ErrorResponse('User already verified', 400);

        $code = $this->generateVerificationCode();

        $this->userService->update(
            $user->id,
            [
                'verification_code' => $code,
                'verification_code_generated_at' => now()
            ],
        );

        $activation_link = env('APP_URL') . '/api/v1/' . $user->email . '/verify/' . $code;
        //TODO SEND EMAIL
        return $activation_link;
    }

    public function forgotPassword(string $email) {
        $user = $this->userService->findOne(['email' => $email]);
        if (!$user->verified) throw new ErrorResponse('Unverified account', 401);

        $this->passwordService->upsert(['email' => $email], [
            'token' => $this->generateIdentity(),
            'created_at' => now()
        ]);

        $this->userService->update($user->id, ['password' => '']);

        return $user;
    }


    public function resetPassword($email, array $payload) {
        $passwordData = $this->passwordService->findOne(['email' => $email]);
        if (!$passwordData) throw new ErrorResponse('Password Data Not found', 404);

        $user = $this->userService->findOne(['email' => $passwordData->email, 'verified' => 1]);
        if (!$user) throw new ErrorResponse('Email not verified', 422);

        if (!$this->verifyTimeDiff($passwordData->created_at))
            throw new ErrorResponse('Code Expired', 422);

        $this->userService->update($user->id, ['password' => $payload['password']]);
        $passwordData->delete();

        $token = $user->createToken('smsApiToken')->plainTextToken;

        return array('user' => $user, 'token' => $token);
    }

    public function logout() {
        return auth()->logout();
    }
}
