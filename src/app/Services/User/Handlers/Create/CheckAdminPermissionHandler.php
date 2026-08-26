<?php

namespace App\Services\User\Handlers\Create;

use App\Services\User\Handlers\AbstractUserHandler;
use Illuminate\Support\Facades\Auth;

class CheckAdminPermissionHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $currentUser = Auth::guard('api')->user();

        if (!$currentUser || $currentUser->rol !== 'admin') {
            throw new \Exception('No tienes permisos de administrador para realizar esta acción.', 403);
        }

        return parent::handle($context);
    }
}
