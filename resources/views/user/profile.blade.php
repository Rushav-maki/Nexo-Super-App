@extends('layouts.app')

@section('content')
    <div class="space-y-8 pb-20">
        <!-- Header -->
        <div class="animate-slide-up">
            <h2 class="text-4xl font-bold text-stone-900 font-serif tracking-tighter uppercase italic">
                My Profile
            </h2>
            <p class="text-stone-500 mt-2">Manage your account and view your bookings</p>
        </div>

        <!-- User Information Card -->
        <div class="bg-white border border-black/10 rounded-[3rem] shadow-xl p-10 animate-fade-in">
            <h3 class="text-2xl font-bold text-stone-900 mb-6 font-serif italic">Personal Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="animate-fade-in">
                    <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2 block">Name</label>
                    <p class="text-lg font-bold text-stone-900">{{ $user->name }}</p>
                </div>
                <div class="animate-fade-in">
                    <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2 block">Email</label>
                    <p class="text-lg font-bold text-stone-900">{{ $user->email }}</p>
                </div>
                @if ($user->contact)
                    <div class="animate-fade-in">
                        <label
                            class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2 block">Contact</label>
                        <p class="text-lg font-bold text-stone-900">{{ $user->contact }}</p>
                    </div>
                @endif
                <div class="animate-fade-in">
                    <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2 block">Member
                        Since</label>
                    <p class="text-lg font-bold text-stone-900">{{ $user->created_at->format('F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Nexo Paisa Card -->
        <div
            class="bg-white border border-black/10 rounded-[3rem] shadow-xl p-10 animate-fade-in hover:shadow-2xl transition-shadow duration-300">
            <h3 class="text-2xl font-bold text-stone-900 mb-6 font-serif italic">Nexo Paisa</h3>

            <div class="flex items-center justify-between">
                <div>
                    <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2 block">Current
                        Balance</label>
                    <p class="text-3xl font-bold text-stone-900 animate-float">Rs. {{ number_format($user->nexo_paisa, 2) }}
                    </p>
                </div>
                <a href="{{ route('user.loadNexoPaisa') }}"
                    class="bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-700 transition-all duration-300 hover:scale-105 hover:shadow-lg animate-bounce-in">
                    Load Nexo Paisa
                </a>
            </div>
        </div>

        <!-- Transaction History Section -->
        <div class="bg-white border border-black/10 rounded-[3rem] shadow-xl p-10 animate-fade-in">
            <h3 class="text-2xl font-bold text-stone-900 mb-6 font-serif italic">Transaction History</h3>

            <!-- Transaction Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center">
                    <div class="text-sm font-black text-green-700 uppercase tracking-widest mb-2">Total Loaded</div>
                    <div class="text-2xl font-bold font-serif text-green-600">Rs. {{ number_format($totalLoaded, 2) }}</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center">
                    <div class="text-sm font-black text-red-700 uppercase tracking-widest mb-2">Total Spent</div>
                    <div class="text-2xl font-bold font-serif text-red-600">Rs. {{ number_format($totalSpent, 2) }}</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center">
                    <div class="text-sm font-black text-blue-700 uppercase tracking-widest mb-2">Current Balance</div>
                    <div class="text-2xl font-bold font-serif text-blue-600">Rs. {{ number_format($user->nexo_paisa, 2) }}
                    </div>
                </div>
            </div>

            @if ($transactions->isEmpty())
                <div class="text-center py-12 text-stone-400">
                    <i class="fa-solid fa-receipt text-5xl mb-4 animate-bounce"></i>
                    <p class="text-sm font-bold uppercase tracking-widest">No transactions yet</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($transactions as $index => $transaction)
                        <div
                            class="border border-stone-200 rounded-2xl p-6 hover:border-orange-600 transition-all duration-300 animate-fade-in hover:scale-[1.02] hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center {{ $transaction->type === 'load' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} animate-pulse">
                                        <i
                                            class="fa-solid {{ $transaction->type === 'load' ? 'fa-plus' : 'fa-minus' }} text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-stone-900">{{ $transaction->description }}</h4>
                                        <p class="text-sm text-stone-500">
                                            {{ $transaction->created_at->format('M d, Y \a\t h:i A') }}</p>
                                        @if ($transaction->metadata && isset($transaction->metadata['bank']))
                                            <p class="text-xs text-stone-400 mt-1">Bank:
                                                {{ $transaction->metadata['bank'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p
                                        class="text-3xl font-bold font-serif {{ $transaction->type === 'load' ? 'text-green-600' : 'text-red-600' }} animate-float">
                                        {{ $transaction->type === 'load' ? '+' : '-' }}Rs.
                                        {{ number_format($transaction->amount, 2) }}
                                    </p>
                                    <span
                                        class="text-xs font-black uppercase tracking-widest px-3 py-1 rounded-full {{ $transaction->type === 'load' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $transaction->type === 'load' ? 'Credit' : 'Debit' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        <!-- Car Bookings Section -->
        <div class="bg-white border border-black/10 rounded-[3rem] shadow-xl p-10 animate-fade-in">
            <h3 class="text-2xl font-bold text-stone-900 mb-6 font-serif italic">Car Bookings</h3>

            @if ($carBookings->isEmpty())
                <div class="text-center py-12 text-stone-400">
                    <i class="fa-solid fa-car text-5xl mb-4"></i>
                    <p class="text-sm font-bold uppercase tracking-widest">No car bookings yet</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($carBookings as $booking)
                        <div class="border border-stone-200 rounded-2xl p-6 hover:border-orange-600 transition-all">
                            <div class="flex flex-col md:flex-row gap-6">
                                @if ($booking->car && $booking->car->image_url)
                                    <div class="md:w-48 h-32 rounded-xl overflow-hidden">
                                        <img src="{{ asset($booking->car->image_url) }}" alt="{{ $booking->car->name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="text-xl font-bold text-stone-900 font-serif italic mb-2">
                                        {{ $booking->car ? $booking->car->name : $booking->car_details['name'] ?? 'Car' }}
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-stone-400 font-bold uppercase text-xs">Days</span>
                                            <p class="font-bold text-stone-900">{{ $booking->days }}</p>
                                        </div>
                                        <div>
                                            <span class="text-stone-400 font-bold uppercase text-xs">Price/Day</span>
                                            <p class="font-bold text-stone-900">Rs.
                                                {{ number_format($booking->price_per_day, 2) }}</p>
                                        </div>
                                        <div>
                                            <span class="text-stone-400 font-bold uppercase text-xs">Total</span>
                                            <p class="font-bold text-orange-600">Rs.
                                                {{ number_format($booking->total_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <span class="text-stone-400 font-bold uppercase text-xs">Date</span>
                                            <p class="font-bold text-stone-900">
                                                {{ $booking->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    @if ($booking->car)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <span
                                                class="text-xs font-black uppercase tracking-widest bg-stone-50 px-3 py-1 rounded-full">
                                                {{ $booking->car->type }}
                                            </span>
                                            <span
                                                class="text-xs font-black uppercase tracking-widest bg-stone-50 px-3 py-1 rounded-full">
                                                {{ $booking->car->transmission }}
                                            </span>
                                            <span
                                                class="text-xs font-black uppercase tracking-widest bg-stone-50 px-3 py-1 rounded-full">
                                                {{ $booking->car->seating_capacity }} Seater
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Hotel Bookings Section -->
        <div class="bg-white border border-black/10 rounded-[3rem] shadow-xl p-10 animate-fade-in">
            <h3 class="text-2xl font-bold text-stone-900 mb-6 font-serif italic">Hotel Bookings</h3>

            @if ($hotelBookings->isEmpty())
                <div class="text-center py-12 text-stone-400">
                    <i class="fa-solid fa-hotel text-5xl mb-4"></i>
                    <p class="text-sm font-bold uppercase tracking-widest">No hotel bookings yet</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($hotelBookings as $booking)
                        <div class="border border-stone-200 rounded-2xl p-6 hover:border-orange-600 transition-all">
                            <div class="flex flex-col md:flex-row gap-6">
                                @if ($booking->image_url)
                                    <div class="md:w-48 h-32 rounded-xl overflow-hidden">
                                        <img src="{{ $booking->image_url }}" alt="{{ $booking->hotel_name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="text-xl font-bold text-stone-900 font-serif italic">
                                            {{ $booking->hotel_name }}
                                        </h4>
                                        @if ($booking->rating)
                                            <div
                                                class="bg-[#1c1917]/90 text-white px-3 py-1 rounded-full text-xs font-black">
                                                ★ {{ $booking->rating }}
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-sm text-stone-400 mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-orange-600"></i>
                                        {{ $booking->location }}
                                    </p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-stone-400 font-bold uppercase text-xs">Nights</span>
                                            <p class="font-bold text-stone-900">{{ $booking->nights }}</p>
                                        </div>
                                        <div>
                                            <span class="text-stone-400 font-bold uppercase text-xs">Price/Night</span>
                                            <p class="font-bold text-stone-900">Rs.
                                                {{ number_format($booking->price_per_night, 2) }}</p>
                                        </div>
                                        <div>
                                            <span class="text-stone-400 font-bold uppercase text-xs">Total</span>
                                            <p class="font-bold text-orange-600">Rs.
                                                {{ number_format($booking->total_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <span class="text-stone-400 font-bold uppercase text-xs">Date</span>
                                            <p class="font-bold text-stone-900">
                                                {{ $booking->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    @if ($booking->amenities && count($booking->amenities) > 0)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @foreach ($booking->amenities as $amenity)
                                                <span
                                                    class="text-xs font-black uppercase tracking-widest bg-stone-50 px-3 py-1 rounded-full">
                                                    {{ $amenity }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
