<?php

namespace App\Services\User\Handlers\Delete;

use App\Models\User;
use App\Services\User\Handlers\AbstractUserHandler;

class DeleteUserHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $user = User::find($context['id']);

        if (!$user) {
            throw new \Exception('Usuario no encontrado.', 404);
        }

        $user->delete();
        $context['deleted'] = true;

        return parent::handle($context);
    }
}
