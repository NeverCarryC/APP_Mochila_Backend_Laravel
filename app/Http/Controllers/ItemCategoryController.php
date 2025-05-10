<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum')
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ItemCategory::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                "name" => "required|string|max:50",
                "description" => "nullable|string|max:100",
                "backpack_id" => "required|exists:backpacks,id"
            ]);

            $item_category =  $request->user()->itemCategories()->create($validated);
            // $item_category = ItemCategory::create($validated);
            return response()->json([
                'message' => "Item category creado con éxito.",
                'data' => $item_category,
                'user' => $request->user()->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while create item category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $item_category = ItemCategory::find($id);
            if (!$item_category) {
                return response()->json([
                    'message' => "item category not found",
                    'user' => $request->user()->id
                ], 404);
            } else {
                return response()->json([
                    'message' => 'Item category encontrado',
                    'data' => $item_category,
                    'user' => $request->user()->id
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while update item category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'string|max:50',
                'description' => 'string|max:100',
                "backpack_id" => "required|exists:backpacks,id"
            ]);

            $item_category = ItemCategory::find($id);
            if (!$item_category) {
                return response()->json([
                    'message' => "item category not found",
                    'user' => $request->user()->id
                ], 404);
            } else {
                $updated = $item_category->update($validated);
                if (!$updated) {
                    return response()->json([
                        'message' => "Error al actualizar el item category."
                    ], 400);
                } else {
                    return response()->json([
                        'message' => 'Item category actualizada correctamente.',
                        'data' => $item_category,
                        'user' => $request->user()->id
                    ], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while update item category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {

        // Si se elimina una categoría, sus items asociados también serán eliminados.
        try {
            $item_category = ItemCategory::find($id);
            if (!$item_category) {
                return response()->json([
                    'message' => "item category not found",
                    'user' => $request->user()->id
                ], 404);
            }
            $eliminado = $item_category->delete();
            if ($eliminado) {
                return response()->json([
                    'message' => 'item category eliminado con éxito',
                    'data' => $item_category,
                    'user' => $request->user()->id
                ], 200);
            } else {
                return response()->json([
                    'message' => "Error al eliminar el Item category",
                    'user' => $request->user()->id
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while update item category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
