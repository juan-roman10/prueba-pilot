<?php

namespace App\Services\Article\Handlers\List;

use App\Services\Article\Handlers\AbstractArticleHandler;

class PaginateArticlesHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $query = $context['query'];
        $perPage = $context['filters']['per_page'] ?? 10;

        $context['articles'] = $query->paginate($perPage);

        return parent::handle($context);
    }
}
