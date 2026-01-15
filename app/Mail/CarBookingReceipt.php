<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CarBookingReceipt extends Mailable
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
        return $this->subject('Your Car Booking Receipt')
                    ->markdown('emails.carBooking')
                    ->attachData($this->generateReceiptPdf(), 'CarBookingReceipt.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }

    private function generateReceiptPdf()
    {
        // You can use any PDF generator, here we use barryvdh/laravel-dompdf
        $pdf = \PDF::loadView('emails.carBookingPdf', [
            'booking' => $this->booking,
            'user' => $this->user
        ]);

        return $pdf->output();
    }
}
