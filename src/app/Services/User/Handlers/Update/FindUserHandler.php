<?php

namespace App\Services\User\Handlers\Update;

use App\Models\User;
use App\Services\User\Handlers\AbstractUserHandler;

class FindUserHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $user = User::find($context['id']);

        if (!$user) {
            throw new \Exception('Usuario no encontrado.', 404);
        }

        $context['user'] = $user;

        return parent::handle($context);
    }
}
