<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function list($data)
    {
        try {
            $status = isset($data['status']) ? $data['status'] : null;
            $search = isset($data['search']) ? $data['search'] : null;

            $list = Category::query()
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
            return self::successOrErrorResponse(true, 200, "Lista de categorías", $list);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function create($data)
    {
        try {
            $item = new Category();
            $item->fill($data);
            if (isset($dataTypeItem['image']) && $dataTypeItem['image']->isValid()) {
                $file = $data['image'];
                $fileName = $data['name'] . '.png';
                $destinationPath = storage_path("app/public/Categories_images");
                $file->move($destinationPath, $fileName);
                $item->path_image = 'storage/Categories_images/' . $fileName;
            }
            $item->user_created_at = "admin@service.com";
            //$item->user_created_at = Auth::user()->email;
            $item->save();
            return self::successOrErrorResponse(true, 200, "Categoría registrada con éxito", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function update($data)
    {
        try {
            $item = Category::find($data['id']);
            if (!$item) {
                throw new ModelNotFoundException('La categoria no existe');
            }
            $item->fill($data);
            $item->user_updated_at = "admin@service.com";
            //$item->user_updated_at = Auth::user()->email;
            $item->save();
            return self::successOrErrorResponse(true, 200, "Categoría actualizada con éxito", []);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 401, $e->getMessage(), []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function delete($id)
    {
        try {
            $item = Category::findOrFail($id);
            $item->status = false;
            $item->save();
            return self::successOrErrorResponse(true, 200, "Categoría eliminada con éxito", []);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Categoría no encontrada", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function getItem($id)
    {
        try {
            $item = Category::findOrFail($id);
            return self::successOrErrorResponse(true, 200, "Categoría encontrada", $item);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Categoría no encontrada", []);
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