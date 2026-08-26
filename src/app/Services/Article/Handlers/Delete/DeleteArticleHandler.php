<?php

namespace App\Services\Article\Handlers\Delete;

use App\Services\Article\Handlers\AbstractArticleHandler;

class DeleteArticleHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $article = $context['article'];
        $article->delete();

        $context['deleted'] = true;

        return parent::handle($context);
    }
}
