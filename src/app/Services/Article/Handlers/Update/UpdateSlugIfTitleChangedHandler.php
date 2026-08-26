<?php

namespace App\Services\Article\Handlers\Update;

use App\Models\Article;
use App\Services\Article\Handlers\AbstractArticleHandler;
use Illuminate\Support\Str;

class UpdateSlugIfTitleChangedHandler extends AbstractArticleHandler
{
    public function handle(array $context): array
    {
        $data = $context['validated_data'];
        $article = $context['article'];

        if (!empty($data['titulo']) && $data['titulo'] !== $article->titulo) {
            $baseSlug = Str::slug($data['titulo']);
            $slug = $baseSlug;
            $count = 1;

            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }

            $context['validated_data']['slug'] = $slug;
        }

        return parent::handle($context);
    }
}
