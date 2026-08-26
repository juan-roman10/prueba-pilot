<?php

namespace App\Services\User\Handlers\Create;

use App\Services\User\Handlers\AbstractUserHandler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidateUserDataHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $validator = Validator::make($context['data'], [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'rol'      => 'required|in:admin,editor',
            'estado'   => 'required|in:activo,inactivo',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $context['validated_data'] = $validator->validated();

        return parent::handle($context);
    }
}
