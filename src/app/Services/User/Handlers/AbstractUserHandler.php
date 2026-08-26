<?php

namespace App\Services\User\Handlers;

use App\Services\User\Contracts\UserHandlerInterface;

abstract class AbstractUserHandler implements UserHandlerInterface
{
    private ?UserHandlerInterface $nextHandler = null;

    public function setNext(UserHandlerInterface $handler): UserHandlerInterface
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
