<?php

namespace App\Services\Article\Handlers\Create;

use App\Models\Article;
use App\Services\Article\Handlers\AbstractArticleHandler;
use Illuminate\Support\Str;

class GenerateUniqueSlugHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $titulo = $context['validated_data']['titulo'];
        $baseSlug = Str::slug($titulo);
        $slug = $baseSlug;
        $count = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        $context['validated_data']['slug'] = $slug;

        return parent::handle($context);
    }
}
