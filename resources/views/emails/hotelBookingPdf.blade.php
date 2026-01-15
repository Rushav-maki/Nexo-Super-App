<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hotel Booking Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.6; }
        h1 { color: #F97316; }
        .section { margin-bottom: 20px; }
        .section p { margin: 4px 0; }
        .total { font-weight: bold; }
        .note { margin-top: 10px; font-size: 0.9rem; color: #15803d; }
    </style>
</head>
<body>
    <h1>Hotel Booking Receipt</h1>

    <div class="section">
        <p><strong>Hotel Name:</strong> {{ $booking->hotel_name }}</p>
        <p><strong>Location:</strong> {{ $booking->location }}</p>
        <p><strong>Rating:</strong> ★ {{ $booking->rating ?? '4.5' }}</p>

        @if(!empty($booking->amenities))
        <p><strong>Amenities:</strong> {{ implode(', ', $booking->amenities) }}</p>
        @endif

        <p><strong>Price Per Night:</strong> Rs. {{ $booking->price_per_night }}</p>
        <p><strong>Number of Nights:</strong> {{ $booking->nights }}</p>
        <p class="total"><strong>Total Amount:</strong> Rs. {{ $booking->total_amount }}</p>
        <p><strong>Booking Date:</strong> {{ $booking->created_at->format('d M Y, H:i') }}</p>
    </div>

    <p class="note">Your hotel booking is confirmed! Please proceed to the hotel at your check-in time.</p>
</body>
</html>
