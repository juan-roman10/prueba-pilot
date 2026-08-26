<?php

namespace App\Services\Category\Handlers;

use App\Services\Category\Contracts\CategoryHandlerInterface;

abstract class AbstractCategoryHandler implements CategoryHandlerInterface
{
    private ?CategoryHandlerInterface $nextHandler = null;

    public function setNext(CategoryHandlerInterface $handler): CategoryHandlerInterface
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
