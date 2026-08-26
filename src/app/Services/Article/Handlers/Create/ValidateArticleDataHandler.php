<?php

namespace App\Services\Article\Handlers\Create;

use App\Services\Article\Handlers\AbstractArticleHandler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidateArticleDataHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $validator = Validator::make($context['data'], [
            'titulo'            => 'required|string|max:255',
            'contenido'         => 'required|string',
            'estado'            => 'required|in:borrador,publicado',
            'fecha_publicacion' => 'nullable|date',
            'categories'        => 'required|array|min:1',
            'categories.*'      => 'integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $context['validated_data'] = $validator->validated();

        return parent::handle($context);
    }
}
