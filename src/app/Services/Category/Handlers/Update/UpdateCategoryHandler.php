<?php

namespace App\Services\Category\Handlers\Update;

use App\Services\Category\Handlers\AbstractCategoryHandler;

class UpdateCategoryHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $category = $context['category'];
        $category->update($context['validated_data']);

        $context['category'] = $category->fresh();

        return parent::handle($context);
    }
}
