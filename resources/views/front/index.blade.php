@extends('front.layouts.app')

@section('title', 'NEXO.GLOBAL · Shaping Nepal’s Digital Future')

@section('content')

<!-- ================= HERO ================= -->
<section class="relative min-h-[85vh] flex items-center justify-center px-6">

    <!-- Background Symbol -->
    <div class="absolute inset-0 opacity-[0.04] flex items-center justify-center pointer-events-none">
        <i class="fa-solid fa-atom text-[60rem] animate-slow-spin"></i>
    </div>

    <div class="relative z-10 max-w-5xl text-center space-y-10">

        <span class="inline-block text-[11px] uppercase tracking-[0.5em] text-[#c4b5fd] font-semibold">
            Building Nepal’s Digital Tomorrow
        </span>

        <h1 class="text-5xl md:text-7xl font-serif font-bold tracking-tight leading-tight">
            Technology that <br class="hidden md:block">
            <span class="italic">understands</span> where it lives.
        </h1>

        <p class="text-lg md:text-xl text-[#4a4a4a] max-w-3xl mx-auto leading-relaxed">
            NEXA.Global is a people-first digital ecosystem designed for Nepal —
            respecting geography, culture, and community while unlocking
            modern innovation.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4 pt-6">
            <a href="#systems"
               class="px-10 py-4 rounded-full bg-[#2a1b18] text-white uppercase tracking-widest text-xs font-semibold hover:-translate-y-1 transition">
                Explore the Platform
            </a>
            <a href="#vision"
               class="px-10 py-4 rounded-full border border-[#c4b5fd] text-xs uppercase tracking-widest font-semibold hover:bg-[#f1eefc] transition">
                Our Vision
            </a>
        </div>

    </div>
</section>

<!-- ================= VISION ================= -->
<section id="vision" class="max-w-7xl mx-auto px-6 py-32 grid lg:grid-cols-2 gap-20 items-center">

    <div class="space-y-8">
        <h2 class="text-4xl md:text-5xl font-serif font-bold">
            Innovation, rooted in Nepal.
        </h2>

        <p class="text-lg text-[#4a4a4a] leading-relaxed">
            From the plains of the Terai to the Himalayan highlands,
            digital systems must adapt — not demand adaptation.
        </p>

        <p class="text-lg text-[#4a4a4a] leading-relaxed">
            We design technology that works with people,
            languages, infrastructure, and realities of Nepal.
        </p>
    </div>

    <div class="relative rounded-[3rem] overflow-hidden shadow-2xl">
        <img src="https://images.unsplash.com/photo-1544735716-392fe2489ffa?q=80&w=900"
             class="w-full h-full object-cover">
    </div>

</section>

<!-- ================= SYSTEMS ================= -->
<section id="systems" class="bg-[#f1efe9] py-32 px-6">

    <div class="max-w-7xl mx-auto text-center mb-20">
        <h2 class="text-4xl md:text-5xl font-serif font-bold">
            An Integrated National Ecosystem
        </h2>
        <p class="mt-4 text-lg text-[#4a4a4a]">
            Built module by module — connected by purpose.
        </p>
    </div>

    <div class="max-w-7xl mx-auto grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

        @php
            $systems = [
                ['Education','Smarter learning for every child','fa-graduation-cap'],
                ['Health','Accessible, predictive care','fa-heart-pulse'],
                ['Agriculture','Empowering farmers with data','fa-leaf'],
                ['Mobility','Reliable logistics across terrain','fa-mountain-sun'],
            ];
        @endphp

        @foreach($systems as $sys)
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:-translate-y-2 transition text-left">
                <i class="fa-solid {{ $sys[2] }} text-3xl text-[#c4b5fd]"></i>
                <h3 class="mt-6 text-xl font-serif font-bold">{{ $sys[0] }}</h3>
                <p class="mt-2 text-[#4a4a4a]">
                    {{ $sys[1] }}
                </p>
            </div>
        @endforeach

    </div>
</section>

@endsection
