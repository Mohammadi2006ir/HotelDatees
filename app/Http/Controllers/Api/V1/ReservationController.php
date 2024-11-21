<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use CanLoadRelationships;
    protected array $relations = ['room', 'user'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $query = $this->loadRelationship(Reservation::query());
        return ReservationResource::collection($query->orderBy('id', $request->query('order') ?? 'desc')->paginate($request->query('limit')));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationRequest $request): ReservationResource|\Illuminate\Http\JsonResponse
    {
        try {
            // Check for date conflicts
            $isReserved = Reservation::where('room_id', $request->room_id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                        ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date]);
                })
                ->exists();

            if ($isReserved) {
                return response()->json(['message' => 'Reservation already exists.'], 400);
            }

            // Check if the room can accommodate the family members
            $roomCapacity = Room::where('id', $request->room_id)->value('capacity');

            if ($roomCapacity < $request->user()->family_members) {
                return response()->json(['message' => 'Your family members exceed room capacity.'], 400);
            }

            // Create a new reservation
            $createReservation = Reservation::create([
                'room_id' => $request->input('room_id'),
                'user_id' => $request->user()->id,
                'check_in_date' => $request->input('check_in_date'),
                'check_out_date' => $request->input('check_out_date')
            ]);

            // Update room status to "reserved"
            $room = Room::find($request->room_id);
            if ($room) {
                $room->status = 'reserved';
                $room->save();
            }

            return new ReservationResource($this->loadRelationship($createReservation));
        } catch ( \Exception $e){
            return response()->json(['message' => 'خطا در ایجاد رزرو', 'error' => $e->getMessage(),], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reserve): ReservationResource
    {
        return new ReservationResource($this->loadRelationship($reserve));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReservationRequest $request, Reservation $reserve): ReservationResource|\Illuminate\Http\JsonResponse
    {
        try {
            if ($reserve->user_id !== $request->user()->id) {
                return response()->json(['message' => 'شما مجاز به ویرایش این رزرو نیستید.'], 403);
            }

            // بررسی عدم تداخل تاریخ‌ها
            $existingReservation = Reservation::where('room_id', $request->room_id)
                ->where('id', '!=', $reserve->id) // اطمینان از اینکه رزرو فعلی را نادیده بگیریم
                ->where(function ($query) use ($request) {
                    $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                        ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date]);
                })
                ->exists();

            if ($existingReservation) {
                return response()->json(['message' => 'این اتاق در تاریخ‌های انتخابی رزرو شده است.'], 400);
            }

            // به‌روزرسانی اطلاعات رزرو
            $reserve->update($request->all());

            // تغییر وضعیت اتاق به "رزرو شده" در صورت نیاز
            $room = Room::find($request->room_id);
            if ($room) {
                $room->status = 'reserved';
                $room->save();
            }
            return new ReservationResource($this->loadRelationship($reserve));

        } catch (\Exception $e){
            return response()->json(['message' => 'خطا در ویرایش رزرو', 'error' => $e->getMessage(),], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Reservation $reserve): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        if ($reserve->user_id !== $request->user()->id) {
            return response()->json(['message' => 'شما مجاز به حذف این رزرو نیستید.'], 403);
        }

        $reserve->delete();
        return response()->noContent();
    }
}
