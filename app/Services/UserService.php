<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function list($data)
    {
        try {
            $status = isset($data['status']) ? $data['status'] : null;
            $search = isset($data['search']) ? $data['search'] : null;

            $list = User::with('role')
                ->when($status !== null, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($search !== null, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('names', 'like', $search . '%')
                        ->orWhere('username', 'like', $search . '%');
                    });
                })
                ->orderBy('created_at','desc')
                ->get();
            return self::successOrErrorResponse(true, 200, "Lista de usuarios", $list);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function create($data)
    {
        try {
            $item = new User();
            $item->fill($data);
            $item->user_created_at = Auth::user()->email;
            $item->save();
            if (isset($data['image']) && $data['image']->isValid()) {
                $file = $data['image'];
                $fileName = $item->id . '.png';
                $destinationPath = storage_path("app/public/Users_images");
                $file->move($destinationPath, $fileName);
                $item->path_image = 'storage/Users_images/' . $fileName;
            }
            $item->save();
            return self::successOrErrorResponse(true, 200, "Usuario registrado con éxito", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function update($data)
    {
        try {
            $item = User::find($data['id']);
            if (!$item) {
                throw new ModelNotFoundException('El usuario no existe');
            }
            $item->fill($data);
            $item->user_updated_at = Auth::user()->email;
            $item->save();
            return self::successOrErrorResponse(true, 200, "Usuario actualizado con éxito", []);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 401, $e->getMessage(), []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function delete($id)
    {
        try {
            $item = User::findOrFail($id);
            $item->user_updated_at = Auth::user()->email;
            $item->status = false;
            $item->save();
            return self::successOrErrorResponse(true, 200, "Usuario eliminado con éxito", []);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Usuario no encontrado", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function getItem($id)
    {
        try {
            $item = User::findOrFail($id);
            return self::successOrErrorResponse(true, 200, "Usuario encontrado", $item);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Usuario no encontrado", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function listRoles($data)
    {
        try {
            $status = isset($data['status']) ? $data['status'] : null;
            $search = isset($data['search']) ? $data['search'] : null;

            $list = Role::query()
                ->when($status !== null, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($search !== null, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', $search . '%');
                    });
                })
                ->orderBy('created_at','desc')
                ->get();
            return self::successOrErrorResponse(true, 200, "Lista de roles", $list);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    protected static function successOrErrorResponse($success, $status, $message, $data)
    {
        $response = [
            'success' => $success,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
        return response()->json($response, $status);
    }
}