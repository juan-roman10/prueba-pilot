<?php

namespace App\Http\Controllers;

use App\Services\Category\Handlers\Create\SaveCategoryHandler;
use App\Services\Category\Handlers\Create\ValidateCategoryDataHandler;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
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
}
