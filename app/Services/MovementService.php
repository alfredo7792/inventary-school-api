<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovementService
{
    public function list($data)
    {
        try {
            $status = isset($data['status']) ? $data['status'] : null;
            $search = isset($data['search']) ? $data['search'] : null;

            $list = Movement::with('movementDetails')
                ->when($status !== null, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($search !== null, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('source', 'like', $search . '%');
                    });
                })
                ->orderBy('created_at','desc')
                ->get();
            return self::successOrErrorResponse(true, 200, "Lista de movimientos", $list);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            $movements = $data['movements'];
            unset($data['movements']);
            $item = new Movement();
            $item->fill($data);
            $item->user_created_at = Auth::user()->email;
            $item->save();
            foreach ($movements as $movementDetailData) {
                $movementDetail = new MovementDetail();
                $movementDetail->fill($movementDetailData);
                $movementDetail->movement_id = $item->id;
                $movementDetail->save();
                if($movementDetail->save()){
                    $material=Material::find($movementDetail->material_id);
                    switch ($movementDetail->type) {
                        case 1:
                            $material->stock+=$movementDetail->quantity;
                            break;
                        case 0:
                            $material->stock-=$movementDetail->quantity;
                            break;
                        default:
                            break;
                    }
                    $material->save();
                }
            }
            DB::commit();
            return self::successOrErrorResponse(true, 200, "Movimiento registrado con éxito", []);
        } catch (\Exception $e) {
            DB::rollback();
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function update($data)
    {
        try {
            DB::beginTransaction();
            $movements = $data['movements'];
            unset($data['movements']);

            $item = Movement::find($data['id']);
            $item->fill($data);
            $item->user_updated_at = Auth::user()->email;
            $item->save();

            foreach ($item->movementDetails as $movementDetail) {
                $material = Material::find($movementDetail->material_id);
                $material->stock -= ($movementDetail->type == 1 ? $movementDetail->quantity : -$movementDetail->quantity);
                $material->save();
            }

            $item->movementDetails()->delete(); 

            foreach ($movements as $movementDetailData) {
                $movementDetail = new MovementDetail();
                $movementDetail->fill($movementDetailData);
                $movementDetail->movement_id = $item->id;
                $movementDetail->save();

                if ($movementDetail->save()) {
                    $material = Material::find($movementDetail->material_id);

                    $material->stock += ($movementDetail->type == 1 ? $movementDetail->quantity : -$movementDetail->quantity);
                    $material->save();
                }
            }
            DB::commit();
            return self::successOrErrorResponse(true, 200, "Movimiento actualizado con éxito", []);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return self::successOrErrorResponse(false, 401, $e->getMessage(), null);
        } catch (\Exception $e) {
            DB::rollback();
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $item = Movement::find($id);

            if (!$item) {
                DB::rollback();
                return self::successOrErrorResponse(false, 404, 'Movimiento no encontrado', []);
            }

            foreach ($item->movementDetails as $movementDetail) {
                $material = Material::find($movementDetail->material_id);
                $material->stock -= ($movementDetail->type == 1 ? $movementDetail->quantity : -$movementDetail->quantity);
                $material->save();
            }

            $item->movementDetails()->delete(); 

            $item->delete();

            DB::commit();
            return self::successOrErrorResponse(true, 200, "Movimiento eliminado con éxito", []);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return self::successOrErrorResponse(false, 404, $e->getMessage(), null);
        } catch (\Exception $e) {
            DB::rollback();
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function getItem($id)
    {
        try {
            $item = Movement::with(['movementDetails' => function ($query) {
                $query->with('material');
            }])->findOrFail($id);
            $detail = $item->movementDetails->first();
            if ($detail) {
                $item->type = $detail->type;
            }
            return self::successOrErrorResponse(true, 200, "Movimiento encontrado", $item);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Movimiento no encontrado", []);
        } catch (\Exception $e) {
            return self::successOrErrorResponse(false, 500, 'Ocurrió un error: ' . $e->getMessage(), []);
        }
    }

    public function getReport($data)
    {
        try {
            $startTimestamp = date('Y-m-d', strtotime($data['date_init']));
            $endTimestamp = date('Y-m-d', strtotime($data['date_finish'])); 

            $movements = Movement::with('movementDetails')
                ->where('status', 1)
                ->whereDate('created_at', '>=', $startTimestamp)
                ->whereDate('created_at', '<=', $endTimestamp)
                ->get();

            if ($movements->isEmpty()) {
                return self::successOrErrorResponse(false, 404, "Movimientos no encontrados", []);
            }
            // $date_init = date('d-m-Y', strtotime($data['date_init']));
            // $date_finish = date('d-m-Y', strtotime($data['date_finish']));

            // $pdf = Pdf::loadView('reports.Movements', ['movements' => $movements, 'date_init' =>$date_init, 'date_finish' =>$date_finish]);

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