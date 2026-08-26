<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\Article\Handlers\Create\ValidateArticleDataHandler;
use App\Services\Article\Handlers\Create\GenerateUniqueSlugHandler;
use App\Services\Article\Handlers\Create\AssignAuthorAndDatesHandler;
use App\Services\Article\Handlers\Create\SaveArticleWithCategoriesHandler;
use App\Services\Article\Handlers\Update\FindArticleHandler;
use App\Services\Article\Handlers\Update\CheckArticlePermissionHandler;
use App\Services\Article\Handlers\Update\ValidateArticleUpdateDataHandler;
use App\Services\Article\Handlers\Update\UpdateSlugIfTitleChangedHandler;
use App\Services\Article\Handlers\Update\UpdateArticleWithCategoriesHandler;
use App\Services\Article\Handlers\Delete\FindArticleForDeletionHandler;
use App\Services\Article\Handlers\Delete\CheckArticleDeletePermissionHandler;
use App\Services\Article\Handlers\Delete\DeleteArticleHandler;

class ArticleController extends Controller
{
    /**
     * Crear artículo
     */
    public function store(Request $request)
    {
        try {
            $chain = new ValidateArticleDataHandler();
            $chain
                ->setNext(new GenerateUniqueSlugHandler())
                ->setNext(new AssignAuthorAndDatesHandler())
                ->setNext(new SaveArticleWithCategoriesHandler());
            $result = $chain->handle(['data' => $request->all()]);
            return response()->json([
                'message' => 'Artículo creado exitosamente',
                'article' => $result['article'],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * Actualizar artículo
     */
    public function update(Request $request, $id)
    {
        try {
            $chain = new FindArticleHandler();
            $chain
                ->setNext(new CheckArticlePermissionHandler())
                ->setNext(new ValidateArticleUpdateDataHandler())
                ->setNext(new UpdateSlugIfTitleChangedHandler())
                ->setNext(new UpdateArticleWithCategoriesHandler());
            $result = $chain->handle([
                'id'   => $id,
                'data' => $request->all(),
            ]);
            return response()->json([
                'message' => 'Artículo actualizado exitosamente',
                'article' => $result['article'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * Eliminar artículo
     */
    public function destroy($id)
    {
        try {
            $chain = new FindArticleForDeletionHandler();
            $chain
                ->setNext(new CheckArticleDeletePermissionHandler())
                ->setNext(new DeleteArticleHandler());
            $chain->handle(['id' => $id]);
            return response()->json(['message' => 'Artículo eliminado correctamente']);
        } catch (\Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }
}
