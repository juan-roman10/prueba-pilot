<?php

namespace App\Services\User\Contracts;

interface UserHandlerInterface
{
    public function setNext(UserHandlerInterface $handler): UserHandlerInterface;
    public function handle(array $context): array;
}
