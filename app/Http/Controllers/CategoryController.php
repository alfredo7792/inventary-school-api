<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $itemService;

    public function __construct(CategoryService $itemService)
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
}
