<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Backpack;
use App\Models\TemplateBackpack;
use App\Models\TripCategory;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class TripController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['show'])
        ];
    }

    public function index()
    {
        return response()->json([
            'message' => "all trips found",
            'data' => Trip::all()
        ]);
    }

    // Because We executed the command php artisan install:api, The /routes/api.php file will be automatically created.
    // Because we write "Route::apiResource('trips', TripController::class);" in /routes/api.php,
    // this line help us create trips´s api routes

    // Check the route:list(use php artisan route:list), we can see:   api/trips ...trips.index › TripController@index
    // this means when we call the api： GET https://uemproyecto.site/api/trips, will execute the index() in TripControlller
    // so the returned variable is the response of the API call.
    public function store(Request $req)
    {

        $field = $req->validate([
            'start_date' => 'required|date',
            'destination' => 'required|string',
            'description' => 'nullable|string',  //se ha cambiado por required.
            'name' => 'required|string',
            // now we dont need user id, we need his token.
            // 'user_id' => 'required|exists:users,id',
            'end_date' => 'required|date',
            'url_photo' => 'nullable|image|mimes:jpg,jpeg,png,bmp,tiff|max:10240',
            'use_suggestions' => 'nullable|boolean',
            'temperature' => 'required',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:trip_categories,id'
        ]);


        $image = null;
        if ($req->hasFile('url_photo')) {
            $image = $req->file('url_photo');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Crear la carpeta de trips images 
            $directory = public_path('uploads/trips');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0775, true);
            }

            // Guandar en la carpeta
            $image->move($directory, $filename);
        }


        // BE CAREFUL ABOUT  $req->user()
        //Sanctum validates the Token: When the request reaches your Laravel back-end, 
        //Sanctum will automatically validate the token and associate it with the user who owns that token.
        // dd($req->user());
        $firstCategoryName = null;
        foreach ($req->categories as $category) {
            $firstCategoryName = TripCategory::find($category);
            $firstCategoryName = $firstCategoryName->name;
            break;
        }

        $trip = $req->user()->trips()->create([
            'name' => $req->input('name'),
            'destination' => $req->input('destination'),
            'description' => $req->input('description'),
            'start_date' => $req->input('start_date'),
            'end_date' => $req->input('end_date'),
            'temperature' => $req->input('temperature'),
            'url_photo' => "https://uemproyecto.site/images/".$firstCategoryName . ".jpg",
        ]);
        //$trip = Trip::create($field);
        if ($req->boolean('use_suggestions')) {
            $template = TemplateBackpack::with('items')
                ->where('name', $trip->temperature)
                ->first();

            $backpack = $trip->backpacks()->create([
                'name' => $trip->name,
                'url_photo' => $trip->url_photo,
            ]);

            if ($template) {
                $category = $backpack->categories()->create(
                    ['name' => $template->name],
                    [
                        'description' => 'Categoría creada desde el template ' . $template->name,
                        'user_id' => Auth::id(),
                    ]
                );


                foreach ($template->items as $templateItem) {
                    $category->items()->create([
                        'name' => $templateItem->name,
                        'quantity' => $templateItem->quantity,
                        'user_id' => Auth::id(),
                    ]);
                }
            }
            
            foreach ($req->categories as $category) {
                $category = TripCategory::find($category);
                $template = TemplateBackpack::with('items')
                    ->where('name', $category->name)
                    ->first();

                    // dd ($template);


                if ($template) {
                    $category = $backpack->categories()->create(
                        ['name' => $template->name],
                        [
                            'description' => 'Categoría creada desde el template ' . $template->name,
                            'user_id' => Auth::id(),
                        ]
                    );

                    foreach ($template->items as $templateItem) {
                        $category->items()->create([
                            'name' => $templateItem->name,
                            'quantity' => $templateItem->quantity,
                            'user_id' => Auth::id(),
                        ]);
                    }
                }
            }
        } else {
            $trip->backpacks()->create([
                'name' => $trip->name . "Error",
            ]);
        }
        

        // Si pasa la lista de trip_categories, asignar el viaje a las categorias
        if ($req->has('categories')) {
            // dd($req->categories);
            $trip->categories()->syncWithoutDetaching($req->categories);
        }

        return response()->json(['data' => $trip, "categoria" => $req->categories], 201);
        //return response()->json($trip, 201);
    }



    // Check the route:api/trips/{trip}-- trips.show › TripController@show
    // and create show() to show one trip
    public function show(String $trip_id)
    {
        try {
            $trip = Trip::findOrFail($trip_id);
            $trip->url_photo = $trip->url_photo ? asset($trip->url_photo) : null;
            return response()->json(['data' => $trip]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while get trip.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Check route: PUT|PATCH api/trips/{trip}--trips.update › TripController@update
    //the LOGIC:
    // 1. user call api xxx/api/trips/1, 
    // 2.  update() will be executed in TripController.php
    public function update(Request $req, Trip $trip)
    {
        try {
            $field = $req->validate([
                'name' => 'string',
                'destination' => 'string',
                'description' => 'string',
                'start_date' => 'date',
                'end_date' => 'date',
                'url_photo' => 'nullable|image|mimes:jpg,jpeg,png,bmp,tiff|max:10240',
                'category_ids' => 'array',
                'category_ids.*' => 'integer|exists:trip_categories,id'
            ]);

            if ($req->hasFile('url_photo')) {
                // Borrar imágenes antiguas
                if ($trip->url_photo && File::exists(public_path($trip->url_photo))) {
                    File::delete(public_path($trip->url_photo));
                }

                // Procesamiento de imágenes cargadas
                $image = $req->file('url_photo');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $directory = public_path('uploads/trips');

                // Asegúrese de que la directoria existe
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0775, true);
                }

                // Guardar imagenes 
                $image->move($directory, $filename);

                // Actualizar ruta de imagen
                $field['url_photo'] = 'uploads/trips/' . $filename;
            }

            $trip->update($field);
            $trip->url_photo = $trip->url_photo ? asset($trip->url_photo) : null;
            if ($req->has('categories')) {

                // Este método añade las nuevas categorías sin eliminar las antiguas.
                //$trip->categories()->syncWithoutDetaching($req->categories);

                // Solo mantiene los IDs que le pases y elimina los demás.
                $trip->categories()->sync($req->categories);
            }
            return response()->json([
                'message' => 'success',
                'data' => $trip
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while update trip.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Actualizar trip 
    public function customPostMethod(Request $req, Trip $trip)
    {

        $field = $req->validate([
            'name' => 'string',
            'destination' => 'string',
            'description' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
            'url_photo' => 'nullable|image|mimes:jpg,jpeg,png,bmp,tiff|max:10240',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:trip_categories,id'
        ]);


        if ($req->hasFile('url_photo')) {
            // Eliminar la imagen vieja
            if ($trip->url_photo && File::exists(public_path($trip->url_photo))) {
                File::delete(public_path($trip->url_photo));
            }


            $image = $req->file('url_photo');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $directory = public_path('uploads/trips');


            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0775, true);
            }


            $image->move($directory, $filename);


            $field['url_photo'] = 'uploads/trips/' . $filename;
        }


        $trip->update($field);
        if ($req->has('categories')) {

            // Este método añade las nuevas categorías sin eliminar las antiguas.
            //$trip->categories()->syncWithoutDetaching($req->categories);

            // Solo mantiene los IDs que le pases y elimina los demás.
            $trip->categories()->sync($req->categories);
        }
        $trip->url_photo = $trip->url_photo ? asset($trip->url_photo) : null;
        return response()->json($trip);
    }
    // lets create a function to delete a trip
    // The same way to check route, method name and controller name, then write methods codes

    // BUT WE CAN RUN A COMMAND(i forgot) IN TERMINAL TO CREATE THE BASIC ESTRUCTURA OF TRIPCONTROLLER
    // THE ONLY THING WE DO : Define the route, create estructura of controller by command, write logic in controller´s method


    public function destroy(Trip $trip)
    {
        if ($trip->url_photo && File::exists(public_path($trip->url_photo))) {
            File::delete(public_path($trip->url_photo));
        }
        $trip->delete();
        return response()->json(['message' => 'Delete successful'], 204);
    }

    public function getUserTrips(Request $request)
    {

        $trips = $request->user()->trips()->get();
        // $trip->url_photo = $trip->url_photo ? asset($trip->url_photo) : null;
        $trips = $trips->map(function ($trip) {
            $trip->url_photo = $trip->url_photo
                ? (filter_var($trip->url_photo, FILTER_VALIDATE_URL) ? $trip->url_photo : asset($trip->url_photo))
                : null;
            return $trip;
        });
        return response()->json(['data' => $trips, 'message' => 'success']);
    }


    public function getTripsGroupedByCategory(Request $request)
    {
        try {
            $user = $request->user();

            // Condición de filtro opcional
            $categoryId = $request->query('category_id');
            $perPage = $request->query('per_page', 10);

            // Obtener los viajes del usuario actual, incluyendo las categorías
            $tripsQuery = $user->trips()->with('categories');

            // Filtrar los viajes según el category_id
            if ($categoryId) {
                $tripsQuery->whereHas('categories', function ($query) use ($categoryId) {
                    $query->where('trip_categories.id', $categoryId); // Asegurarse de que solo se devuelvan los viajes de la categoría con el ID proporcionado
                });
            }

            // Obtener los viajes y paginarlos
            $trips = $tripsQuery->paginate($perPage);

            // Agrupar los viajes por nombre de categoría
            $grouped = [];
            if ($categoryId) {
                // Recorrer los viajes y agruparlos según las categorías
                foreach ($trips as $trip) {
                    // Solo procesar las categorías relacionadas con la categoría objetivo
                    foreach ($trip->categories as $category) {

                        // Asegurarse de que solo los viajes relacionados con la categoría objetivo se agreguen al grupo
                        if ($category->id == $categoryId) {
                            $grouped[$category->name][] = [
                                'trip id' => $trip->id,
                                'name' => $trip->name,
                                'user_id' => $trip->user_id,
                                'description' => $trip->description,
                                'temperature' => $trip->temperature,
                                'start_date' => $trip->start_date,
                                'end_date' => $trip->end_date,
                                'url_photo' => $trip->url_photo
                            ];
                        }
                    }
                }
            } else {
                // Recorrer los viajes y agruparlos según las categorías
                foreach ($trips as $trip) {
                    // Solo procesar las categorías relacionadas con el viaje
                    foreach ($trip->categories as $category) {

                        // Agregar los viajes al grupo según la categoría
                        $grouped[$category->name][] = [
                            'trip id' => $trip->id,
                            'name' => $trip->name,
                            'user_id' => $trip->user_id,
                            'description' => $trip->description,
                            'temperature' => $trip->temperature,
                            'start_date' => $trip->start_date,
                            'end_date' => $trip->end_date,
                            'url_photo' => $trip->url_photo
                        ];
                    }
                }
            }

            return response()->json([
                'data' => $grouped,
                'pagination' => [
                    'total' => $trips->total(),
                    'current_page' => $trips->currentPage(),
                    'per_page' => $trips->perPage(),
                    'last_page' => $trips->lastPage(),
                    'next_page_url' => $trips->nextPageUrl(),
                    'prev_page_url' => $trips->previousPageUrl(),
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los viajes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
