<?php

namespace App\Services;

use App\Models\Material;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class MaterialService
{
    public function list($data)
    {
        try {
            $status = isset($data['status']) ? $data['status'] : null;
            $search = isset($data['search']) ? $data['search'] : null;

            $list = Material::with('category')
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
            return self::successOrErrorResponse(true, 200, "Lista de materiales", $list);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function create($data)
    {
        try {
            $item = new Material();
            $item->fill($data);
            if (isset($data['image']) && $data['image']->isValid()) {
                $file = $data['image'];
                $fileName = $data['name'] . '.png';
                $destinationPath = storage_path("app/public/Materials_images");
                $file->move($destinationPath, $fileName);
                $item->path_image = 'storage/Materials_images/' . $fileName;
            }
            $item->user_created_at = Auth::user()->email;
            $item->save();
            return self::successOrErrorResponse(true, 200, "Material registrado con éxito", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function update($data)
    {
        try {
            $item = Material::find($data['id']);
            if (!$item) {
                throw new ModelNotFoundException('El material no existe');
            }
            $item->fill($data);
            $item->user_updated_at = Auth::user()->email;
            $item->save();
            return self::successOrErrorResponse(true, 200, "Material actualizado con éxito", []);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 401, $e->getMessage(), []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function delete($id)
    {
        try {
            $item = Material::findOrFail($id);
            $item->user_updated_at = Auth::user()->email;
            $item->status = false;
            $item->save();
            return self::successOrErrorResponse(true, 200, "Material eliminado con éxito", []);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Material no encontrado", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function getItem($id)
    {
        try {
            $item = Material::findOrFail($id);
            return self::successOrErrorResponse(true, 200, "Material encontrado", $item);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Material no encontrado", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function getReport($id)
    {
        try {
            $today = Carbon::now();
            $sixMonthsAgo = Carbon::now()->subMonths(6);

            $movements = Material::with(['detail_movements' => function($query) use($today, $sixMonthsAgo) {
                $query
                    ->whereDate('created_at', '>=', $sixMonthsAgo->toDateString())
                    ->whereDate('created_at', '<=', $today->toDateString());
            }])->findOrFail($id);

            // $pdf = Pdf::loadView('reports.Material', ['movements' => $movements]);
            // return $pdf->download('hola.pdf');
            return self::successOrErrorResponse(true, 200, "Movimientos encontrados", $movements);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Movimientos no encontrados", []);
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