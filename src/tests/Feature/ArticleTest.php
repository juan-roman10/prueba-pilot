<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper para generar el token JWT y setear el header de autorización.
     */
    protected function authenticateAs(User $user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_articles(): void
    {
        $response = $this->getJson('/api/articles');
        $response->assertStatus(401);
    }

    #[Test]
    public function inactive_user_cannot_create_article(): void
    {
        $inactiveUser = User::factory()->create([
            'estado' => 'inactivo',
            'rol'    => 'editor',
        ]);
        $category = Category::create([
            'nombre' => 'Tecnología',
            'estado' => 'activa',
        ]);

        $response = $this->authenticateAs($inactiveUser)->postJson('/api/articles', [
            'titulo'     => 'Mi Primer Artículo',
            'contenido'  => 'Contenido de prueba',
            'estado'     => 'publicado',
            'categories' => [$category->id],
        ]);

        $response->assertStatus(403)
                 ->assertJsonFragment([
                     'error' => 'Tu cuenta se encuentra inactiva. No tienes permisos para realizar esta acción.'
                 ]);
    }

    #[Test]
    public function active_user_can_create_article_with_auto_generated_slug_and_categories(): void
    {
        $user = User::factory()->create([
            'estado' => 'activo',
            'rol'    => 'editor',
        ]);
        $category = Category::create([
            'nombre' => 'Deportes',
            'estado' => 'activa',
        ]);

        $response = $this->authenticateAs($user)->postJson('/api/articles', [
            'titulo'     => 'Noticia de Fútbol Internacional',
            'contenido'  => 'Detalles de la noticia...',
            'estado'     => 'publicado',
            'categories' => [$category->id],
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'article' => [
                         'id', 'titulo', 'slug', 'contenido', 'estado', 'user_id', 'categories'
                     ]
                 ]);

        $this->assertDatabaseHas('articles', [
            'titulo'  => 'Noticia de Fútbol Internacional',
            'slug'    => 'noticia-de-futbol-internacional',
            'user_id' => $user->id,
            'estado'  => 'publicado',
        ]);

        $this->assertDatabaseHas('article_category', [
            'category_id' => $category->id,
        ]);
    }

    #[Test]
    public function cannot_create_article_with_invalid_data(): void
    {
        $user = User::factory()->create(['estado' => 'activo']);

        $response = $this->authenticateAs($user)->postJson('/api/articles', [
            'titulo' => '',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['titulo', 'contenido', 'estado', 'categories']);
    }

    #[Test]
    public function user_can_list_and_filter_articles(): void
    {
        $user = User::factory()->create(['estado' => 'activo']);
        $cat1 = Category::create(['nombre' => 'PHP', 'estado' => 'activa']);
        $cat2 = Category::create(['nombre' => 'JS', 'estado' => 'activa']);

        $art1 = Article::create([
            'user_id'   => $user->id,
            'titulo'    => 'Aprende Laravel',
            'slug'      => 'aprende-laravel',
            'contenido' => 'Contenido PHP',
            'estado'    => 'publicado',
        ]);
        $art1->categories()->sync([$cat1->id]);

        $art2 = Article::create([
            'user_id'   => $user->id,
            'titulo'    => 'Aprende Vue',
            'slug'      => 'aprende-vue',
            'contenido' => 'Contenido JS',
            'estado'    => 'borrador',
        ]);
        $art2->categories()->sync([$cat2->id]);

        $response = $this->authenticateAs($user)->getJson('/api/articles?estado=publicado');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $responseSearch = $this->authenticateAs($user)->getJson('/api/articles?search=Laravel');
        $responseSearch->assertStatus(200);
        $this->assertCount(1, $responseSearch->json('data'));
    }

    #[Test]
    public function author_can_update_own_article_and_slug_updates(): void
    {
        $author = User::factory()->create(['estado' => 'activo', 'rol' => 'editor']);
        $article = Article::create([
            'user_id'   => $author->id,
            'titulo'    => 'Título Original',
            'slug'      => 'titulo-original',
            'contenido' => 'Contenido original',
            'estado'    => 'borrador',
        ]);

        $response = $this->authenticateAs($author)->putJson("/api/articles/{$article->id}", [
            'titulo' => 'Título Actualizado',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('articles', [
            'id'     => $article->id,
            'titulo' => 'Título Actualizado',
            'slug'   => 'titulo-actualizado',
        ]);
    }

    #[Test]
    public function editor_cannot_update_another_users_article(): void
    {
        $author = User::factory()->create(['estado' => 'activo', 'rol' => 'editor']);
        $otherEditor = User::factory()->create(['estado' => 'activo', 'rol' => 'editor']);

        $article = Article::create([
            'user_id'   => $author->id,
            'titulo'    => 'Artículo del Autor',
            'slug'      => 'articulo-del-autor',
            'contenido' => 'Contenido',
            'estado'    => 'borrador',
        ]);

        $response = $this->authenticateAs($otherEditor)->putJson("/api/articles/{$article->id}", [
            'titulo' => 'Intento de Hack',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_update_any_article(): void
    {
        $author = User::factory()->create(['estado' => 'activo', 'rol' => 'editor']);
        $admin = User::factory()->create(['estado' => 'activo', 'rol' => 'admin']);

        $article = Article::create([
            'user_id'   => $author->id,
            'titulo'    => 'Artículo del Autor',
            'slug'      => 'articulo-del-autor',
            'contenido' => 'Contenido',
            'estado'    => 'borrador',
        ]);

        $response = $this->authenticateAs($admin)->putJson("/api/articles/{$article->id}", [
            'titulo' => 'Modificado por Admin',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('articles', [
            'id'     => $article->id,
            'titulo' => 'Modificado por Admin',
        ]);
    }

    #[Test]
    public function author_can_delete_own_article(): void
    {
        $author = User::factory()->create(['estado' => 'activo', 'rol' => 'editor']);
        $article = Article::create([
            'user_id'   => $author->id,
            'titulo'    => 'Artículo a borrar',
            'slug'      => 'articulo-a-borrar',
            'contenido' => 'Contenido',
            'estado'    => 'borrador',
        ]);

        $response = $this->authenticateAs($author)->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    #[Test]
    public function editor_cannot_delete_another_users_article(): void
    {
        $author = User::factory()->create(['estado' => 'activo', 'rol' => 'editor']);
        $otherEditor = User::factory()->create(['estado' => 'activo', 'rol' => 'editor']);

        $article = Article::create([
            'user_id'   => $author->id,
            'titulo'    => 'Artículo a borrar',
            'slug'      => 'articulo-a-borrar',
            'contenido' => 'Contenido',
            'estado'    => 'borrador',
        ]);

        $response = $this->authenticateAs($otherEditor)->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    }
}
