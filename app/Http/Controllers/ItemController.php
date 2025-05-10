<?php

namespace App\Http\Controllers;

use App\Models\Backpack;
use App\Models\ItemCategory;
use App\Models\Item;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemController extends Controller implements HasMiddleware
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
        return Item::all();
    }


    public function getItemsBybackpack(Request $request, $backpack_id)
    {
        try {
            $backpack = Backpack::with('categories.items')
                ->findOrFail($backpack_id);

            return response()->json([
                'message' => 'Backpack categories and items fetched successfully.',
                'data' => [
                    'backpack_id' => $backpack->id,
                    'item_categories' => $backpack->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'description' => $category->description,
                            'items' => $category->items->map(function ($item) {
                                return [
                                    'id' => $item->id,
                                    'name' => $item->name,
                                    'description' => $item->description,
                                    'quantity' => $item->quantity,
                                ];
                            }),
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while get items by backpack´s id.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'item_category_id' => 'integer|exists:item_categories,id',
            'description' => 'nullable|string|max:255',
            'quantity' => 'nullable|int|min:1',
            'is_checked' => 'boolean',
            'backpack_id' => 'integer|exists:backpacks,id'
        ]);
        try {
            $item = Item::create($validated);
            return response()->json([
                'message' => 'Item Creado con éxito',
                'data' => $item,
                'user' => $request->user()->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear un item.',
                'error' => $e->getMessage(),
            ], 500); // Internal server error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {

        try {
            $item = Item::find($id);
            if ($item) {
                return  response()->json([
                    'message' => 'item encontrada.',
                    'data' => $item,
                    'user' => $request->user()->id
                ], 200);
            } else {
                return response()->json([
                    'message' => 'item no encontrada.',
                    'user' => $request->user()->id
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear un item.',
                'error' => $e->getMessage(),
                'user' => $request->user()->id
            ], 500); // Internal server error
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
                'item_category_id' => 'integer|exists:item_categories,id',
                'description' => 'nullable|string|max:255',
                'is_checked' => 'boolean',
                'quantity' => 'nullable|int|min:1',
            ]);
            $item = Item::find($id);
            if (!$item) {
                return response()->json([
                    'message' => "Item no encontrado"
                ], 404);
            }


            $updated = $item->update($validated);
            if ($updated) {
                return response()->json([
                    'message' => "Item actualizada correctamente.",
                    'data' => $item,
                    'user' => $request->user()->id
                ]);
            } else {
                return response()->json([
                    'message' => "Error al actualizar el item."
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al actualizar un item.',
                'error' => $e->getMessage(),
                'user' => $request->user()->id
            ], 500); // Internal server error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $item = Item::find($id);
            if (!$item) {
                return response()->json([
                    'message' => 'Item no encontrado',
                    'user' => $request->user()->id
                ], 404);
            }

            $eliminado = $item->delete();
            if (!$eliminado) {
                return response()->json([
                    'message' => "Error al eliminar el item",
                    'user' => $request->user()->id
                ], 404);
            } else {
                return response()->json([
                    'message' => "Item eliminado correctamente.",
                    'data' => $item,
                    'user' => $request->user()->id
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al eliminar un item.',
                'error' => $e->getMessage(),
                'user' => $request->user()->id
            ], 500); // Internal server error
        }
    }
}
