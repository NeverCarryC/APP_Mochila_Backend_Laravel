<?php

namespace App\Http\Controllers;

use App\Models\TripCategory;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TripCategoryController extends Controller implements HasMiddleware
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
        $basePath = public_path('images');
        $baseUrl = url('/images');
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
     /**
     * No usamos solo ->map() porque incluiría TODAS las categorías,
     * incluso aquellas que no tienen imagen en el servidor.
     *
     * En lugar de eso:
     * - Usamos ->filter() primero para excluir categorías que no tengan imagen física.
     * - Luego ->map() para formatear las categorías válidas.
     * - Finalmente ->values() para resetear los índices (en caso de que se eliminen algunas).
     *
     * Esto asegura que solo se devuelvan categorías que tienen imagen y se pueden mostrar en Flutter.
     */
    
        $categories = TripCategory::all()->filter(function ($category) use ($basePath, $extensions) {
            // Solo mantenemos las categorías que tengan una imagen real asociada por su nombre y extensión
            foreach ($extensions as $ext) {
                $filePath = $basePath . '/' . $category->name . '.' . $ext;
                if (file_exists($filePath)) {
                    return true;
                }
            }
            return false; // Excluir si no tiene imagen
        })->map(function ($category) use ($basePath, $baseUrl, $extensions) {
            $imageUrl = null;
            foreach ($extensions as $ext) {
                $filePath = $basePath . '/' . $category->name . '.' . $ext;
                if (file_exists($filePath)) {
                    $imageUrl = $baseUrl . '/' . $category->name . '.' . $ext;
                    break;
                }
            }
    
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'image_url' => $imageUrl,
            ];
        })->values(); // resetear índices
    
        return response()->json([
            'message' => "success",
            'data' => $categories
        ]);
    }

    public function getTripCategoriesByUser(Request $request)
    {
        return response()->json([
            'message' => "suceess",
            "data" => $request->user()->tripCategories()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //  protected $fillable = ['name', 'description'];
        try {
            $validated = $request->validate([
                "name" => "required|string|max:50",
                "description" => "nullable|string|max:100"
            ]);
            $trip_category = TripCategory::create($validated);
            return response()->json([
                'message' => "Trip category creado con éxito",
                'data' => $trip_category,
                'user' => $request->user()->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while create trip category.',
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
            $trip_category = TripCategory::find($id);
            if (!$trip_category) {
                return response()->json([
                    'message' => "trip category not found",
                    'user' => $request->user()->id
                ], 404);
            } else {
                return response()->json([
                    'message' => 'Trip category encontrado',
                    'data' => $trip_category,
                    'user' => $request->user()->id
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while show trip category.',
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
                'description' => 'string|max:100'
            ]);

            $trip_category = TripCategory::find($id);
            if (!$trip_category) {
                return response()->json([
                    'message' => "trip category not found",
                    'user' => $request->user()->id
                ], 404);
            } else {
                $updated = $trip_category->update($validated);
                if (!$updated) {
                    return response()->json([
                        'message' => "Error al actualizar el trip category."
                    ], 400);
                } else {
                    return response()->json([
                        'message' => 'Trip category actualizada correctamente.',
                        'data' => $trip_category,
                        'user' => $request->user()->id
                    ], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while update trip category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $trip_category = TripCategory::find($id);
            if (!$trip_category) {
                return response()->json([
                    'message' => "trip_category not found",
                    'user' => $request->user()->id
                ], 404);
            }
            $eliminado = $trip_category->delete();
            if ($eliminado) {
                return response()->json([
                    'message' => 'trip_category eliminado con éxito',
                    'data' => $trip_category,
                    'user' => $request->user()->id
                ], 200);
            } else {
                return response()->json([
                    'message' => "Error al eliminar el trip_category",
                    'user' => $request->user()->id
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while update trip_category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Asignar categorias al trip
    public function attachCategories(Request $request, String $trip_id)
    {
        try {
            $trip = Trip::findOrFail($trip_id);
            $request->validate([
                'category_ids' => 'required|array',
                'category_ids.*' => 'integer|exists:trip_categories,id'
            ]);
            $trip->categories()->syncWithoutDetaching($request->category_ids);
            return response()->json(['message' => 'attached categories correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Trip no found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while attach trip_category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function detachCategories(Request $request, String $trip_id)
    {
        try {
            $trip = Trip::findOrFail($trip_id);

            $request->validate([
                'category_ids' => 'required|array',
                'category_ids.*' => 'integer|exists:trip_categories,id'
            ]);

            $trip->categories()->detach($request->category_ids);

            return response()->json(['message' => 'Categorías desvinculadas correctamente ✅'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No se encontró el viaje especificado.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al desvincular las categorías.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
