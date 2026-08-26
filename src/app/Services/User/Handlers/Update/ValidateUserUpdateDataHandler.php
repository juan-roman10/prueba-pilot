<?php

namespace App\Services\User\Handlers\Update;

use App\Services\User\Handlers\AbstractUserHandler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidateUserUpdateDataHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $userId = $context['id'];

        $validator = Validator::make($context['data'], [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'sometimes|string|min:6',
            'rol'      => 'sometimes|in:admin,editor',
            'estado'   => 'sometimes|in:activo,inactivo',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $context['validated_data'] = $validator->validated();

        return parent::handle($context);
    }
}
