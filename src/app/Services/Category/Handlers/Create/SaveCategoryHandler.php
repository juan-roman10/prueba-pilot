<?php

namespace App\Services\Category\Handlers\Create;

use App\Models\Category;
use App\Services\Category\Handlers\AbstractCategoryHandler;

class SaveCategoryHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $data = $context['validated_data'];
        $data['estado'] = $data['estado'] ?? 'activa'; // 'activa' por defecto

        $category = Category::create($data);
        $context['category'] = $category;

        return parent::handle($context);
    }
}
