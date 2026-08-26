<?php

namespace App\Services\Article\Handlers\Create;

use App\Services\Article\Handlers\AbstractArticleHandler;
use Illuminate\Support\Facades\Auth;

class AssignAuthorAndDatesHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $data = $context['validated_data'];
        $data['user_id'] = Auth::guard('api')->id();

        // Si se publica y no se especificó fecha, colocar la fecha y hora actual
        if ($data['estado'] === 'publicado' && empty($data['fecha_publicacion'])) {
            $data['fecha_publicacion'] = now();
        }

        $context['validated_data'] = $data;

        return parent::handle($context);
    }
}
