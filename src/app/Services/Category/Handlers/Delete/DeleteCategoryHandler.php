<?php

namespace App\Services\Category\Handlers\Delete;

use App\Services\Category\Handlers\AbstractCategoryHandler;

class DeleteCategoryHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $category = $context['category'];
        $category->delete();

        $context['deleted'] = true;

        return parent::handle($context);
    }
}
