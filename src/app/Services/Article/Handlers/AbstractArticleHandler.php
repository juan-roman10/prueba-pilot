<?php

namespace App\Services\Article\Handlers;

use App\Services\Article\Contracts\ArticleHandlerInterface;

abstract class AbstractArticleHandler implements ArticleHandlerInterface
{
    private ?ArticleHandlerInterface $nextHandler = null;

    public function setNext(ArticleHandlerInterface $handler): ArticleHandlerInterface
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(array $context): array
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($context);
        }

        return $context;
    }
}
