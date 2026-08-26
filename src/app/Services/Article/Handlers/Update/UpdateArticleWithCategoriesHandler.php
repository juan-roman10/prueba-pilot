<?php

namespace App\Services\Article\Handlers\Update;

use App\Services\Article\Handlers\AbstractArticleHandler;

class UpdateArticleWithCategoriesHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $article = $context['article'];
        $data = $context['validated_data'];

        if (isset($data['categories'])) {
            $article->categories()->sync($data['categories']);
            unset($data['categories']);
        }

        if (isset($data['estado']) && $data['estado'] === 'publicado' && empty($article->fecha_publicacion) && empty($data['fecha_publicacion'])) {
            $data['fecha_publicacion'] = now();
        }

        $article->update($data);
        $context['article'] = $article->fresh()->load(['user:id,name,email', 'categories']);

        return parent::handle($context);
    }
}
