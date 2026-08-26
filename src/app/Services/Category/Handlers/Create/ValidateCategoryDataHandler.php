<?php

namespace App\Services\Category\Handlers\Create;

use App\Services\Category\Handlers\AbstractCategoryHandler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidateCategoryDataHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $validator = Validator::make($context['data'], [
            'nombre'      => 'required|string|max:255|unique:categories,nombre',
            'descripcion' => 'nullable|string|max:1000',
            'estado'      => 'nullable|in:activa,inactiva',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $context['validated_data'] = $validator->validated();

        return parent::handle($context);
    }
}
