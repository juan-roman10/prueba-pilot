<?php

namespace App\Services\User\Handlers\Delete;

use App\Services\User\Handlers\AbstractUserHandler;
use Illuminate\Support\Facades\Auth;

class PreventSelfDeletionHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $currentUser = Auth::guard('api')->user();

        if ($currentUser && $currentUser->id == $context['id']) {
            throw new \Exception('No puedes eliminar tu propia cuenta.', 400);
        }

        return parent::handle($context);
    }
}
