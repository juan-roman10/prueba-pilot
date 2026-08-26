<?php

namespace App\Services\Category\Handlers\List;

use App\Models\Category;
use App\Services\Category\Handlers\AbstractCategoryHandler;

class ApplyCategoryFiltersHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $query = Category::query();
        $filters = $context['filters'] ?? [];

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $context['query'] = $query;

        return parent::handle($context);
    }
}
