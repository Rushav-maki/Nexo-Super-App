<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAIService;
use App\Models\Car;
use App\Models\CarBooking;
use App\Models\HotelBooking;
use App\Models\NexoPaisaTransaction;
use App\Mail\CarBookingReceipt;
use App\Mail\HotelBookingReceipt;
use Illuminate\Support\Facades\Mail;


class TravelController extends Controller
{
    public function index()
    {
        return view('user.travel');
    }

    public function plan(Request $request)
    {
        $validated = $request->validate([
            'destination' => 'required|string|min:2',
            'budget' => 'required|string|in:Affordable,Standard,Premium',
            'days' => 'required|integer|min:1|max:365',
        ]);

        try {
            $data = OpenAIService::generateItinerary(
                $validated['destination'],
                $validated['budget'],
                $validated['days']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function hotels(Request $request)
    {
        $validated = $request->validate([
            'destination' => 'required|string|min:2',
        ]);

        try {
            $data = OpenAIService::searchHotels($validated['destination']);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cars(Request $request)
    {
        try {
            // Return all available cars from database
            $cars = Car::all()->map(function ($car) {
                return [
                    'id' => $car->id,
                    'name' => $car->name,
                    'type' => $car->type,
                    'pricePerDay' => $car->price_per_day,
                    'transmission' => $car->transmission,
                    'seatingCapacity' => $car->seating_capacity,
                    'fuelType' => $car->fuel_type,
                    'image_url' => $car->image_url,

                ];
            })->toArray();

            return response()->json($cars);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function carBooking()
    {
        return view('user.car-booking');
    }

    public function carPayment(Request $request)
    {
        $validated = $request->validate([
            'carId' => 'required|exists:cars,id',
            'days' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();

        if ($user->nexo_paisa < $validated['total']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient Nexo Paisa. Please load more paisa to proceed.'
            ], 400);
        }

        $car = Car::findOrFail($validated['carId']);

        // Create booking record
        $carBooking = CarBooking::create([
            'user_id' => $user->id,
            'car_id' => $validated['carId'],
            'days' => $validated['days'],
            'price_per_day' => $car->price_per_day,
            'total_amount' => $validated['total'],
            'car_details' => [
                'name' => $car->name,
                'type' => $car->type,
                'transmission' => $car->transmission,
                'seating_capacity' => $car->seating_capacity,
                'fuel_type' => $car->fuel_type,
                'image_url' => $car->image_url,
            ],
        ]);

        // Deduct nexo_paisa
        $user->decrement('nexo_paisa', $validated['total']);

        // Record transaction
        NexoPaisaTransaction::create([
            'user_id' => $user->id,
            'type' => 'spend',
            'amount' => $validated['total'],
            'description' => "Car booking payment - {$car->name}",
            'metadata' => [
                'booking_type' => 'car',
                'car_id' => $car->id,
                'days' => $validated['days'],
                'car_name' => $car->name
            ]
        ]);

        // Send email with receipt PDF
        try {
            Mail::to($user->email)->send(new CarBookingReceipt($user, $carBooking));
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::error("Failed to send car booking email: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Payment recorded and receipt emailed successfully'
        ]);
    }

    public function hotelBooking()
    {
        return view('user.hotel-booking');
    }

    public function hotelPayment(Request $request)
    {
        $validated = $request->validate([
            'hotelName' => 'required|string',
            'location' => 'required|string',
            'nights' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();

        if ($user->nexo_paisa < $validated['total']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient Nexo Paisa. Please load more paisa to proceed.'
            ], 400);
        }

        // Get hotel data from request body
        $hotelData = $request->input('hotelData', []);

        // Calculate price per night
        $pricePerNight = isset($hotelData['pricePerNight'])
            ? (float) $hotelData['pricePerNight']
            : ((float) $validated['total'] / (int) $validated['nights']);

        // Ensure amenities is an array
        $amenities = isset($hotelData['amenities']) && is_array($hotelData['amenities'])
            ? $hotelData['amenities']
            : (isset($hotelData['amenities']) ? [$hotelData['amenities']] : []);

        try {
            // Create hotel booking
            $hotelBooking = HotelBooking::create([
                'user_id' => $user->id,
                'hotel_name' => $validated['hotelName'],
                'location' => $validated['location'],
                'nights' => $validated['nights'],
                'price_per_night' => $pricePerNight,
                'total_amount' => (float) $validated['total'],
                'rating' => $hotelData['rating'] ?? null,
                'amenities' => $amenities,
                'image_url' => $hotelData['image'] ?? null,
                'hotel_details' => $hotelData,
            ]);

            // Deduct nexo_paisa
            $user->decrement('nexo_paisa', $validated['total']);

            // Record transaction
            NexoPaisaTransaction::create([
                'user_id' => $user->id,
                'type' => 'spend',
                'amount' => (float) $validated['total'],
                'description' => "Hotel booking payment - {$validated['hotelName']}",
                'metadata' => [
                    'booking_type' => 'hotel',
                    'hotel_name' => $validated['hotelName'],
                    'location' => $validated['location'],
                    'nights' => $validated['nights']
                ]
            ]);

            // Send email with receipt PDF
            try {
                Mail::to($user->email)->send(new HotelBookingReceipt($user, $hotelBooking));
            } catch (\Exception $e) {
                \Log::error("Failed to send hotel booking email: " . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment recorded and receipt emailed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save booking: ' . $e->getMessage()
            ], 500);
        }
    }
}
