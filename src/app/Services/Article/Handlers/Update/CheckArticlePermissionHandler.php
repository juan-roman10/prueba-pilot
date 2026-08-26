<?php

namespace App\Services\Article\Handlers\Update;

use App\Services\Article\Handlers\AbstractArticleHandler;
use Illuminate\Support\Facades\Auth;

class CheckArticlePermissionHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $currentUser = Auth::guard('api')->user();
        $article = $context['article'];

        if ($currentUser->rol !== 'admin' && $article->user_id !== $currentUser->id) {
            throw new \Exception('No tienes permiso para editar este artículo.', 403);
        }

        return parent::handle($context);
    }
}
