<?php

namespace App\Services\User\Handlers\List;

use App\Models\User;
use App\Services\User\Handlers\AbstractUserHandler;

class ApplyFiltersHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $query = User::query();
        $filters = $context['filters'] ?? [];

        if (!empty($filters['rol'])) {
            $query->where('rol', $filters['rol']);
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $context['query'] = $query;

        return parent::handle($context);
    }
}
