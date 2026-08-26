<?php

namespace App\Services\User\Handlers\Update;

use App\Services\User\Handlers\AbstractUserHandler;
use Illuminate\Support\Facades\Hash;

class UpdateUserHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $user = $context['user'];
        $data = $context['validated_data'];

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $context['user'] = $user->fresh();

        return parent::handle($context);
    }
}
