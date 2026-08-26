<?php

namespace App\Services\Article\Handlers\Delete;

use App\Models\Article;
use App\Services\Article\Handlers\AbstractArticleHandler;

class FindArticleForDeletionHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $article = Article::find($context['id']);

        if (!$article) {
            throw new \Exception('Artículo no encontrado.', 404);
        }

        $context['article'] = $article;

        return parent::handle($context);
    }
}
