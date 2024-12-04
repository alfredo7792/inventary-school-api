<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\Movement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getData(Request $data) {
        try {
            $users = User::where('status', 1);
            $movements = Movement::where('status', 1);
            $materials = Material::where('status', 1);
            $categories = Category::where('status', 1);
        
            switch ($data['type']) {
                case 'day':
                    $users->whereDate('created_at', Carbon::today());
                    $movements->whereDate('created_at', Carbon::today());
                    $materials->whereDate('created_at', Carbon::today());
                    $categories->whereDate('created_at', Carbon::today());
                    break;
                case 'month':
                    $users->whereMonth('created_at', Carbon::now()->month);
                    $movements->whereMonth('created_at', Carbon::now()->month);
                    $materials->whereMonth('created_at', Carbon::now()->month);
                    $categories->whereMonth('created_at', Carbon::now()->month);
                    break;
                case 'year':
                    $users->whereYear('created_at', Carbon::now()->year);
                    $movements->whereYear('created_at', Carbon::now()->year);
                    $materials->whereYear('created_at', Carbon::now()->year);
                    $categories->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'all':
                    break;
                default:
                    return response()->json(['error' => 'Invalid filter type'], 400);
            }
        
            $data = [
                'users' => $users->count(),
                'movements' => $movements->count(),
                'materials' => $materials->count(),
                'categories' => $categories->count(),
            ];
            return self::successOrErrorResponse(true, 200, "Datos obtenidos con éxito", $data);
        } catch (ModelNotFoundException $e) {
            return self::successOrErrorResponse(false, 404, "Datos no encontrados", []);
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
