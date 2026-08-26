<?php

namespace App\Http\Controllers;

use App\Services\Category\Handlers\Create\SaveCategoryHandler;
use App\Services\Category\Handlers\Create\ValidateCategoryDataHandler;
use App\Services\Category\Handlers\Delete\DeleteCategoryHandler;
use App\Services\Category\Handlers\Delete\FindCategoryForDeletionHandler;
use App\Services\Category\Handlers\Update\FindCategoryHandler;
use App\Services\Category\Handlers\Update\UpdateCategoryHandler;
use App\Services\Category\Handlers\Update\ValidateCategoryUpdateDataHandler;
use App\Services\Category\Handlers\Delete\CheckCategoryHasNoArticlesHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Listar categorías con filtros y paginación
     */
    public function index(Request $request)
    {
        try {
            $filterChain = new ApplyCategoryFiltersHandler();
            $filterChain->setNext(new PaginateCategoriesHandler());
            $result = $filterChain->handle(['filters' => $request->all()]);
            return response()->json($result['categories']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Crear categoría
     */
    public function store(Request $request)
    {
        try {
            $createChain = new ValidateCategoryDataHandler();
            $createChain->setNext(new SaveCategoryHandler());
            $result = $createChain->handle(['data' => $request->all()]);
            return response()->json([
                'message'  => 'Categoría creada exitosamente',
                'category' => $result['category'],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, $id)
    {
        try {
            $updateChain = new FindCategoryHandler();
            $updateChain
                ->setNext(new ValidateCategoryUpdateDataHandler())
                ->setNext(new UpdateCategoryHandler());
            $result = $updateChain->handle([
                'id'   => $id,
                'data' => $request->all(),
            ]);
            return response()->json([
                'message'  => 'Categoría actualizada exitosamente',
                'category' => $result['category'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * Eliminar categoría
     */
    public function destroy($id)
    {
        try {
            $deleteChain = new FindCategoryForDeletionHandler();
            $deleteChain->setNext(new CheckCategoryHasNoArticlesHandler())
                        ->setNext(new DeleteCategoryHandler());
            $deleteChain->handle(['id' => $id]);
            return response()->json(['message' => 'Categoría eliminada correctamente']);
        } catch (\Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }   
}
