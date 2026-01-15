<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Mail\ContactFormSubmitted;
use Illuminate\Support\Facades\Mail;

class FrontController extends Controller
{
    public function index()
    {
        return view('front.index');
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function store_contact(Request $request)
    {
        // Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            // Save in DB
            $contact = Contact::create($request->only('name', 'email', 'subject', 'message'));

            // Send email to admin
            Mail::to('panerurushav@gmail.com')->send(new ContactFormSubmitted($contact));

            return back()->with('success', 'Thank you! Your message has been sent.');
        } catch (\Exception $e) {
            return back()->with('error', 'Oops! Something went wrong. Please try again later.');
        }
    }

    public function about()
    {
        return view('front.about');
    }
}
