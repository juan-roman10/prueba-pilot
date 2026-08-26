<?php

namespace App\Services\Category\Handlers\Update;

use App\Services\Category\Handlers\AbstractCategoryHandler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidateCategoryUpdateDataHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $categoryId = $context['id'];

        $validator = Validator::make($context['data'], [
            'nombre'      => 'sometimes|string|max:255|unique:categories,nombre,' . $categoryId,
            'descripcion' => 'nullable|string|max:1000',
            'estado'      => 'sometimes|in:activa,inactiva',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $context['validated_data'] = $validator->validated();

        return parent::handle($context);
    }
}
