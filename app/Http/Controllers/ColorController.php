<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use SebastianBergmann\CodeCoverage\Report\Html\Colors;

class ColorController extends Controller implements HasMiddleware
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
        return Color::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20',
            'hexa' => 'required|string|max:7'
        ]);

        try {
            $color = Color::create($validated);
            return response()->json([
                'message' => 'Color creado con éxito',
                'data' => $color,
                'user' => $request->user()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Hubo un error al crear el color",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $color = Color::find($id);
            if ($color) {
                return response()->json([
                    'message' => 'Color encontrado',
                    "data" => $color,
                    'user' => $request->user()
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Color no encontrado'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Hubo un error al show el color",
                'error' => $e->getMessage()
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
                'name' => 'string|max:20',
                'hexa' => 'string|max:7'
            ]);

            $color = Color::find($id);
            if (!$color) {
                return response()->json([
                    'message' => 'Color no encontrado',
                ], 404);
            }

            $updated = $color->update($validated);
            if (!$updated) {
                return response()->json([
                    'message' => "Error al actualizar el color.",
                ], 400);
            }

            return response()->json([
                'message' => 'Color actualizada correctamente',
                'data' => $color,
                'user' => $request->user()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Hubo un error al actualizar el color",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // Si se elimina un color, sus backpacks asociados también serán eliminados.
    public function destroy(Request $request, string $id)
    {
        try {
            $color = Color::find($id);
            if (!$color) {
                return response()->json([
                    'message' => "Color no encontrado"
                ], 404);
            }

            $eliminado = $color->delete();
            if (!$eliminado) {
                return response()->json([
                    'message' => "Error al eliminar el color"
                ], 404);
            }

            return response()->json([
                'message' => 'Color eliminado correctamente',
                'data' => $color,
                'user' => $request->user()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al eliminar un color.',
                'error' => $e->getMessage(),
                'user' => $request->user()->id
            ], 500); // Internal server error
        }
    }
}
