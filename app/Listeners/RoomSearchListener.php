<?php

namespace App\Listeners;

use App\Events\RoomSearchEvent;
use App\Http\Resources\RoomResource;
use App\Models\Hotel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RoomSearchListener
{
    /**
     * Handle the event.
     */
    public function handle(RoomSearchEvent $event): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $request = $event->request;
        $query = Hotel::with('rooms');

        if ($request->has('hotel_name')) {
            $query->where('name', 'like', '%' . $request->hotel_name . '%');
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('room_type')) {
            $query->whereHas('rooms', function ($q) use ($request) {
                $q->where('room_type', $request->room_type);
            });
        }

        if ($request->has('star_rating')) {
            $query->where('star_rating', $request->star_rating);
        }

        return RoomResource::collection($query->get());
    }
}
