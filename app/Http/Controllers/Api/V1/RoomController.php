<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\RoomSearchEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use CanLoadRelationships;
    protected array $relations = ['hotel', 'reservations'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $query = $this->loadRelationship(Room::query());
        return RoomResource::collection($query->orderBy('id', $request->query('order') ?? 'desc')->paginate($request->query('limit')));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomRequest $request): \Illuminate\Http\JsonResponse|RoomResource
    {
        try {
            $createRoom = Room::create($request->all());
            return new RoomResource($this->loadRelationship($createRoom));
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در ایجاد اتاق', 'error' => $e->getMessage(),], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room): \Illuminate\Http\JsonResponse|RoomResource
    {
        return new RoomResource($this->loadRelationship($room));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomRequest $request, Room $room): \Illuminate\Http\JsonResponse|RoomResource
    {
        try {
            $room->update($request->all());
            return new RoomResource($this->loadRelationship($room));
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در ویرایش اتاق', 'error' => $e->getMessage(),], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room): \Illuminate\Http\Response
    {
        $room->delete();
        return response()->noContent();
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = event(new RoomSearchEvent($request));
        return response()->json($result[0]);
    }
}
