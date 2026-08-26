<?php

namespace App\Services\Article\Contracts;

interface ArticleHandlerInterface
{
    public function setNext(ArticleHandlerInterface $handler): ArticleHandlerInterface;
    public function handle(array $context): array;
}
