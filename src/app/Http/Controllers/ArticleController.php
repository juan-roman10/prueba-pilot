<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\Article\Handlers\Create\ValidateArticleDataHandler;
use App\Services\Article\Handlers\Create\GenerateUniqueSlugHandler;
use App\Services\Article\Handlers\Create\AssignAuthorAndDatesHandler;
use App\Services\Article\Handlers\Create\SaveArticleWithCategoriesHandler;

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
}
