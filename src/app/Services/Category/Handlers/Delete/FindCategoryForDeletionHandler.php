<?php

namespace App\Services\Category\Handlers\Delete;

use App\Models\Category;
use App\Services\Category\Handlers\AbstractCategoryHandler;

class FindCategoryForDeletionHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $category = Category::find($context['id']);

        if (!$category) {
            throw new \Exception('Categoría no encontrada.', 404);
        }

        $context['category'] = $category;

        return parent::handle($context);
    }
}
