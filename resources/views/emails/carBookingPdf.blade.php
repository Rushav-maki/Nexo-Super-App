<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Car Booking Receipt</title>
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
    <h1>Car Booking Receipt</h1>

    <div class="section">
        <p><strong>Car Name:</strong> {{ $booking->car_details['name'] }}</p>
        <p><strong>Type:</strong> {{ $booking->car_details['type'] }}</p>
        <p><strong>Transmission:</strong> {{ $booking->car_details['transmission'] }}</p>
        <p><strong>Seating Capacity:</strong> {{ $booking->car_details['seating_capacity'] }}</p>
        <p><strong>Fuel Type:</strong> {{ $booking->car_details['fuel_type'] }}</p>
        <p><strong>Price Per Day:</strong> Rs. {{ $booking->price_per_day }}</p>
        <p><strong>Number of Days:</strong> {{ $booking->days }}</p>
        <p class="total"><strong>Total Amount:</strong> Rs. {{ $booking->total_amount }}</p>
        <p><strong>Payment Date:</strong> {{ $booking->created_at->format('d M Y, H:i') }}</p>
    </div>

    <p class="note">You can visit a nearby Nexo showroom to receive your rented car.</p>
</body>
</html>
