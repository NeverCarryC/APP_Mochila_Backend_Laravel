<?php

namespace App\Http\Controllers;

use App\Models\Backpack;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BackpackController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['show'])
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json(['data' => Backpack::all()]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving backpacks.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getBackpacksByUser(Request $request)
    {
        // $trips = Trip::where('user_id', $userId)->get(); 
        $trips = $request->user()->trips;
        if ($trips->isEmpty()) {
            return response()->json([
                'message' => 'No trips found for this user.',
                'user' => $request->user()->id
            ], 404);
        }

        // Como SELECT * FROM backpacks WHERE trip_id IN (1, 2);
        $backpacks = Backpack::whereIn('trip_id', $trips->pluck('id'))->get();

        if ($backpacks->isEmpty()) {
            return response()->json([
                'message' => 'No backpacks found for the trips.',
                'user' => $request->user()->id
            ], 404);
        }

        return response()->json([
            'message' => 'Backpacks found.',
            'data' => $backpacks,
            'user' => $request->user()->id
        ], 200);
    }

    public function getBackpacksByTrip(Request $request, $trip_id)
    {
        try {
            $backpacks = Backpack::where('trip_id', $trip_id)->get();
            if ($backpacks->isEmpty()) {
                return response()->json([
                    'message' => 'No backpacks found for the trip.',
                ], 404);
            }
            return response()->json([
                'message' => 'Backpacks found.',
                'data' => $backpacks,
                'user' => $request->user()->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving backpacks.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->user());
        $validated = $request->validate([
            'trip_id' => 'required|integer|exists:trips,id',
            'color_id' => 'integer|exists:colors,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $backpack = Backpack::create($validated);
            return response()->json([
                'message' => 'Mochila creada con éxito.',
                'data' => $backpack,
                'user' => $request->user()->id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear la mochila.',
                'error' => $e->getMessage(),
            ], 500); // Internal server error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $backpack = Backpack::find($id);

        if ($backpack) {
            return response()->json([
                'message' => 'Mochila encontrada.',
                'data' => $backpack,

            ], 200);
        }

        return response()->json([
            'message' => 'Mochila no encontrada.',

        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'string|max:255',
                'description' => 'nullable|string|max:255',
                'trip_id' => 'exists:trips,id',
                'color_id' => 'exists:colors,id',
            ]);
            $backpack = Backpack::findOrFail($id);



            $backpack->update($validatedData);

            return response()->json([
                'message' => 'Mochila actualizada correctamente.',
                'data' => $backpack,
                'user' => $request->user()->id
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Mochila no encontrada.',

            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $backpack = Backpack::findOrFail($id);

            $backpack->delete();

            return response()->json([
                'message' => 'Mochila eliminada correctamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Mochila no encontrada.',
            ], 404);
        }
    }
}
