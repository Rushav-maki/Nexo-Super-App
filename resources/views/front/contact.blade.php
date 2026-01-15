@extends('front.layouts.app')

@section('title', 'Contact · NEXO.GLOBAL')

@section('content')

<section class="max-w-7xl mx-auto px-6 py-32">

    <div class="grid md:grid-cols-2 gap-20 items-start">

        <!-- LEFT -->
        <div class="space-y-10">
            <h2 class="text-5xl font-serif font-bold italic leading-tight">
                Initiate <br> Communication.
            </h2>

            <p class="text-lg text-[#4a4a4a] opacity-80">
                We believe conversations shape futures.
                Reach out — thoughtfully, freely, humanly.
            </p>

            <div class="space-y-6">
                <div class="flex gap-6 items-center">
                    <div class="h-12 w-12 rounded-2xl bg-white shadow flex items-center justify-center">
                        <i class="fa-solid fa-phone text-[#c4b5fd]"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest opacity-60">Phone</p>
                        <p class="font-semibold">+977 98XXXXXXXX</p>
                    </div>
                </div>

                <div class="flex gap-6 items-center">
                    <div class="h-12 w-12 rounded-2xl bg-white shadow flex items-center justify-center">
                        <i class="fa-solid fa-envelope text-[#c4b5fd]"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest opacity-60">Email</p>
                        <p class="font-semibold">hello@nexa.global</p>
                    </div>
                </div>

                <div class="flex gap-6 items-center">
                    <div class="h-12 w-12 rounded-2xl bg-white shadow flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-[#c4b5fd]"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest opacity-60">Location</p>
                        <p class="font-semibold">Kathmandu, Nepal</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT FORM -->
        <div class="bg-white p-12 rounded-[3rem] shadow-2xl">

            {{-- @if(session('success'))
                <div class="bg-green-100 text-green-800 px-6 py-4 rounded-2xl mb-6">
                    {{ session('success') }}
                </div>
            @endif --}}

            <form class="space-y-8" action="{{ route('contact.store') }}" method="POST">
                @csrf

                <div>
                    <label class="text-xs uppercase tracking-widest opacity-60">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                    @error('name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs uppercase tracking-widest opacity-60">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                    @error('email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs uppercase tracking-widest opacity-60">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                           class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                    @error('subject')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs uppercase tracking-widest opacity-60">Message</label>
                    <textarea rows="4" name="message"
                              class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none resize-none">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-[#2a1b18] text-white py-5 rounded-2xl uppercase tracking-widest text-xs font-semibold hover:bg-[#3d2b26] transition">
                    Send Message
                </button>

            </form>
        </div>

    </div>
</section>

@endsection
