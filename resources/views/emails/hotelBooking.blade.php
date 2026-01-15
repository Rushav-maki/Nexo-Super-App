@component('mail::message')
# Hotel Booking Confirmation

Hi {{ $user->name }},

Your hotel booking has been successfully confirmed!

**Hotel:** {{ $booking->hotel_name }}
**Location:** {{ $booking->location }}
**Nights:** {{ $booking->nights }}
**Total Paid:** Rs. {{ $booking->total_amount }}

Your booking receipt is attached as a PDF.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
