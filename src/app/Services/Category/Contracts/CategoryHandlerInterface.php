<?php

namespace App\Services\Category\Contracts;

interface CategoryHandlerInterface
{
    public function setNext(CategoryHandlerInterface $handler): CategoryHandlerInterface;
    public function handle(array $context): array;
}
