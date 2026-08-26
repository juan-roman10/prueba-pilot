<?php

namespace App\Http\Controllers;

use App\Services\User\Handlers\Create\CheckAdminPermissionHandler;
use App\Services\User\Handlers\Create\SaveUserHandler;
use App\Services\User\Handlers\Create\ValidateUserDataHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Crear nuevo usuario
     */
    public function store(Request $request)
    {
        try {
            $createChain = new CheckAdminPermissionHandler();
            $createChain
                ->setNext(new ValidateUserDataHandler())
                ->setNext(new SaveUserHandler());
            $result = $createChain->handle(['data' => $request->all()]);
            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'user'    => $result['user'],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        try {
            $updateChain = new CheckAdminPermissionHandler();
            $updateChain
                ->setNext(new FindUserHandler())
                ->setNext(new ValidateUserUpdateDataHandler())
                ->setNext(new UpdateUserHandler());
            $result = $updateChain->handle([
                'id'   => $id,
                'data' => $request->all(),
            ]);
            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
                'user'    => $result['user'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }
}
