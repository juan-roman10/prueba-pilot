<?php

namespace App\Services\Article\Handlers\Update;

use App\Services\Article\Handlers\AbstractArticleHandler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidateArticleUpdateDataHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $validator = Validator::make($context['data'], [
            'titulo'            => 'sometimes|string|max:255',
            'contenido'         => 'sometimes|string',
            'estado'            => 'sometimes|in:borrador,publicado',
            'fecha_publicacion' => 'nullable|date',
            'categories'        => 'sometimes|array|min:1',
            'categories.*'      => 'integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $context['validated_data'] = $validator->validated();

        return parent::handle($context);
    }
}
