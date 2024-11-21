<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelRequest;
use App\Http\Resources\HotelResource;
use App\Http\Traits\CanLoadRelationships;
use App\Mail\ReserveRememberMail;
use App\Models\Hotel;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HotelController extends Controller
{
    use CanLoadRelationships;
    protected array $relations = ['rooms'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        ##### we can load optional relationship with it ----> ?include=relation_one,relation_two, .....
        $query = $this->loadRelationship(Hotel::query());
        return HotelResource::collection($query->orderBy('id', $request->query('order') ?? 'desc')->paginate($request->query('limit')));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HotelRequest $request): HotelResource|\Illuminate\Http\JsonResponse
    {
        try {
            $createHotel = Hotel::create($request->all());
            // ایندکس کردن هتل جدید
            /* $client = ClientBuilder::create()->build();
            $client->index([
                'index' => 'hotels',
                'id'    => $createHotel->id,
                'body'  => $createHotel->toArray(),
            ]); */

            return new HotelResource($this->loadRelationship($createHotel));
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در ایجاد هتل', 'error' => $e->getMessage(),], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel): HotelResource
    {
        return new HotelResource($this->loadRelationship($hotel));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HotelRequest $request, Hotel $hotel): HotelResource|\Illuminate\Http\JsonResponse
    {
        try {
            $hotel->update($request->all());
            return new HotelResource($this->loadRelationship($hotel));
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در ویرایش هتل', 'error' => $e->getMessage(),], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel): \Illuminate\Http\Response
    {
        $hotel->delete();
        return response()->noContent();
    }
}
