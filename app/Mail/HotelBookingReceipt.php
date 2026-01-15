<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HotelBookingReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $user;

    public function __construct($user, $booking)
    {
        $this->user = $user;
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Your Hotel Booking Receipt')
                    ->markdown('emails.hotelBooking')
                    ->attachData($this->generateReceiptPdf(), 'HotelBookingReceipt.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }

    private function generateReceiptPdf()
    {
        $pdf = \PDF::loadView('emails.hotelBookingPdf', [
            'booking' => $this->booking,
            'user' => $this->user
        ]);

        return $pdf->output();
    }
}
