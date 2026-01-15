@extends('layouts.app')

@section('title', 'EduSync Calendar')

@section('content')
    <div x-data="calendarApp()" class="space-y-12 pb-20">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <h2 class="text-4xl font-bold text-stone-900 font-serif tracking-tighter uppercase italic">
                    EduSync<span class="text-orange-600">.Calendar</span>
                </h2>
                <p class="text-sm text-stone-500 font-medium font-serif">Nepali Calendar System - BS <span x-text="currentYear"></span></p>
            </div>

            <!-- Today's Quick Info -->
            <div class="bg-stone-900 text-white px-8 py-4 rounded-2xl shadow-xl">
                <p class="text-[9px] font-black uppercase tracking-widest opacity-70 mb-2 font-serif">Today</p>
                <p x-text="todayDateText" class="text-sm font-bold font-serif"></p>
            </div>
        </div>

        <!-- Main Calendar Card -->
        <div class="bg-white border border-black/5 rounded-[3rem] shadow-2xl overflow-hidden">
            <!-- Calendar Header -->
            <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-10 py-8">
                <div class="flex items-center justify-between">
                    <button @click="prevMonth()"
                            class="h-12 w-12 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white flex items-center justify-center transition-all hover:scale-110 shadow-lg">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="text-center">
                        <h3 class="text-3xl font-bold text-white font-serif italic" x-text="currentMonthName"></h3>
                        <p class="text-white/80 text-sm font-semibold mt-1 font-serif" x-text="'BS ' + currentYear"></p>
                    </div>

                    <button @click="nextMonth()"
                            class="h-12 w-12 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white flex items-center justify-center transition-all hover:scale-110 shadow-lg">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="p-8 md:p-12">
                <!-- Days of Week -->
                <div class="grid grid-cols-7 gap-4 mb-6">
                    <template x-for="day in daysOfWeek" :key="day">
                        <div class="text-center text-[10px] font-black text-stone-400 uppercase tracking-widest font-serif" x-text="day"></div>
                    </template>
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7 gap-4">
                    <template x-for="(cell, index) in getCalendarGrid()" :key="index">
                        <div class="aspect-square">
                            <div x-show="cell.day"
                                 :class="isToday(cell.day) ? 'bg-orange-600 text-white shadow-xl ring-4 ring-orange-200' : 'bg-stone-50 hover:bg-stone-100 text-stone-900'"
                                 class="h-full rounded-2xl flex flex-col items-center justify-center transition-all cursor-pointer hover:scale-105 hover:shadow-lg group relative">
                                <span class="text-2xl font-bold font-serif" x-text="cell.day?.date"></span>
                                <span class="text-[8px] font-black uppercase tracking-widest mt-1 font-serif"
                                      :class="isToday(cell.day) ? 'text-white/80' : 'text-stone-400 group-hover:text-stone-600'"
                                      x-text="cell.day?.day"></span>
                                <!-- Today Badge -->
                                <div x-show="isToday(cell.day)" class="absolute -top-2 -right-2 bg-white text-orange-600 text-[7px] font-black px-2 py-0.5 rounded-full shadow-lg uppercase tracking-wider">
                                    Today
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Date Converters Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- BS to AD Conversion -->
            <div class="bg-white border border-black/5 rounded-[2.5rem] p-10 shadow-xl hover:shadow-2xl transition-all">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-14 w-14 bg-orange-50 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-serif italic text-stone-900">BS → AD</h3>
                        <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest font-serif">Convert Bikram Sambat</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Year</label>
                            <input type="number" x-model.number="bsYear" placeholder="2081"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Month</label>
                            <input type="number" x-model.number="bsMonth" placeholder="1-12" min="1" max="12"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Day</label>
                            <input type="number" x-model.number="bsDay" placeholder="1-32" min="1" max="32"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                    </div>

                    <button @click="convertBsToAd()"
                            class="w-full bg-stone-900 hover:bg-orange-600 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg hover:shadow-xl font-serif">
                        Convert to AD
                    </button>

                    <div x-show="bsToAdResult"
                         class="bg-orange-50 border border-orange-100 rounded-2xl p-6 text-center animate-fadeIn">
                        <p class="text-[9px] font-black text-orange-600 uppercase tracking-widest mb-2 font-serif">Result</p>
                        <p class="text-2xl font-bold text-stone-900 font-serif" x-text="bsToAdResult"></p>
                    </div>
                </div>
            </div>

            <!-- AD to BS Conversion -->
            <div class="bg-white border border-black/5 rounded-[2.5rem] p-10 shadow-xl hover:shadow-2xl transition-all">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-14 w-14 bg-orange-50 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-calendar text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-serif italic text-stone-900">AD → BS</h3>
                        <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest font-serif">Convert Gregorian</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Year</label>
                            <input type="number" x-model.number="adYear" placeholder="2024"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Month</label>
                            <input type="number" x-model.number="adMonth" placeholder="1-12" min="1" max="12"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Day</label>
                            <input type="number" x-model.number="adDay" placeholder="1-31" min="1" max="31"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                    </div>

                    <button @click="convertAdToBs()"
                            class="w-full bg-stone-900 hover:bg-orange-600 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg hover:shadow-xl font-serif">
                        Convert to BS
                    </button>

                    <div x-show="adToBsResult"
                         class="bg-orange-50 border border-orange-100 rounded-2xl p-6 text-center animate-fadeIn">
                        <p class="text-[9px] font-black text-orange-600 uppercase tracking-widest mb-2 font-serif">Result</p>
                        <p class="text-2xl font-bold text-stone-900 font-serif" x-text="adToBsResult"></p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Age Calculators Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Age from BS -->
            <div class="bg-white border border-black/5 rounded-[2.5rem] p-10 shadow-xl hover:shadow-2xl transition-all">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-14 w-14 bg-stone-50 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-cake-candles text-stone-900 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-serif italic text-stone-900">Age Calculator</h3>
                        <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest font-serif">From BS Date</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Year</label>
                            <input type="number" x-model.number="ageBsYear" placeholder="2050"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Month</label>
                            <input type="number" x-model.number="ageBsMonth" placeholder="1-12" min="1" max="12"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Day</label>
                            <input type="number" x-model.number="ageBsDay" placeholder="1-32" min="1" max="32"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                    </div>

                    <button @click="calculateAgeBs()"
                            class="w-full bg-stone-900 hover:bg-orange-600 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg hover:shadow-xl font-serif">
                        Calculate Age
                    </button>

                    <div x-show="ageBsResult"
                         class="bg-stone-50 border border-stone-200 rounded-2xl p-6 text-center animate-fadeIn">
                        <p class="text-[9px] font-black text-stone-400 uppercase tracking-widest mb-2 font-serif">Your Age</p>
                        <p class="text-xl font-bold text-stone-900 font-serif" x-text="ageBsResult"></p>
                    </div>
                </div>
            </div>

            <!-- Age from AD -->
            <div class="bg-white border border-black/5 rounded-[2.5rem] p-10 shadow-xl hover:shadow-2xl transition-all">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-14 w-14 bg-stone-50 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-cake-candles text-stone-900 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold font-serif italic text-stone-900">Age Calculator</h3>
                        <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest font-serif">From AD Date</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Year</label>
                            <input type="number" x-model.number="ageAdYear" placeholder="1990"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Month</label>
                            <input type="number" x-model.number="ageAdMonth" placeholder="1-12" min="1" max="12"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-stone-400 uppercase tracking-widest ml-2 font-serif">Day</label>
                            <input type="number" x-model.number="ageAdDay" placeholder="1-31" min="1" max="31"
                                   class="w-full bg-stone-50 border border-stone-100 text-stone-900 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-transparent font-bold text-center font-serif">
                        </div>
                    </div>

                    <button @click="calculateAgeAd()"
                            class="w-full bg-stone-900 hover:bg-orange-600 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg hover:shadow-xl font-serif">
                        Calculate Age
                    </button>

                    <div x-show="ageAdResult"
                         class="bg-stone-50 border border-stone-200 rounded-2xl p-6 text-center animate-fadeIn">
                        <p class="text-[9px] font-black text-stone-400 uppercase tracking-widest mb-2 font-serif">Your Age</p>
                        <p class="text-xl font-bold text-stone-900 font-serif" x-text="ageAdResult"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function calendarApp() {
            return {
                calendar: @json($calendar),
                currentYear: {{ $year }},
                currentMonthIndex: 0,
                todayDateText: 'Loading...',
                daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],

                // Today's BS date
                todayBsYear: null,
                todayBsMonth: null,
                todayBsDay: null,

                // Form data
                bsYear: '',
                bsMonth: '',
                bsDay: '',
                adYear: '',
                adMonth: '',
                adDay: '',
                ageBsYear: '',
                ageBsMonth: '',
                ageBsDay: '',
                ageAdYear: '',
                ageAdMonth: '',
                ageAdDay: '',

                // Results
                bsToAdResult: '',
                adToBsResult: '',
                ageBsResult: '',
                ageAdResult: '',

                get currentMonthName() {
                    return Object.keys(this.calendar)[this.currentMonthIndex] || '';
                },

                get currentMonthDays() {
                    const monthName = this.currentMonthName;
                    return this.calendar[monthName] || [];
                },

                getCalendarGrid() {
                    const days = this.currentMonthDays;
                    if (!days.length) return [];

                    // Map day names to indices (Sun=0, Mon=1, ..., Sat=6)
                    const dayMap = {
                        'Sunday': 0, 'Sun': 0,
                        'Monday': 1, 'Mon': 1,
                        'Tuesday': 2, 'Tue': 2,
                        'Wednesday': 3, 'Wed': 3,
                        'Thursday': 4, 'Thu': 4,
                        'Friday': 5, 'Fri': 5,
                        'Saturday': 6, 'Sat': 6
                    };

                    // Get the first day of the month
                    const firstDayName = days[0].day;
                    const firstDayIndex = dayMap[firstDayName] || 0;

                    // Create empty cells for days before the first day
                    const grid = [];
                    for (let i = 0; i < firstDayIndex; i++) {
                        grid.push({ day: null });
                    }

                    // Add all the days of the month
                    days.forEach(day => {
                        grid.push({ day: day });
                    });

                    return grid;
                },

                prevMonth() {
                    if (this.currentMonthIndex > 0) {
                        this.currentMonthIndex--;
                    }
                },

                nextMonth() {
                    if (this.currentMonthIndex < Object.keys(this.calendar).length - 1) {
                        this.currentMonthIndex++;
                    }
                },

                isToday(day) {
                    if (!day || !this.todayBsYear || !this.todayBsMonth || !this.todayBsDay) {
                        return false;
                    }

                    // Check if this day matches today's BS date
                    const monthIndex = this.currentMonthIndex + 1; // Month index starts at 0, BS months start at 1
                    return this.currentYear === this.todayBsYear &&
                           monthIndex === this.todayBsMonth &&
                           day.date === this.todayBsDay;
                },

                async getTodayDate() {
                    this.todayDateText = 'Loading...';
                    try {
                        const res = await fetch('{{ url('/api/todaydate') }}');
                        const data = await res.json();
                        if (data.success) {
                            this.todayDateText = `AD: ${data.ad.formatted} | BS: ${data.bs.formatted}`;

                            // Store today's BS date for highlighting
                            this.todayBsYear = data.bs.year;
                            this.todayBsMonth = data.bs.month;
                            this.todayBsDay = data.bs.day;

                            // Auto-navigate to current month if we're viewing the current year
                            if (this.currentYear === this.todayBsYear) {
                                this.currentMonthIndex = this.todayBsMonth - 1;
                            }
                        } else {
                            this.todayDateText = 'Failed to fetch date';
                        }
                    } catch (e) {
                        this.todayDateText = 'Error fetching date';
                        console.error(e);
                    }
                },

                async convertBsToAd() {
                    if (!this.bsYear || !this.bsMonth || !this.bsDay) {
                        return alert('Please fill all BS date fields');
                    }
                    this.bsToAdResult = 'Converting...';
                    try {
                        const res = await fetch(`{{ url('/api/bs-to-ad') }}/${this.bsYear}/${this.bsMonth}/${this.bsDay}`);
                        const data = await res.json();
                        if (data.success) {
                            this.bsToAdResult = `${data.result.year}-${String(data.result.month).padStart(2, '0')}-${String(data.result.day).padStart(2, '0')}`;
                        } else {
                            this.bsToAdResult = 'Conversion failed';
                        }
                    } catch (e) {
                        this.bsToAdResult = 'Error';
                        console.error(e);
                    }
                },

                async convertAdToBs() {
                    if (!this.adYear || !this.adMonth || !this.adDay) {
                        return alert('Please fill all AD date fields');
                    }
                    this.adToBsResult = 'Converting...';
                    try {
                        const res = await fetch(`{{ url('/api/ad-to-bs') }}/${this.adYear}/${this.adMonth}/${this.adDay}`);
                        const data = await res.json();
                        if (data.success) {
                            this.adToBsResult = `${data.result.year}-${String(data.result.month).padStart(2, '0')}-${String(data.result.day).padStart(2, '0')}`;
                        } else {
                            this.adToBsResult = 'Conversion failed';
                        }
                    } catch (e) {
                        this.adToBsResult = 'Error';
                        console.error(e);
                    }
                },

                async calculateAgeBs() {
                    if (!this.ageBsYear || !this.ageBsMonth || !this.ageBsDay) {
                        return alert('Please fill all birthdate fields');
                    }
                    this.ageBsResult = 'Calculating...';
                    try {
                        const res = await fetch(`{{ url('/api/calculateage/bs') }}/${this.ageBsYear}/${this.ageBsMonth}/${this.ageBsDay}`);
                        const data = await res.json();
                        if (data.success) {
                            this.ageBsResult = data.age.formatted;
                        } else {
                            this.ageBsResult = 'Calculation failed';
                        }
                    } catch (e) {
                        this.ageBsResult = 'Error';
                        console.error(e);
                    }
                },

                async calculateAgeAd() {
                    if (!this.ageAdYear || !this.ageAdMonth || !this.ageAdDay) {
                        return alert('Please fill all birthdate fields');
                    }
                    this.ageAdResult = 'Calculating...';
                    try {
                        const res = await fetch(`{{ url('/api/calculateage/ad') }}/${this.ageAdYear}/${this.ageAdMonth}/${this.ageAdDay}`);
                        const data = await res.json();
                        if (data.success) {
                            this.ageAdResult = data.age.formatted;
                        } else {
                            this.ageAdResult = 'Calculation failed';
                        }
                    } catch (e) {
                        this.ageAdResult = 'Error';
                        console.error(e);
                    }
                },

                init() {
                    this.getTodayDate();
                }
            }
        }
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
@endsection
