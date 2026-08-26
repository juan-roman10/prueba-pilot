<?php

namespace App\Services\Article\Handlers\Delete;

use App\Services\Article\Handlers\AbstractArticleHandler;
use Illuminate\Support\Facades\Auth;

class CheckArticleDeletePermissionHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $currentUser = Auth::guard('api')->user();
        $article = $context['article'];

        if ($currentUser->rol !== 'admin' && $article->user_id !== $currentUser->id) {
            throw new \Exception('No tienes permiso para eliminar este artículo.', 403);
        }

        return parent::handle($context);
    }
}
