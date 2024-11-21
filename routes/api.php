<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\HotelController;
use App\Http\Controllers\Api\V1\ReservationController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// auth-route ------------------------------------->>
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::middleware(['cors', 'auth:sanctum'])->prefix('v1')->group(function ($router) {
    // hotels-route ------------------------------------->>
    $router->get('hotels', [HotelController::class, 'index']);
    $router->get('hotels/{hotel}', [HotelController::class, 'show']);
    $router->post('hotels', [HotelController::class, 'store'])->middleware(['permissions:create-hotel']);
    $router->put('hotels/{hotel}', [HotelController::class, 'update'])->middleware(['permissions:update-hotel']);
    $router->delete('hotels/{hotel}', [HotelController::class, 'destroy'])->middleware(['permissions:delete-hotel']);
    $router->get('/search-hotels', function (Request $request) {
        $params = $request->only(['name', 'city', 'star_rating']);
        $results = Hotel::searchHotels($params);

        return response()->json($results);
    });

    // reservations-route ------------------------------------->>
    $router->get('reserves', [ReservationController::class, 'index'])->middleware(['permissions:reserves']);
    $router->get('reserves/{reserve}', [ReservationController::class, 'show'])->middleware(['permissions:reserve']);
    $router->post('reserves', [ReservationController::class, 'store']);
    $router->put('reserves/{reserve}', [ReservationController::class, 'update']);
    $router->delete('reserves/{reserve}', [ReservationController::class, 'destroy']);

    // rooms-route ------------------------------------->>
    $router->get('rooms', [RoomController::class, 'index']);
    $router->get('rooms/{room}', [RoomController::class, 'show']);
    $router->post('rooms', [RoomController::class, 'store'])->middleware(['permissions:create-room']);
    $router->put('rooms/{room}', [RoomController::class, 'update'])->middleware(['permissions:update-room']);
    $router->delete('rooms/{room}', [RoomController::class, 'destroy'])->middleware(['permissions:delete-room']);
    $router->get('search-rooms', [RoomController::class, 'search']);
});

