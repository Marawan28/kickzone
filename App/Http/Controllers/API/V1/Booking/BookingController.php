<?php

// ============================================================
// FILE: app/Http/Controllers/API/V1/Booking/BookingController.php
// ============================================================
namespace App\Http\Controllers\API\V1\Booking;

use App\DTOs\Booking\CreateBookingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Resources\Booking\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Booking", description="Field booking management")
 */
class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/bookings",
     *     tags={"Booking"},
     *     summary="Create a new booking (concurrency-safe)",
     *     security={{"sanctum":{}}},
     * )
     */
    public function store(CreateBookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->createBooking(
            CreateBookingDTO::fromArray($request->validated(), $request->user()->id)
        );

        return response()->json([
            'message' => 'Booking confirmed!',
            'data'    => new BookingResource($booking->load(['slot.field', 'payment'])),
        ], 201);
    }

    /**
     * @OA\Get(path="/api/v1/bookings", tags={"Booking"}, security={{"sanctum":{}}})
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = $this->bookingService->getUserBookings($request->user()->id);
        return response()->json(['data' => BookingResource::collection($bookings)]);
    }

    /**
     * @OA\Get(path="/api/v1/bookings/{id}", tags={"Booking"}, security={{"sanctum":{}}})
     */
   public function show($id): JsonResponse
{
    // 1. محاولة جلب الحجز
    $booking = $this->bookingService->bookingRepo->findById($id);

    // 2. فحص: هل الحجز موجود فعلاً قبل تحويله لـ Resource؟
    if (!$booking) {
        return response()->json([
            'status' => false,
            'message' => 'الحجز ده مش موجود يا هندسة، اتأكد من الـ ID'
        ], 404);
    }

    // 3. لو موجود، ابعته للـ Resource وأنت مطمن
    return response()->json([
        'status' => true,
        'data' => new BookingResource($booking)
    ]);
}

    /**
     * @OA\Delete(path="/api/v1/bookings/{id}", tags={"Booking"}, security={{"sanctum":{}}})
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $this->bookingService->cancelBooking($id, $request->user()->id);
        return response()->json(['message' => 'Booking cancelled and wallet refunded.']);
    }
}

// ============================================================