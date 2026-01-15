@extends('layouts.app')

@section('content')
<div x-data="agroApp()" x-init="init()" class="space-y-16 pb-32 text-stone-900 w-full">

    <!-- Navigation Tabs -->
    <nav class="flex bg-white/80 backdrop-blur-2xl p-2 rounded-[2.5rem] border border-stone-200 w-fit shadow-2xl mx-auto sticky top-6 z-40">
        <template x-for="(tab, idx) in tabs" :key="idx">
            <button
                @click="currentTab = tab.id"
                :class="currentTab === tab.id ? 'bg-stone-900 text-white shadow-xl' : 'text-stone-500 hover:text-stone-900'"
                class="px-8 md:px-12 py-3.5 rounded-[2rem] text-[10px] md:text-[11px] font-black uppercase tracking-[0.2em] transition-all">
                <span x-text="tab.label"></span>
            </button>
        </template>
    </nav>

    <!-- Header -->
    <header class="border-b border-stone-200 pb-12 flex flex-col md:flex-row justify-between items-end gap-10">
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <span class="h-[2px] w-16 bg-orange-600"></span>
                <span class="text-[11px] font-black uppercase tracking-[0.6em] text-orange-600">Himalayan Genetic Ledger</span>
            </div>
            <h2 class="text-7xl md:text-9xl font-serif italic font-bold tracking-tighter text-stone-900 leading-[0.85]">
                Nexo<span class="text-orange-600">.Agro</span>
            </h2>
        </div>
        <div class="flex flex-col items-end gap-4">
            <p class="max-w-md text-base text-stone-500 font-medium italic leading-relaxed text-right opacity-80">
                Verifying molecular botanical elements for Himalayan growth.
            </p>
            <div x-show="currentTab === 'seeds'" class="relative w-full max-w-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-stone-300"></i>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Search Registry..."
                    class="w-full pl-14 pr-6 py-4 bg-stone-50 border border-stone-200 rounded-2xl text-[12px] font-bold uppercase tracking-widest focus:border-orange-600 outline-none transition-all placeholder:text-stone-300"
                />
            </div>
        </div>
    </header>

    <!-- Seeds Tab -->
    <div x-show="currentTab === 'seeds'" class="space-y-16">
        <div x-show="loading" class="py-48 text-center space-y-8">
            <div class="h-24 w-24 border-[4px] border-orange-600 border-t-transparent rounded-full animate-spin mx-auto shadow-2xl"></div>
            <p class="text-[12px] font-black uppercase tracking-[0.8em] text-stone-400 italic">Accessing Genetic Archive...</p>
        </div>

        <div x-show="!loading" class="periodic-grid animate-reveal px-2">
            <template x-for="(plant, idx) in filteredPlants" :key="idx">
                <button
                    x-show="plant"
                    @click="selectPlant(plant)"
                    class="periodic-cell flex flex-col p-3.5 border border-stone-200 rounded-2xl text-left bg-white group hover:border-orange-600 shadow-sm transition-all">
                    <div class="flex justify-between items-start mb-auto">
                        <span class="text-[8px] font-black text-stone-300" x-text="'#' + (idx + 1)"></span>
                        <div class="h-2 w-2 rounded-full bg-orange-400"></div>
                    </div>
                    <div class="text-2xl md:text-3xl font-serif font-black italic tracking-tighter text-stone-900 group-hover:text-orange-600 transition-colors" x-text="getSymbol(plant.name)"></div>
                    <div class="text-[8px] font-black uppercase tracking-tighter truncate text-stone-500 group-hover:text-stone-900" x-text="plant.name"></div>
                </button>
            </template>
        </div>
    </div>

    <!-- Analysis Tab -->
    <div x-show="currentTab === 'analysis'" class="grid grid-cols-1 lg:grid-cols-2 gap-12 animate-reveal">
        <!-- Input Form -->
        <div class="bg-white border border-stone-200 p-12 md:p-20 rounded-[4rem] shadow-xl space-y-12">
            <header class="space-y-4">
                <span class="text-[10px] font-black uppercase tracking-[0.5em] text-orange-600">Cognitive Scanning</span>
                <h3 class="text-4xl md:text-6xl font-serif italic font-bold tracking-tighter text-stone-900 leading-tight">
                    Field<br/>Intelligence.
                </h3>
                <p class="text-base text-stone-500 font-medium italic">Analyze crop performance based on location and climate.</p>
            </header>

            <div class="space-y-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-stone-400 ml-2">Location</label>
                    <input
                        type="text"
                        x-model="analysisLocation"
                        placeholder="e.g. Dhulikhel"
                        class="w-full bg-stone-50 border border-stone-100 p-6 rounded-[2rem] font-bold text-xl italic outline-none focus:border-orange-600 transition-all"
                    />
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-stone-400 ml-2">Crop</label>
                    <input
                        type="text"
                        x-model="analysisCrop"
                        placeholder="e.g. Wheat"
                        class="w-full bg-stone-50 border border-stone-100 p-6 rounded-[2rem] font-bold text-xl italic outline-none focus:border-orange-600 transition-all"
                    />
                </div>
                <button
                    @click="runAnalysis()"
                    :disabled="analyzing"
                    class="w-full py-8 bg-stone-900 text-white rounded-[2rem] font-black uppercase tracking-[0.3em] text-[11px] hover:bg-orange-600 disabled:bg-stone-400 transition-all shadow-2xl flex items-center justify-center gap-6">
                    <span x-show="!analyzing"><i class="fa-solid fa-radar"></i> RUN SCAN</span>
                    <span x-show="analyzing"><i class="fa-solid fa-dna fa-spin"></i></span>
                </button>
            </div>
        </div>

        <!-- Results -->
        <div class="relative">
            <div x-show="analysisResult" class="bg-stone-900 text-white p-12 md:p-20 rounded-[4rem] shadow-2xl space-y-12 animate-slideUp">
                <div class="flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-[0.5em] text-orange-600">Scan Results</span>
                        <h4 class="text-4xl font-serif italic font-bold text-white uppercase tracking-tighter" x-text="analysisCrop + ' @ ' + analysisLocation"></h4>
                    </div>
                    <div class="h-16 w-16 rounded-2xl bg-white/10 flex items-center justify-center text-orange-600 text-2xl shadow-xl">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white/5 p-8 rounded-[2rem] border border-white/10">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-3">Suitability</p>
                        <p class="text-xl font-serif italic font-bold" x-text="analysisResult?.suitability || 'Analyzing...'"></p>
                    </div>
                    <div class="bg-white/5 p-8 rounded-[2rem] border border-white/10">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-3">Best Variant</p>
                        <p class="text-xl font-serif italic font-bold" x-text="analysisResult?.bestVariety || 'Processing...'"></p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="space-y-3">
                        <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Soil Protocol</p>
                        <p class="text-xl italic font-serif leading-relaxed opacity-90" x-text="analysisResult?.soilTips || 'Analyzing soil parameters...'"></p>
                    </div>
                    <div class="space-y-3">
                        <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Climatic Mitigation</p>
                        <p class="text-xl italic font-serif leading-relaxed opacity-90" x-text="analysisResult?.climateRisk || 'Analyzing climate patterns...'"></p>
                    </div>
                </div>
            </div>

            <div x-show="!analysisResult" class="h-full min-h-[500px] border-2 border-dashed border-stone-200 rounded-[4rem] flex flex-col items-center justify-center text-center p-12 space-y-6 opacity-30 grayscale">
                <i class="fa-solid fa-microchip text-7xl text-stone-200"></i>
                <p class="text-xs font-black uppercase tracking-[0.3em] font-serif italic">Waiting for field parameters...</p>
            </div>
        </div>
    </div>

    <!-- Diseases Tab -->
    <div x-show="currentTab === 'diseases'" class="space-y-16 animate-reveal">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <div x-show="diseases.length === 0" class="col-span-full py-32 text-center opacity-40">
                <i class="fa-solid fa-shield-virus text-6xl mb-6"></i>
                <p class="text-xs font-black uppercase tracking-widest">No diseases available.</p>
            </div>

            <template x-for="(disease, idx) in diseases" :key="idx">
                <button
                    @click="selectDisease(disease)"
                    class="bg-white border border-stone-200 p-8 rounded-[3rem] text-left hover:border-rose-500 transition-all group flex flex-col justify-between aspect-square shadow-sm">
                    <div class="space-y-3">
                        <span class="text-[9px] font-black text-stone-300 uppercase tracking-widest" x-text="'ID: ' + disease.id"></span>
                        <h4 class="text-3xl font-serif italic font-bold text-stone-900 leading-tight group-hover:text-rose-600 transition-colors" x-text="disease.name"></h4>
                    </div>
                    <div class="pt-8 flex justify-between items-center border-t border-stone-50 mt-8">
                        <span class="text-[9px] font-black text-stone-400 uppercase tracking-widest">Scientific Spec</span>
                        <i class="fa-solid fa-arrow-right text-stone-200 group-hover:text-rose-600 group-hover:translate-x-1 transition-all"></i>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="analyzing" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 rounded-2xl">
        <div class="bg-white p-8 rounded-[2rem] shadow-2xl">
            <div class="h-12 w-12 border-4 border-orange-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-xs font-black uppercase tracking-[0.2em] italic text-center">Analyzing...</p>
        </div>
    </div>

    <!-- Plant Detail Overlay -->
    <div x-show="selectedPlant || detailLoading" class="fixed inset-0 z-[100] bg-black backdrop-blur-3xl overflow-y-auto" @click.self="selectedPlant = null">
        <!-- Loading Bar -->
        <div x-show="detailLoading" class="fixed top-0 left-0 right-0 z-[120] h-1 bg-gradient-to-r from-orange-600 via-orange-400 to-orange-600 animate-pulse" style="animation: slideIn 0.6s ease-in-out infinite;"></div>

        <button @click="selectedPlant = null" class="fixed top-10 right-10 h-16 w-16 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white hover:bg-orange-600 transition-all z-[110] shadow-2xl">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <div x-show="!detailLoading" class="w-full max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-20 px-6 py-24 items-start lg:items-center animate-slideUp">
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative h-72 w-72 md:h-[32rem] md:w-[32rem]">
                    <div class="absolute inset-0 border border-white/10 rounded-full animate-[spin_25s_linear_infinite]"></div>
                    <div class="absolute inset-28 bg-white rounded-full overflow-hidden border-[15px] border-white/5 shadow-[0_0_120px_rgba(255,255,255,0.15)] group">
                        <template x-if="selectedPlant && selectedPlant.default_image">
                            <img :src="selectedPlant.default_image.original_url" class="w-full h-full object-cover group-hover:scale-125 transition-transform duration-[6s]" :alt="detailTitle()">
                        </template>
                        <template x-if="!selectedPlant || !selectedPlant.default_image">
                            <div class="h-full w-full flex items-center justify-center text-stone-900 text-8xl font-serif font-black italic" x-text="getSymbol(detailTitle())"></div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-12 text-white text-left">
                <header class="space-y-6">
                    <div class="flex items-center gap-6">
                        <span class="px-6 py-2 bg-orange-600 rounded-full text-[11px] font-black uppercase tracking-widest shadow-xl">Molecular ID: <span x-text="selectedPlant?.id"></span></span>
                        <span class="text-white/40 text-sm font-serif italic border-l border-white/20 pl-6 uppercase tracking-widest" x-text="selectedPlant?.cycle + ' Protocol'"></span>
                    </div>
                    <div class="space-y-6">
                        <h3 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-serif italic font-bold tracking-tighter leading-tight text-white break-words" x-text="detailTitle()"></h3> 
                        <p x-show="nepaliInfo && nepaliInfo.nepaliName" class="text-3xl sm:text-4xl md:text-4xl lg:text-5xl text-orange-500 font-bold font-serif opacity-90 break-words" x-text="nepaliInfo && (nepaliInfo.nepaliName || '')"></p>
                    </div>
                    <div class="space-y-3">
                        <p class="text-2xl md:text-3xl text-white/40 font-serif italic font-medium" x-text="(selectedPlant && selectedPlant.scientific_name && Array.isArray(selectedPlant.scientific_name) ? selectedPlant.scientific_name[0] : (selectedPlant && (selectedPlant.scientific_name || '')))"></p>
                    </div>
                </header>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div class="bg-white/5 p-10 rounded-[3rem] border border-white/10 hover:bg-white/20 hover:shadow-[0_20px_40px_rgba(234,179,8,0.2)] hover:-translate-y-2 transition-all duration-300 cursor-default group">
                        <i class="fa-solid fa-droplet text-orange-600 mb-4 block text-lg group-hover:scale-110 transition-transform"></i>
                        <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-2">Watering Protocol</p>
                        <p class="text-2xl font-bold italic font-serif capitalize text-white" x-text="selectedPlant && (selectedPlant.watering || 'Standard')"></p>
                    </div>
                    <div class="bg-white/5 p-10 rounded-[3rem] border border-white/10 hover:bg-white/20 hover:shadow-[0_20px_40px_rgba(234,179,8,0.2)] hover:-translate-y-2 transition-all duration-300 cursor-default group">
                        <i class="fa-solid fa-sun text-orange-600 mb-4 block text-lg group-hover:scale-110 transition-transform"></i>
                        <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-2">Luminance Needs</p>
                        <p class="text-2xl font-bold italic font-serif capitalize text-white" x-text="(selectedPlant && selectedPlant.sunlight && Array.isArray(selectedPlant.sunlight) ? selectedPlant.sunlight[0] : (selectedPlant && (selectedPlant.sunlight || 'Standard'))) "></p>
                    </div>
                    <div class="bg-white/5 p-10 rounded-[3rem] border border-white/10 hover:bg-white/20 hover:shadow-[0_20px_40px_rgba(234,179,8,0.2)] hover:-translate-y-2 transition-all duration-300 cursor-default group">
                        <i class="fa-solid fa-wrench text-orange-600 mb-4 block text-lg group-hover:scale-110 transition-transform"></i>
                        <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-2">Maintenance Level</p>
                        <p class="text-2xl font-bold italic font-serif capitalize text-white" x-text="selectedPlant && (selectedPlant.maintenance || 'Standard')"></p>
                    </div>
                </div>

                <div class="bg-white/5 p-14 rounded-[4rem] border border-white/10 space-y-10 relative overflow-hidden group">
                    <div class="flex items-center gap-6 relative z-10">
                        <div class="h-10 w-10 rounded-xl bg-orange-600 flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-map-location-dot text-white"></i>
                        </div>
                        <h5 class="text-[12px] font-black uppercase tracking-[0.5em] text-white/60">Himalayan Adaptability</h5>
                    </div>
                    <div class="space-y-10 relative z-10">
                        <p class="text-2xl md:text-4xl text-white font-serif italic leading-relaxed border-l-4 border-orange-600 pl-10" x-text="nepaliInfo?.guide || 'No localized guide available.'"></p>
                        <div class="flex flex-wrap gap-12 pt-8 border-t border-white/10">
                            <div>
                               <p class="text-[11px] font-black text-orange-600 uppercase tracking-widest mb-2">Target Altitude</p>
                               <p class="text-5xl font-serif font-black italic text-white" x-text="nepaliInfo?.altitude || 'Varies'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="flex flex-col sm:flex-row gap-8 pt-6">
                    <button class="flex-1 py-8 bg-white text-stone-900 rounded-[2.5rem] text-[12px] font-black uppercase tracking-widest hover:bg-orange-600 hover:text-white hover:shadow-[0_20px_40px_rgba(255,255,255,0.2)] hover:-translate-y-1 transition-all active:scale-95 shadow-2xl">Synchronize Field Asset</button>
                    <button class="flex-1 py-8 border-2 border-white/20 text-white rounded-[2.5rem] text-[12px] font-black uppercase tracking-widest hover:bg-white/10 hover:shadow-[0_20px_40px_rgba(255,255,255,0.1)] hover:-translate-y-1 transition-all active:scale-95">Export Registry Spec</button>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Disease Detail Overlay - High Contrast -->
    <div x-show="selectedDisease" class="fixed inset-0 z-[100] bg-black backdrop-blur-3xl overflow-y-auto" @click.self="selectedDisease = null">
       <button @click="selectedDisease = null" class="fixed top-10 right-10 h-16 w-16 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white hover:bg-rose-600 transition-all z-[110] shadow-2xl">
          <i class="fa-solid fa-xmark text-2xl"></i>
       </button>

       <div class="w-full max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 px-6 py-24 items-start animate-slideUp">
          <div class="space-y-10">
             <div class="relative aspect-square rounded-[4rem] overflow-hidden border border-white/10 shadow-3xl group bg-rose-950/20 flex items-center justify-center">
                <template x-if="selectedDisease && selectedDisease.images && selectedDisease.images.length">
                   <img :src="selectedDisease.images[0].original_url || selectedDisease.images[0].thumbnail" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[5s]" :alt="selectedDisease.common_name">
                </template>
                <template x-if="!selectedDisease || !selectedDisease.images || !selectedDisease.images.length">
                   <i class="fa-solid fa-skull-crossbones text-9xl text-rose-500/10"></i>
                </template>
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                <div class="absolute bottom-12 left-12 space-y-2">
                   <span class="text-[10px] font-black uppercase tracking-[0.4em] text-rose-500">Hazard Trace</span>
                   <h3 class="text-3xl sm:text-4xl md:text-5xl font-serif italic font-bold tracking-tighter break-words" x-text="selectedDisease.common_name"></h3>
                </div>
             </div>

                <div class="bg-white/5 p-12 rounded-[3.5rem] border border-white/10 space-y-6">
                    <h5 class="text-[10px] font-black uppercase tracking-widest text-white/40">Biological Ledger</h5>
                    <div class="space-y-4">
                       <div class="flex justify-between border-b border-white/5 pb-4">
                          <span class="text-[10px] font-black uppercase tracking-widest text-stone-600">Genetic Spec</span>
                          <span class="font-serif italic text-lg" x-text="selectedDisease?.scientific_name || 'Unknown'"></span>
                       </div>
                       <div class="flex justify-between border-b border-white/5 pb-4">
                          <span class="text-[10px] font-black uppercase tracking-widest text-stone-600">Family Node</span>
                          <span class="font-serif italic text-lg" x-text="selectedDisease?.family || 'Undefined'"></span>
                       </div>
                    </div>
                 </div>
          </div>

          <div class="space-y-12">
             <header class="space-y-6">
                <span class="px-6 py-2 bg-rose-600 rounded-full text-[11px] font-black uppercase tracking-widest">Target Pathology</span>
                <h3 class="text-4xl sm:text-5xl md:text-6xl font-serif italic font-bold tracking-tighter leading-tight break-words">Neutralization <br /><span class="text-rose-600">& Countermeasures.</span></h3>
             </header>

             <div class="space-y-10">
                <template x-for="(desc, i) in (selectedDisease?.description || [])" :key="i">
                   <div class="space-y-4 border-l-2 border-rose-500 pl-10 group">
                      <h6 class="text-[11px] font-black uppercase tracking-[0.3em] text-rose-500" x-text="desc.subtitle"></h6>
                      <p class="text-xl font-serif italic text-white leading-relaxed" x-text="desc.description"></p> 
                   </div>
                </template>
                <div x-show="!(selectedDisease?.description && selectedDisease.description.length)" class="bg-white/5 p-10 rounded-[2.5rem] border border-white/10 italic text-white/40 font-serif">
                   Searching global pathogen registry for specific countermeasures...
                </div>
             </div>

             {{-- <div class="pt-10">
                <button class="w-full py-7 bg-rose-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-widest hover:bg-white hover:text-rose-600 hover:shadow-[0_20px_40px_rgba(225,29,72,0.3)] hover:-translate-y-1 transition-all shadow-2xl">REQUEST FIELD INTERVENTION</button>
             </div> --}}
          </div>
       </div>
    </div>
</div>

<script>
function agroApp() {
    return {
        tabs: [
            { id: 'seeds', label: 'Botanical Matrix' },
            { id: 'analysis', label: 'Field Scanner' },
            { id: 'diseases', label: 'Pathogen DB' }
        ],
        currentTab: 'seeds',
        searchQuery: '',
        loading: false,
        analyzing: false,
        analysisLocation: '',
        analysisCrop: '',
        analysisResult: null,
        plants: [],
        diseases: [],
        // Detail states
        selectedPlant: null,
        selectedDisease: null,
        nepaliInfo: null,
        detailLoading: false,

        async init() {
            console.log('Agro app initializing...');
            this.loading = true;
            try {
                // Fetch plants
                console.log('Fetching plants from {{ route("user.agro.plants") }}');
                const plantsRes = await fetch("{{ route('user.agro.plants') }}?page=1", {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                console.log('Plants response status:', plantsRes.status);

                if (plantsRes.ok) {
                    const plantsData = await plantsRes.json();
                    console.log('Plants data:', plantsData);
                    this.plants = Array.isArray(plantsData) ? plantsData : (plantsData.data || plantsData.results || []);
                } else {
                    console.error('Plants API error:', plantsRes.status);
                    this.plants = [];
                }

                // Fetch diseases
                console.log('Fetching diseases from {{ route("user.agro.diseases") }}');
                const diseasesRes = await fetch("{{ route('user.agro.diseases') }}", {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                console.log('Diseases response status:', diseasesRes.status);

                if (diseasesRes.ok) {
                    const diseasesData = await diseasesRes.json();
                    console.log('Diseases data:', diseasesData);
                    this.diseases = Array.isArray(diseasesData) ? diseasesData : (diseasesData.data || diseasesData.results || []);
                } else {
                    console.error('Diseases API error:', diseasesRes.status);
                    this.diseases = [];
                }

                console.log('Init complete. Plants:', this.plants.length, 'Diseases:', this.diseases.length);
            } catch (error) {
                console.error('Error loading data:', error);
                this.plants = [];
                this.diseases = [];
            } finally {
                this.loading = false;
            }
        },

        get filteredPlants() {
            return this.plants.filter(p =>
                p.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },

        getSymbol(name) {
            if (!name) return 'Pl';
            const parts = name.split(' ');
            if (parts.length > 1) return (parts[0][0] + parts[1][0]).toUpperCase();
            return name.substring(0, 2).toUpperCase();
        },

        // Returns the best available title for a plant (common name -> scientific name -> list name)
        detailTitle() {
            const p = this.selectedPlant;
            if (!p) return '';
            if (p.common_name) return p.common_name;
            if (p.scientific_name) {
                if (Array.isArray(p.scientific_name)) return p.scientific_name[0];
                return p.scientific_name;
            }
            return p.name || '';
        },

        async selectPlant(plant) {
            console.log('Selecting plant:', plant);
            this.detailLoading = true;
            // show minimal plant info immediately while we fetch details
            this.selectedPlant = plant;
            this.nepaliInfo = null;
            try {
                const res = await fetch(`{{ url('/agro/plant') }}/${plant.id}`, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });

                if (!res.ok) {
                    const text = await res.text();
                    console.error('Failed to fetch plant details', res.status, text);
                    alert('Failed to load plant details from server (status: ' + res.status + ').');
                    this.detailLoading = false;
                    return;
                }

                const details = await res.json();
                // Merge the detailed data with the initial plant object so UI updates smoothly
                this.selectedPlant = Object.assign({}, this.selectedPlant || {}, details || {});

                // fetch nepali/localized info
                const trans = await fetch("{{ route('user.agro.translate') }}?name=" + encodeURIComponent(plant.name), {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (trans.ok) {
                    this.nepaliInfo = await trans.json();
                } else {
                    this.nepaliInfo = null;
                }
            } catch (e) {
                console.error('Plant detail error', e);
                alert('Error loading plant details');
                // keep the minimal plant info so users can still see something
                this.nepaliInfo = null;
            } finally {
                this.detailLoading = false;
            }
        }, 

        selectDisease(disease) {
            console.log('Selected disease:', disease);
            this.selectedDisease = disease;
        },

        async runAnalysis() {
            if (!this.analysisLocation || !this.analysisCrop) {
                alert('Please enter location and crop');
                return;
            }

            this.analyzing = true;
            try {
                const res = await fetch("{{ route('user.agro.analyze') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        location: this.analysisLocation,
                        crop: this.analysisCrop
                    })
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                this.analysisResult = await res.json();
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            } finally {
                this.analyzing = false;
            }
        }
    }
}
</script>

<style>
    .periodic-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
    }

    .periodic-cell {
        aspect-ratio: 1;
    }

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

    .animate-reveal {
        animation: fadeIn 0.3s ease-in;
    }

    .animate-slideUp {
        animation: slideUp 0.5s ease-out;
    }

    @keyframes revealbounce {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-revealBounce {
        animation: revealbounce 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes slideIn {
        0%, 100% { width: 0%; }
        50% { width: 100%; }
    }
</style>

@endsection
