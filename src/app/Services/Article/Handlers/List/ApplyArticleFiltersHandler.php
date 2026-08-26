<?php

namespace App\Services\Article\Handlers\List;

use App\Models\Article;
use App\Services\Article\Handlers\AbstractArticleHandler;

class ApplyArticleFiltersHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $query = Article::with(['user:id,name,email', 'categories:id,nombre']);
        $filters = $context['filters'] ?? [];

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('contenido', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $context['query'] = $query->orderBy('created_at', 'desc');

        return parent::handle($context);
    }
}
