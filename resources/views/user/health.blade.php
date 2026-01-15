@extends('layouts.app')

@section('content')
<div x-data="healthApp()" x-init="loadTravelData()" class="space-y-10 pb-20 animate-fadeIn">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-stone-900 font-serif tracking-tighter uppercase italic">
            Nexo<span class="text-purple-400">.Health</span>
        </h2>
    </div>

    <!-- Travel Info Card (Only show if destination exists) -->
    <div x-show="destination" class="bg-stone-900 rounded-[3.5rem] p-12 text-stone-50 shadow-2xl relative overflow-hidden group animate-slideDown">
        <div class="relative z-10">
            <span class="bg-stone-50/10 border border-white/10 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest text-purple-300">Travel Health Tips</span>
            <h3 class="text-3xl font-bold mt-6 uppercase tracking-tighter font-serif italic">Trip to: <span x-text="destination"></span></h3>

            <!-- Loading State -->
            <div x-show="loadingTips" class="mt-12 space-y-4">
                <div class="h-20 bg-white/10 rounded-2xl animate-pulse"></div>
                <div class="h-20 bg-white/10 rounded-2xl animate-pulse"></div>
                <div class="h-20 bg-white/10 rounded-2xl animate-pulse"></div>
            </div>

            <!-- Tips Grid -->
            <div x-show="!loadingTips" class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <template x-for="(tip, idx) in travelTips" :key="idx">
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 p-6 rounded-3xl flex items-center gap-5">
                        <span class="text-3xl" x-text="tip.icon || '✈️'"></span>
                        <p class="text-sm font-medium leading-relaxed italic" x-text="tip.tip || tip.category"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Symptom Checker Section -->
        <div class="lg:col-span-2 space-y-10">
            <div class="bg-white border border-black/5 p-12 rounded-[3.5rem] shadow-xl">
                <h3 class="text-xl font-bold text-stone-900 mb-10 flex items-center gap-5 uppercase tracking-tighter font-serif italic">
                    <i class="fa-solid fa-microscope text-purple-400"></i> Symptom Checker
                </h3>
                <textarea
                    x-model="symptoms"
                    placeholder="Describe how you are feeling...(Please give as much precise detail as possible, including duration, severity, and any other relevant information.)"
                    class="w-full h-56 bg-stone-50 border border-black/5 text-stone-900 rounded-[2.5rem] p-10 focus:ring-1 focus:ring-purple-400 font-medium text-lg resize-none placeholder:text-black/20"
                ></textarea>
                <button
                    type="button"
                    @click="checkSymptoms()"
                    :disabled="loading"
                    class="mt-10 w-full h-[72px] bg-stone-900 hover:bg-purple-600 disabled:bg-stone-400 text-stone-50 rounded-2xl font-black uppercase tracking-widest shadow-xl transition-all flex items-center justify-center gap-4 cursor-pointer">
                    <span x-show="!loading">CHECK SYMPTOMS</span>
                    <span x-show="loading"><i class="fa-solid fa-spinner fa-spin"></i></span>
                </button>
            </div>

            <!-- Results -->
            <div x-show="result" class="bg-white border-2 border-dashed border-purple-400 p-12 rounded-[3.5rem] shadow-2xl animate-slideUp">
                <div class="flex flex-col md:flex-row justify-between items-start gap-8 mb-12">
                    <div>
                        <h4 class="text-3xl font-bold text-stone-900 tracking-tighter font-serif italic" x-text="result?.diagnosis"></h4>
                        <p class="text-purple-600 font-black uppercase tracking-[0.25em] text-[10px] mt-2 italic">Urgency: <span x-text="result?.urgency"></span></p>
                    </div>
                    <div class="bg-stone-50 px-8 py-4 rounded-2xl border border-black/5 shadow-sm">
                        <p class="text-[10px] font-black text-stone-600 uppercase tracking-widest mb-1">Specialist</p>
                        <p class="font-bold text-stone-900 text-sm" x-text="result?.specialist"></p>
                    </div>
                </div>

                <div class="space-y-10">
                    <div>
                        <label class="text-[10px] font-black text-stone-600 uppercase tracking-widest mb-6 block font-serif">Recommended Hospitals</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <template x-for="(hospital, idx) in (result?.hospitals || [])" :key="idx">
                                <div class="bg-stone-50 p-6 rounded-2xl border border-black/5 flex items-center gap-5 hover:border-purple-400 transition-all group">
                                    <div class="h-10 w-10 rounded-xl bg-white text-stone-900 flex items-center justify-center shadow-sm group-hover:bg-stone-900 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-building-h text-base"></i>
                                    </div>
                                    <span class="font-bold text-stone-900 text-sm italic" x-text="hospital"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctors Sidebar -->
        <div class="bg-stone-50 border border-black/5 rounded-[3.5rem] p-12 h-fit space-y-10 shadow-sm">
            <h4 class="font-bold text-xl text-stone-900 uppercase tracking-tighter font-serif italic border-b border-black/10 pb-6">Recommended Doctors</h4>

            <!-- Loading State -->
            <div x-show="result && loadingDoctors" class="space-y-4">
                <div class="h-24 bg-stone-200 rounded-2xl animate-pulse"></div>
                <div class="h-24 bg-stone-200 rounded-2xl animate-pulse"></div>
                <div class="h-24 bg-stone-200 rounded-2xl animate-pulse"></div>
            </div>

            <!-- Doctors List -->
            <div x-show="result && !loadingDoctors" class="space-y-6">
                <template x-for="(doctor, idx) in doctors" :key="idx">
                    <div class="p-6 rounded-2xl bg-white border border-black/5 hover:border-purple-400 transition-all shadow-sm group">
                        <p class="font-bold text-stone-900 text-base group-hover:translate-x-1 transition-transform" x-text="doctor.name || 'Doctor Name'"></p>
                        <p class="text-[11px] font-bold text-stone-600 uppercase mt-2 tracking-tighter italic opacity-70" x-text="(doctor.specialty || 'Specialty') + ' • ' + (doctor.hospital || 'Hospital')"></p>
                        <p class="text-[10px] text-purple-600 font-semibold mt-3" x-text="'Exp: ' + (doctor.experience || 'N/A') + ' | ' + (doctor.availability || 'Available')"></p>
                    </div>
                </template>
            </div>

            <!-- Placeholder when no diagnosis -->
            <div x-show="!result" class="space-y-4">
                <div class="p-6 rounded-2xl bg-white border border-black/5 shadow-sm">
                    <p class="font-bold text-stone-900 text-sm">Check symptoms to get doctor recommendations</p>
                </div>
            </div>

            {{-- <button type="button" class="w-full bg-stone-900 text-white py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-purple-600 transition-all shadow-xl font-serif cursor-pointer">
                ONLINE CONSULTATION
            </button> --}}
        </div>
    </div>
</div>

<script>
function healthApp() {
    return {
        symptoms: '',
        result: null,
        loading: false,
        loadingTips: false,
        loadingDoctors: false,
        destination: '',
        travelTips: [],
        doctors: [],

        loadTravelData() {
            // Get destination from sessionStorage (set by travel module)
            console.log('Loading travel data from sessionStorage...');
            const travelData = sessionStorage.getItem('travel_destination');
            console.log('Travel destination:', travelData);
            if (travelData) {
                this.destination = travelData;
                this.fetchTravelTips();
            }
        },

        async fetchTravelTips() {
            if (!this.destination) return;

            this.loadingTips = true;
            try {
                const res = await fetch("{{ route('user.health.travel-tips') }}?destination=" + encodeURIComponent(this.destination), {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                console.log('Travel tips response:', res.status);
                if (res.ok) {
                    this.travelTips = await res.json();
                    console.log('Travel tips:', this.travelTips);
                }
            } catch (error) {
                console.error('Error fetching travel tips:', error);
            } finally {
                this.loadingTips = false;
            }
        },

        async fetchDoctors() {
            if (!this.result || !this.result.diagnosis) return;

            this.loadingDoctors = true;
            try {
                const res = await fetch("{{ route('user.health.doctors') }}?diagnosis=" + encodeURIComponent(this.result.diagnosis) + "&urgency=" + encodeURIComponent(this.result.urgency || 'Medium'), {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                console.log('Doctors response:', res.status);
                if (res.ok) {
                    this.doctors = await res.json();
                    console.log('Doctors:', this.doctors);
                }
            } catch (error) {
                console.error('Error fetching doctors:', error);
            } finally {
                this.loadingDoctors = false;
            }
        },

        async checkSymptoms() {
            console.log('Check symptoms called with:', this.symptoms);
            if (!this.symptoms.trim()) {
                alert('Please describe your symptoms');
                return;
            }

            this.loading = true;
            try {
                const res = await fetch("{{ route('user.health.check') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        symptoms: this.symptoms
                    })
                });

                console.log('Response status:', res.status);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                this.result = await res.json();
                console.log('Result received:', this.result);

                // Fetch doctors based on diagnosis
                await this.fetchDoctors();
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-in;
    }

    .animate-slideUp {
        animation: slideUp 0.5s ease-out;
    }

    .animate-slideDown {
        animation: slideDown 0.3s ease-out;
    }
</style>

@endsection
