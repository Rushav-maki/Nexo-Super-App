@extends('layouts.app')

@section('content')
    @php
        $username = auth()->user()->name;
        $status = 'User Node';
    @endphp

    <div class="space-y-20 pb-10">

        {{-- Hero Greeting Section --}}
        <section class="relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-r from-orange-500/5 to-purple-500/5 rounded-[3rem] transform -skew-y-1">
            </div>
            <div
                class="relative bg-white/80 backdrop-blur-xl rounded-[3rem] border border-stone-200/60 shadow-2xl p-10 md:p-14 animate-slide-up">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-10">
                    <div class="space-y-6 flex-1">
                        <!-- Status Badge -->
                        <div
                            class="inline-flex items-center gap-3 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-full text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-orange-500/30 hover:shadow-2xl hover:shadow-orange-500/40 transition-all duration-500 cursor-default group">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                            </span>
                            <span class="group-hover:tracking-[0.3em] transition-all duration-300">
                                {{ now()->format('H') < 12 ? 'Good Morning' : (now()->format('H') < 17 ? 'Good Afternoon' : 'Good Evening') }},
                                {{ $username }}
                            </span>
                        </div>

                        <!-- Main Greeting -->
                        <div class="space-y-5">
                            <div class="flex flex-col md:flex-row md:items-end gap-5">
                                <h1
                                    class="text-6xl md:text-7xl lg:text-8xl font-black tracking-tight bg-gradient-to-br from-stone-900 via-stone-800 to-stone-900 bg-clip-text text-transparent leading-none hover:scale-105 transition-transform duration-700 cursor-default font-serif italic origin-left">
                                    Namaste<span class="text-orange-600">.</span>
                                </h1>
                                {{-- <div class="inline-flex items-center gap-3 px-6 py-3 bg-stone-900 text-white rounded-2xl shadow-2xl mb-2 hover:shadow-3xl hover:-translate-y-1 transition-all duration-500 group cursor-default">
                                <span class="text-[9px] font-black uppercase tracking-[0.3em] opacity-70 group-hover:opacity-100 transition-opacity">Node:</span>
                                <span class="text-xs font-bold text-emerald-400">{{ $status }}</span>
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            </div> --}}
                            </div>
                            <p class="text-xl md:text-2xl text-stone-600 font-medium max-w-3xl leading-relaxed">
                                Welcome to your integrated <span class="font-bold text-stone-900">Himalayan
                                    dashboard</span>. Your Nexo node is fully synchronized.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- Premium Stats Grid --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-7 animate-fade-in">

            <!-- Nexo Paisa Card -->
            <a href="{{ route('user.loadNexoPaisa') }}"
                class="group relative bg-gradient-to-br from-orange-500 to-orange-600 rounded-[2rem] p-9 shadow-2xl shadow-orange-500/20 hover:shadow-3xl hover:shadow-orange-500/40 transition-all duration-700 hover:-translate-y-3 cursor-pointer overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                </div>
                <div
                    class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-transform duration-1000">
                </div>
                <div class="relative z-10 space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-orange-100 uppercase tracking-[0.2em]">Nexo Paisa</span>
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:rotate-12 group-hover:scale-110 transition-all duration-500">
                            <i class="fa-solid fa-wallet text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div
                            class="text-4xl font-black text-white group-hover:scale-110 transition-transform duration-500 origin-left font-serif">
                            Rs. {{ number_format(auth()->user()->nexo_paisa, 2) }}
                        </div>
                        <div
                            class="inline-flex items-center gap-2 text-sm font-bold text-white/90 hover:text-white transition-colors">
                            <span>Load More</span>
                            <i
                                class="fa-solid fa-arrow-right group-hover:translate-x-2 transition-transform duration-300"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Profile Card -->
            <a href="{{ route('user.profile') }}"
                class="group relative bg-white rounded-[2rem] p-9 shadow-xl border border-stone-200 hover:shadow-3xl hover:border-blue-200 transition-all duration-700 hover:-translate-y-3 overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-100 to-transparent rounded-full -mr-20 -mt-20 opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                </div>
                <div class="relative z-10 space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-stone-400 uppercase tracking-[0.2em]">Profile</span>
                        <div
                            class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                            <i class="fa-solid fa-user text-blue-500 text-xl"></i>
                        </div>
                    </div>
                    <div
                        class="text-4xl font-black text-stone-900 group-hover:scale-110 transition-transform duration-500 origin-left font-serif">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-sm text-stone-500 font-bold">View & Edit Profile</div>
                </div>
            </a>

            <!-- No. of Transactions Card -->
            <div
                class="group relative bg-white rounded-[2rem] p-9 shadow-xl border border-stone-200 hover:shadow-3xl hover:border-emerald-200 transition-all duration-700 hover:-translate-y-3 overflow-hidden">
                @php
                    $transactionsCount = \App\Models\NexoPaisaTransaction::where('user_id', auth()->id())->count();
                @endphp
                <div
                    class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-emerald-100 to-transparent rounded-full -mr-20 -mt-20 opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                </div>
                <div class="relative z-10 space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-stone-400 uppercase tracking-[0.2em]">Transactions</span>
                        <div
                            class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center group-hover:bg-emerald-100 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                            <i class="fa-solid fa-list-check text-emerald-500 text-xl"></i>
                        </div>
                    </div>
                    <div
                        class="text-4xl font-black text-stone-900 group-hover:scale-110 transition-transform duration-500 origin-left font-serif">
                        {{ $transactionsCount }}
                    </div>
                    <div class="text-sm text-stone-500 font-bold">Total Transactions</div>
                </div>
            </div>

            <!-- Climate Card -->
            <div x-data="weatherCard()" x-init="fetchWeather()"
                class="group relative rounded-[2rem] p-9 shadow-xl border border-stone-200 transition-all duration-700 hover:-translate-y-3 overflow-hidden"
                :class="cardBg">

                <!-- Glow -->
                <div class="absolute top-0 right-0 w-40 h-40 rounded-full -mr-20 -mt-20 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                    :class="glowBg"></div>

                <div class="relative z-10 space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-stone-400">
                            Climate · Kathmandu
                        </span>

                        <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-500"
                            :class="iconBg">
                            <i :class="icon" class="text-xl"></i>
                        </div>
                    </div>

                    <div class="text-4xl font-black font-serif transition-transform duration-500 origin-left group-hover:scale-110"
                        x-text="temp"></div>

                    <div class="text-sm font-bold" x-text="desc"></div>
                </div>
            </div>

        </section>

        {{-- Premium Modules Grid --}}
        <section class="space-y-10 animate-slide-up">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <h3 class="text-4xl font-black text-stone-900 font-serif italic">Nexo Modules</h3>
                    <p class="text-stone-500 font-medium">Access your integrated services</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-7">
                @php
                    $modules = [
                        [
                            'title' => 'EduSync',
                            'icon' => 'fa-book-open-reader',
                            'route' => 'user.education',
                            'desc' => 'Learning Hub',
                        ],
                        [
                            'title' => 'Voyage',
                            'icon' => 'fa-compass',
                            'route' => 'user.travel',
                            'desc' => 'Travel Planning',
                        ],
                        [
                            'title' => 'Health',
                            'icon' => 'fa-heart-pulse',
                            'route' => 'user.health',
                            'desc' => 'Wellness Tracking',
                        ],
                        [
                            'title' => 'Agro',
                            'icon' => 'fa-seedling',
                            'route' => 'user.agro',
                            'desc' => 'Farm Management',
                        ],
                        [
                            'title' => 'Calendar',
                            'icon' => 'fa-calendar-days',
                            'route' => 'user.calendar',
                            'desc' => 'Schedule & Events',
                        ],
                    ];
                @endphp

                @foreach ($modules as $index => $mod)
                    <a href="{{ route($mod['route'] ?? '#') }}"
                        class="group relative bg-white rounded-[2rem] p-10 shadow-xl border border-stone-200 hover:bg-orange-600 hover:text-white hover:shadow-3xl hover:border-transparent transition-all duration-700 hover:-translate-y-3 overflow-hidden flex flex-col justify-between aspect-square"
                        style="animation-delay: {{ $index * 100 }}ms">

                        <div
                            class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                        </div>

                        <div class="relative z-10">
                            <div
                                class="h-16 w-16 bg-stone-50 rounded-2xl flex items-center justify-center group-hover:bg-white group-hover:text-orange-600 shadow-lg group-hover:scale-110 group-hover:rotate-12 transition-all duration-700">
                                <i class="fa-solid {{ $mod['icon'] }} text-3xl"></i>
                            </div>
                        </div>

                        <div class="relative z-10 space-y-3">
                            <h4
                                class="text-3xl font-black font-serif italic text-stone-900 group-hover:text-white transition-colors duration-500">
                                {{ $mod['title'] }}
                            </h4>
                            <p
                                class="text-sm text-stone-500 group-hover:text-white/80 font-medium transition-colors duration-500">
                                {{ $mod['desc'] }}
                            </p>
                            <div
                                class="w-10 h-1 bg-stone-200 group-hover:bg-white group-hover:w-16 rounded-full transition-all duration-700">
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

    </div>

    <!-- Simple Floating Chatbot -->
    <div class="fixed bottom-8 right-8 z-50">
        <a href="{{ route('user.chat') }}"
            class="relative h-14 w-14
               bg-gradient-to-br from-purple-600 to-purple-700
               text-white rounded-2xl
               shadow-lg
               flex items-center justify-center
               hover:scale-105 transition-transform duration-200">

            <!-- Robot Icon -->
            <i class="fa-solid fa-robot text-xl"></i>

            <!-- Online Dot -->
            <span
                class="absolute -top-1 -right-1
                   h-3 w-3
                   bg-emerald-500
                   border-2 border-white
                   rounded-full">
            </span>
        </a>
    </div>
@endsection

<script>
    function weatherCard() {
        return {
            temp: '--°C',
            desc: 'Loading...',
            icon: 'fa-solid fa-sun text-yellow-500',
            cardBg: 'bg-white',
            glowBg: 'bg-yellow-100',
            iconBg: 'bg-yellow-50',

            async fetchWeather() {
                const lat = 27.7172;
                const lon = 85.3240;

                try {
                    const res = await fetch(
                        `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`
                    );
                    const data = await res.json();

                    const w = data.current_weather;
                    const isDay = w.is_day === 1;

                    this.temp = `${Math.round(w.temperature)}°C`;

                    // Weather + Day/Night mapping
                    const map = {
                        0: {
                            d: 'Clear Sky',
                            day: 'fa-sun',
                            night: 'fa-moon'
                        },
                        1: {
                            d: 'Mainly Clear',
                            day: 'fa-sun',
                            night: 'fa-moon'
                        },
                        2: {
                            d: 'Partly Cloudy',
                            day: 'fa-cloud-sun',
                            night: 'fa-cloud-moon'
                        },
                        3: {
                            d: 'Overcast',
                            day: 'fa-cloud',
                            night: 'fa-cloud'
                        },
                        45: {
                            d: 'Foggy',
                            day: 'fa-smog',
                            night: 'fa-smog'
                        },
                        61: {
                            d: 'Rain',
                            day: 'fa-cloud-rain',
                            night: 'fa-cloud-rain'
                        },
                        63: {
                            d: 'Heavy Rain',
                            day: 'fa-cloud-showers-heavy',
                            night: 'fa-cloud-showers-heavy'
                        },
                        71: {
                            d: 'Snow',
                            day: 'fa-snowflake',
                            night: 'fa-snowflake'
                        },
                        95: {
                            d: 'Thunderstorm',
                            day: 'fa-bolt',
                            night: 'fa-bolt'
                        },
                    };

                    const weather = map[w.weathercode] || map[0];
                    this.desc = weather.d;

                    if (isDay) {
                        this.icon = `fa-solid ${weather.day} text-orange-500`;
                        this.cardBg = 'bg-white';
                        this.glowBg = 'bg-orange-100';
                        this.iconBg = 'bg-orange-50';
                    } else {
                        this.icon = `fa-solid ${weather.night} text-indigo-400`;
                        this.cardBg = 'bg-gradient-to-br from-stone-900 to-stone-800 text-white';
                        this.glowBg = 'bg-indigo-500/30';
                        this.iconBg = 'bg-white/10';
                    }

                } catch (e) {
                    console.error(e);
                    this.desc = 'Unavailable';
                }
            }
        }
    }
</script>
