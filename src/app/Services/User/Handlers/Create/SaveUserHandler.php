<?php

namespace App\Services\User\Handlers\Create;

use App\Models\User;
use App\Services\User\Handlers\AbstractUserHandler;
use Illuminate\Support\Facades\Hash;

class SaveUserHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $data = $context['validated_data'];
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $context['user'] = $user;

        return parent::handle($context);
    }
}
