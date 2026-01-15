@extends('layouts.app')

@section('content')
    <div x-data="travelApp()" class="space-y-8 pb-20 animate-fadeIn">

        <!-- Tabs -->
        <div
            class="flex bg-stone-100 p-1.5 rounded-[1.5rem] border border-black/5 w-fit shadow-inner overflow-x-auto no-scrollbar">
            <template x-for="(tab, idx) in tabs" :key="idx">
                <button @click="selectTab(tab.id)"
                    :class="currentTab === tab.id ? 'bg-[#1c1917] text-[#fafaf9]' : 'text-stone-500 hover:text-stone-900'"
                    class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all whitespace-nowrap font-serif">
                    <span x-text="tab.name"></span>
                </button>
            </template>
        </div>

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-3xl font-bold text-stone-900 font-serif tracking-tighter uppercase italic">
                Nexo<span class="text-orange-600">.Travel</span>
            </h2>
        </div>

        <!-- Plan Tab -->
        <div x-show="currentTab === 'plan'" class="space-y-8">
            <!-- Input Form -->
            <div class="bg-white border border-black/5 p-8 md:p-12 rounded-[3rem] shadow-xl">
                <h3
                    class="text-xl font-bold text-stone-900 mb-10 flex items-center gap-4 uppercase tracking-tighter font-serif italic">
                    <i class="fa-solid fa-compass text-orange-600"></i> Plan your Trip
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest ml-1 font-serif">Where
                            to?</label>
                        <input type="text" x-model="destination" @input="saveDestination()" placeholder="e.g. Pokhara"
                            class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-2xl px-6 py-4 focus:ring-1 focus:ring-orange-600 font-bold font-serif" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest ml-1 font-serif">How
                            many
                            days?</label>
                        <input type="number" x-model.number="days" min="1"
                            class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-2xl px-6 py-4 focus:ring-1 focus:ring-orange-600 font-bold font-serif" />
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black text-stone-400 uppercase tracking-widest ml-1 font-serif">Budget</label>
                        <select x-model="budget"
                            class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-2xl px-6 py-4 focus:ring-1 focus:ring-orange-600 font-bold font-serif">
                            <option>Affordable</option>
                            <option>Standard</option>
                            <option>Premium</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="fetchPlan()" :disabled="loading"
                            class="w-full h-[64px] bg-[#1c1917] hover:bg-orange-600 disabled:bg-stone-400 text-[#fafaf9] rounded-2xl font-black uppercase tracking-widest shadow-xl transition-all flex items-center justify-center gap-3 font-serif">
                            <span x-show="!loading">CREATE PLAN</span>
                            <span x-show="loading"><i class="fa-solid fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Itinerary Cards Grid -->
            <div x-show="itinerary" class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-slideUp">
                <template x-for="(day, dayIdx) in parseItinerary(itinerary)" :key="dayIdx">
                    <div @click="selectedDay = day"
                        class="bg-gradient-to-br from-stone-50 to-orange-50/30 border-2 border-black/5 p-8 rounded-3xl hover:border-orange-600 hover:shadow-2xl hover:shadow-orange-600/20 transition-all duration-300 cursor-pointer group">
                        <div class="flex justify-between items-start mb-6">
                            <span class="text-sm font-black text-stone-900 uppercase tracking-widest font-serif"
                                x-text="'Day ' + day.day"></span>
                            <div
                                class="h-3 w-3 rounded-full bg-orange-600 group-hover:scale-150 transition-transform duration-300">
                            </div>
                        </div>

                        <!-- Preview Activities (first 2) -->
                        <div class="space-y-3 mb-6">
                            <template x-for="(activity, idx) in day.desc.slice(0, 2)" :key="idx">
                                <div class="flex items-start gap-2">
                                    <div class="min-w-[6px] h-[6px] rounded-full bg-orange-600 mt-2"></div>
                                    <p class="text-sm text-stone-700 font-medium line-clamp-2 font-serif" x-text="activity">
                                    </p>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-black/10">
                            <span class="text-xs font-bold text-orange-600 uppercase tracking-wider font-serif"
                                x-text="day.budget"></span>
                            <span
                                class="text-xs font-semibold text-stone-500 group-hover:text-orange-600 transition-colors font-serif">View
                                Details →</span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Expanded Day Modal -->
            <div x-show="selectedDay" x-cloak @click.self="selectedDay = null"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fadeIn">
                <div @click.stop
                    class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl animate-scaleIn">

                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-orange-600 to-orange-500 p-8 relative">
                        <button @click="selectedDay = null"
                            class="absolute top-6 right-6 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-all group">
                            <span
                                class="text-white text-2xl group-hover:rotate-90 transition-transform duration-300">×</span>
                        </button>
                        <h2 class="text-3xl font-black text-white uppercase tracking-wider font-serif"
                            x-text="selectedDay ? 'Day ' + selectedDay.day : ''"></h2>
                    </div>

                    <!-- Modal Content -->
                    <div class="overflow-y-auto max-h-[calc(90vh-180px)]">
                        <template x-if="selectedDay">
                            <div>
                                <!-- Time Periods -->
                                <div class="p-8 space-y-8">
                                    <template x-for="(period, periodName) in groupActivitiesByTime(selectedDay.desc)"
                                        :key="periodName">
                                        <div x-show="period.length > 0">
                                            <!-- Period Header -->
                                            <div class="flex items-center gap-3 mb-4">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-orange-600 to-orange-500 rounded-full flex items-center justify-center shadow-lg shadow-orange-600/30">
                                                    <span class="text-lg"
                                                        x-text="periodName === 'morning' ? '🌅' : periodName === 'afternoon' ? '☀️' : periodName === 'evening' ? '🌆' : '🌙'"></span>
                                                </div>
                                                <h3 class="text-xl font-black text-orange-600 uppercase tracking-wide font-serif"
                                                    x-text="periodName.charAt(0).toUpperCase() + periodName.slice(1)"></h3>
                                            </div>

                                            <!-- Period Activities -->
                                            <div class="ml-12 space-y-3">
                                                <template x-for="activity in period" :key="activity">
                                                    <div class="flex items-start gap-3 group">
                                                        <div
                                                            class="min-w-[8px] h-[8px] rounded-full bg-orange-600 mt-2 group-hover:scale-150 transition-transform">
                                                        </div>
                                                        <p class="text-stone-800 font-medium leading-relaxed font-serif"
                                                            x-text="activity.replace(/^(Morning|Afternoon|Evening|Night):\s*/, '')">
                                                        </p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Budget Section -->
                                <div
                                    class="bg-gradient-to-r from-orange-50 to-stone-50 border-t-2 border-orange-600/20 p-8">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4
                                                class="text-xs font-black text-stone-500 uppercase tracking-widest mb-1 font-serif">
                                                Daily Budget</h4>
                                            <p class="text-2xl font-black text-orange-600 font-serif"
                                                x-text="selectedDay.budget"></p>
                                        </div>
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br from-orange-600 to-orange-500 rounded-2xl flex items-center justify-center shadow-xl shadow-orange-600/30">
                                            <span class="text-3xl">💰</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hotels Tab -->
        <div x-show="currentTab === 'hotels'" class="space-y-12">
            <div x-show="!destination"
                class="bg-stone-50 p-16 rounded-[3rem] border border-dashed border-orange-200 text-center">
                <i class="fa-solid fa-hotel text-5xl text-stone-200 mb-6"></i>
                <p class="text-stone-400 font-bold uppercase tracking-widest text-xs font-serif italic">Enter a destination
                    to see live hotel options.</p>
            </div>

            <div x-show="destination" class="grid grid-cols-1 lg:grid-cols-2 gap-10 animate-fadeIn">
                <div x-show="loading && !hotels.length" class="col-span-full py-20 text-center">
                    <div
                        class="h-12 w-12 border-4 border-orange-600 border-t-transparent rounded-full animate-spin mx-auto mb-4">
                    </div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] italic font-serif">Loading hotels...</p>
                </div>

                <template x-for="(hotel, hotelIdx) in hotels" :key="hotelIdx">
                    <div
                        class="bg-white border border-black/5 rounded-[3.5rem] overflow-hidden shadow-xl flex flex-col transition-all hover:shadow-2xl">
                        <!-- Hotel Image -->
                        <div class="h-64 relative overflow-hidden group">
                            <img :src="hotel.image || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800'"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                :alt="hotel.name" />
                            <div
                                class="absolute top-6 left-6 bg-[#1c1917]/90 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest backdrop-blur-md font-serif">
                                <span>★ <span x-text="hotel.rating || '4.5'"></span></span>
                            </div>
                        </div>

                        <!-- Hotel Details -->
                        <div class="p-10 flex-1 space-y-6">
                            <div>
                                <h4 class="text-3xl font-bold font-serif italic text-stone-900" x-text="hotel.name"></h4>
                                <p
                                    class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mt-2 flex items-center gap-2 font-serif">
                                    <i class="fa-solid fa-location-dot text-orange-600"></i>
                                    <span x-text="hotel.location"></span>
                                </p>
                            </div>

                            <!-- Amenities -->
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(amenity, aidx) in (hotel.amenities || [])" :key="aidx">
                                    <span
                                        class="text-[9px] font-black uppercase tracking-widest bg-stone-50 px-3 py-1.5 rounded-full text-stone-900 font-serif"
                                        x-text="amenity"></span>
                                </template>
                            </div>

                            <!-- Price & Book -->
                            <div class="pt-6 border-t border-black/5">
                                <div class="flex justify-between items-center">
                                    <span class="text-2xl font-black text-stone-900 font-serif">
                                        Rs. <span x-text="hotel.pricePerNight || '5000'"></span>
                                        <span class="text-xs font-medium text-stone-400 uppercase opacity-60 font-serif">/
                                            night</span>
                                    </span>
                                    <button @click="bookHotel(hotel)"
                                        class="bg-[#1c1917] text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-md font-serif">Book
                                        Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Cars Tab -->
        <div x-show="currentTab === 'cars'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 animate-fadeIn">

            <template x-for="(car, carIdx) in cars" :key="car.id ?? carIdx">
                <div
                    class="group bg-white border border-black/5 rounded-[3.5rem] overflow-hidden hover:border-orange-600 transition-all shadow-lg hover:shadow-2xl">

                    <!-- Car Image -->
                    <div class="h-60 overflow-hidden relative">
                        <img :src="car.image_url ?? 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=400'"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            :alt="car.name" />
                        <div class="absolute bottom-6 left-6 bg-white/90 backdrop-blur-md px-5 py-2 rounded-full text-[10px] font-black text-stone-900 uppercase tracking-widest shadow-sm font-serif"
                            x-text="car.type">
                        </div>
                    </div>

                    <!-- Car Details -->
                    <div class="p-10">
                        <h4 class="text-2xl font-bold text-stone-900 font-serif italic" x-text="car.name"></h4>

                        <p class="text-xs text-stone-400 font-bold uppercase mt-3 tracking-tight font-serif">
                            <span x-text="car.transmission"></span> •
                            <span x-text="car.fuelType"></span> •
                            <span x-text="car.seatingCapacity + ' Seater'"></span>
                        </p>

                        <div class="mt-10 flex items-center justify-between border-t border-black/5 pt-8">
                            <div>
                                <span class="text-2xl font-black text-stone-900 font-serif">
                                    Rs. <span x-text="car.pricePerDay"></span>
                                </span>
                                <span class="text-[10px] text-stone-400 font-bold ml-1 uppercase font-serif">/ day</span>
                            </div>

                            <button @click="rentCar(car)"
                                class="bg-[#1c1917] text-white px-6 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-md font-serif">
                                Rent
                            </button>
                        </div>
                    </div>

                </div>
            </template>

            <!-- Empty State -->
            <div x-show="!loading && cars.length === 0"
                class="col-span-full text-center py-24 text-stone-400 font-bold uppercase tracking-widest font-serif">
                No cars available
            </div>
        </div>

        <!-- Loading Indicator -->
        <div x-show="loading" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 rounded-2xl">
            <div class="bg-white p-8 rounded-[2rem] shadow-2xl">
                <div
                    class="h-12 w-12 border-4 border-orange-600 border-t-transparent rounded-full animate-spin mx-auto mb-4">
                </div>
                <p class="text-xs font-black uppercase tracking-[0.2em] italic text-center font-serif">Processing...</p>
            </div>
        </div>
    </div>

    <script>
        window.travelApp = function travelApp() {
            return {
                tabs: [{
                        id: 'plan',
                        name: 'Plan'
                    },
                    {
                        id: 'hotels',
                        name: 'Hotels'
                    },
                    {
                        id: 'cars',
                        name: 'Cars'
                    }
                ],
                currentTab: 'plan',
                destination: sessionStorage.getItem('travel_destination') || '',
                days: 3,
                budget: 'Standard',
                loading: false,
                itinerary: null,
                hotels: [],
                cars: [],
                selectedDay: null,

                saveDestination() {
                    sessionStorage.setItem('travel_destination', this.destination);
                },

                parseItinerary(data) {
                    try {
                        console.log('Raw data:', data);

                        if (!data) return [];

                        // Handle double-nested JSON string
                        let parsed = data;

                        // If data.plan is a string, parse it (remove trailing commas first)
                        if (data && typeof data.plan === 'string') {
                            // Remove trailing commas before closing brackets/braces
                            let cleanedJson = data.plan
                                .replace(/,(\s*[}\]])/g, '$1') // Remove trailing commas
                                .replace(/\n/g, ' ') // Remove newlines for easier parsing
                                .trim();

                            try {
                                parsed = JSON.parse(cleanedJson);
                            } catch (jsonError) {
                                console.error('JSON parse error, trying alternative method:', jsonError);
                                // If still fails, try to extract the plan array manually
                                return this.fallbackParse(data.plan);
                            }
                        }

                        // If data itself is a string, parse it
                        if (typeof data === 'string') {
                            let cleanedJson = data.replace(/,(\s*[}\]])/g, '$1').trim();
                            parsed = JSON.parse(cleanedJson);
                        }

                        console.log('Parsed data:', parsed);

                        if (!parsed || !parsed.plan) return [];

                        if (Array.isArray(parsed.plan)) {
                            return parsed.plan.map(day => ({
                                day: day.day,
                                desc: Array.isArray(day.desc) ? day.desc : (day.desc ? day.desc.split(
                                    '\n') : []),
                                budget: day.budget || 'Budget not specified'
                            }));
                        }

                        if (typeof parsed.plan === 'string') {
                            return this.fallbackParse(parsed.plan);
                        }

                        return [];
                    } catch (e) {
                        console.error('Error parsing itinerary:', e);
                        return [];
                    }
                },

                fallbackParse(planString) {
                    // Fallback parser for malformed JSON
                    const days = [];
                    try {
                        // Try to extract day objects using regex
                        const dayPattern =
                            /"day":\s*(\d+)[\s\S]*?"desc":\s*\[([\s\S]*?)\][\s\S]*?"budget":\s*"([^"]+)"/g;
                        let match;

                        while ((match = dayPattern.exec(planString)) !== null) {
                            const dayNum = match[1];
                            const descString = match[2];
                            const budget = match[3];

                            // Extract activities from desc array
                            const activityPattern = /"([^"]+)"/g;
                            const activities = [];
                            let actMatch;

                            while ((actMatch = activityPattern.exec(descString)) !== null) {
                                activities.push(actMatch[1]);
                            }

                            if (activities.length > 0) {
                                days.push({
                                    day: dayNum,
                                    desc: activities,
                                    budget: budget
                                });
                            }
                        }
                    } catch (e) {
                        console.error('Fallback parse error:', e);
                    }

                    return days;
                },

                selectTab(tabId) {
                    this.currentTab = tabId;
                    if (tabId === 'cars') this.fetchCars();
                    if (tabId === 'hotels' && this.destination && this.hotels.length === 0) {
                        this.fetchHotels();
                    }
                },

                async fetchPlan() {
                    if (!this.destination) return alert('Please enter a destination');

                    this.loading = true;
                    try {
                        const res = await fetch("{{ route('user.travel.plan') }}", {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                destination: this.destination,
                                days: this.days,
                                budget: this.budget
                            })
                        });
                        if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                        this.itinerary = await res.json();

                        console.log('Itinerary received:', this.itinerary);

                        // Automatically fetch hotels when plan is created
                        await this.fetchHotels();

                    } catch (err) {
                        console.error(err);
                        alert('Error: ' + err.message);
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchHotels() {
                    if (!this.destination) return;
                    this.loading = true;
                    try {
                        const res = await fetch("{{ route('user.travel.hotels') }}", {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                destination: this.destination
                            })
                        });
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const data = await res.json();
                        console.log('Hotels received:', data);
                        this.hotels = Array.isArray(data) ? data : (data.hotels || []);
                    } catch (err) {
                        console.error('Hotel fetch error:', err);
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchCars() {
                    this.loading = true;
                    try {
                        const res = await fetch("{{ route('user.travel.cars') }}", {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const data = await res.json();
                        this.cars = Array.isArray(data) ? data : [];
                    } catch (err) {
                        console.error(err);
                        alert('Failed to load cars');
                    } finally {
                        this.loading = false;
                    }
                },

                getTimeOfDay(activity) {
                    const lower = activity.toLowerCase();
                    if (lower.includes('morning')) return 'Morning';
                    if (lower.includes('afternoon')) return 'Afternoon';
                    if (lower.includes('evening')) return 'Evening';
                    if (lower.includes('night')) return 'Night';
                    return 'Activity';
                },

                getTimeIcon(activity) {
                    const lower = activity.toLowerCase();
                    if (lower.includes('morning')) return '🌅';
                    if (lower.includes('afternoon')) return '☀️';
                    if (lower.includes('evening')) return '🌆';
                    if (lower.includes('night')) return '🌙';
                    return '📍';
                },

                groupActivitiesByTime(activities) {
                    const groups = {
                        morning: [],
                        afternoon: [],
                        evening: [],
                        night: []
                    };

                    activities.forEach(activity => {
                        const lower = activity.toLowerCase();
                        if (lower.includes('morning')) {
                            groups.morning.push(activity);
                        } else if (lower.includes('afternoon')) {
                            groups.afternoon.push(activity);
                        } else if (lower.includes('evening')) {
                            groups.evening.push(activity);
                        } else if (lower.includes('night')) {
                            groups.night.push(activity);
                        }
                    });

                    return groups;
                },

                rentCar(car) {
                    // Save selected car in sessionStorage
                    sessionStorage.setItem('selectedCar', JSON.stringify(car));

                    // Redirect to booking page
                    window.location.href = "{{ route('user.travel.carBooking') }}";
                },

                bookHotel(hotel) {
                    // Save selected hotel in sessionStorage
                    sessionStorage.setItem('selectedHotel', JSON.stringify(hotel));

                    // Redirect to booking page
                    window.location.href = "{{ route('user.travel.hotelBooking') }}";
                }
            }
        }
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        .animate-slideUp {
            animation: slideUp 0.6s ease-out;
        }

        .animate-scaleIn {
            animation: scaleIn 0.3s ease-out;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
