@component('mail::message')
# Car Booking Confirmation

Hi {{ $user->name }},

Your car booking has been successfully confirmed!

**Car:** {{ $booking['car_details']['name'] }}
**Type:** {{ $booking['car_details']['type'] }}
**Days:** {{ $booking['days'] }}
**Total Paid:** Rs. {{ $booking['total_amount'] }}

You can find your booking receipt attached as a PDF.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
