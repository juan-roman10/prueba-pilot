<?php

namespace App\Services\Category\Handlers\Delete;

use App\Services\Category\Handlers\AbstractCategoryHandler;

class CheckCategoryHasNoArticlesHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $category = $context['category'];

        // Verifica si existen artículos asociados a esta categoría
        if ($category->articles()->exists()) {
            throw new \Exception('No se puede eliminar la categoría porque tiene artículos asociados.', 400);
        }

        return parent::handle($context);
    }
}
