<?php

namespace App\Services\Article\Handlers\Create;

use App\Models\Article;
use App\Services\Article\Handlers\AbstractArticleHandler;

class SaveArticleWithCategoriesHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $data = $context['validated_data'];
        $categoryIds = $data['categories'];
        unset($data['categories']);

        $article = Article::create($data);
        $article->categories()->sync($categoryIds);

        $context['article'] = $article->load(['user:id,name,email', 'categories']);

        return parent::handle($context);
    }
}
