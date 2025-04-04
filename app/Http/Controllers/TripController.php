<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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
        return Trip::all();
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
            'description' => 'required|string',
            'name' => 'required|string',
            // now we dont need user id, we need his token.
            // 'user_id' => 'required|exists:users,id',
            'end_date' => 'required|date',
            'url_photo' => 'nullable|image|mimes:jpg,jpeg,png,bmp,tiff|max:10240'
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
        $trip = $req->user()->trips()->create([
            'url_photo' => $image ? 'uploads/trips/' . $filename : null, 
            'name' => $req->input('name'),
            'destination' => $req->input('destination'),
            'description' => $req->input('description'),
            'start_date' => $req->input('start_date'),
            'end_date' => $req->input('end_date'),
        ]);
        //$trip = Trip::create($field);
        $trip->url_photo = $trip->url_photo ? asset($trip->url_photo) : null; 
        return response()->json($trip, 201);
    }

    // Check the route:api/trips/{trip}-- trips.show › TripController@show
    // and create show() to show one trip
    public function show(Trip $trip)
    {
        $trip->url_photo = $trip->url_photo ? asset($trip->url_photo) : null;
        return response()->json($trip);
    }

    // Check route: PUT|PATCH api/trips/{trip}--trips.update › TripController@update
    //the LOGIC:
    // 1. user call api xxx/api/trips/1, 
    // 2.  update() will be executed in TripController.php
    public function update(Request $req, Trip $trip)
    {
        // dd($req->all());
       
        $field = $req->validate([
            'name' => 'string',
            'destination' => 'string',
            'description' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
            'url_photo' => 'nullable|image|mimes:jpg,jpeg,png,bmp,tiff|max:10240'
        ]);
      
        //dd($req->all());

        if ($req->hasFile('url_photo')) {
            // 删除旧图片
            if ($trip->url_photo && File::exists(public_path($trip->url_photo))) {
                File::delete(public_path($trip->url_photo));
            }
    
            // 处理新上传的图片
            $image = $req->file('url_photo');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $directory = public_path('uploads/trips');
    
            // 确保目录存在
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0775, true);
            }
    
            // 保存文件
            $image->move($directory, $filename);
    
            // 更新图片路径
            $field['url_photo'] = 'uploads/trips/' . $filename;
        }
    
 
        $trip->update($field);
        $trip->url_photo = $trip->url_photo ? asset($trip->url_photo) : null;
        return response()->json($trip);
    }


    public function customPostMethod(Request $req, Trip $trip){

        $field = $req->validate([
            'name' => 'string',
            'destination' => 'string',
            'description' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
            'url_photo' => 'nullable|image|mimes:jpg,jpeg,png,bmp,tiff|max:10240'
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

    public function getUserTrips($userId)
    {
        $trips = Trip::where('user_id', $userId)->get();
      
    
        // $trip->url_photo = $trip->url_photo ? asset($trip->url_photo) : null;
        $trips = $trips->map(function ($trip) {
            $trip->url_photo = $trip->url_photo 
                ? (filter_var($trip->url_photo, FILTER_VALIDATE_URL) ? $trip->url_photo : asset($trip->url_photo)) 
                : null;
            return $trip;
        });
        return response()->json($trips);
    }
}
