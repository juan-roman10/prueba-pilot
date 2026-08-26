<?php

namespace App\Services\Article\Handlers\Common;

use App\Services\Article\Handlers\AbstractArticleHandler;
use Illuminate\Support\Facades\Auth;

class CheckActiveUserHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $currentUser = Auth::guard('api')->user();

        if (!$currentUser || $currentUser->estado !== 'activo') {
            throw new \Exception('Tu cuenta se encuentra inactiva. No tienes permisos para realizar esta acción.', 403);
        }

        return parent::handle($context);
    }
}
