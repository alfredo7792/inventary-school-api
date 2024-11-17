<?php

namespace App\Http\Controllers;

use App\Services\MaterialService;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    private $itemService;

    public function __construct(MaterialService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $response = $this->itemService->create($data);
        return $response;
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $response = $this->itemService->update($data);
        return $response;
    }

    public function delete(Request $request)
    {
        $itemId = $request->input('id');
        $response = $this->itemService->delete($itemId);
        return $response;
    }

    public function list(Request $request)
    {
        $data = $request->all();
        $response = $this->itemService->list($data);
        return $response;
    }

    public function getItem(Request $request)
    {
        $itemId = $request->input('id');
        $response = $this->itemService->getItem($itemId);
        return $response;
    }

    public function getReport(Request $request)
    {
        $itemId = $request->input('id');
        $response = $this->itemService->getReport($itemId);
        return $response;
    }
}
