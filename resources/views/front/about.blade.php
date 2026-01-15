@extends('front.layouts.app')

@section('title', 'About · NEXO.GLOBAL')
@section('content')

<section class="max-w-7xl mx-auto px-6 py-28 space-y-24">

    <!-- ================= INTRO ================= -->
    <div class="max-w-3xl space-y-6">
        <span class="text-[11px] uppercase tracking-[0.4em] text-[#c4b5fd] font-semibold block">
            Our Origin
        </span>

        <h1 class="text-4xl md:text-5xl font-serif font-bold italic tracking-tight leading-tight">
            Harmonizing Himalayan <br>
            Modernity.
        </h1>

        <p class="text-lg md:text-xl text-[#4a4a4a] leading-relaxed">
            NEXA was founded on the belief that geography should never limit opportunity.
            We build digital infrastructure that respects Nepal’s terrain, culture,
            and people — while delivering global-grade intelligence.
        </p>
    </div>

    <!-- ================= MISSION / VISION / VALUES ================= -->
    <div class="grid md:grid-cols-3 gap-10">

        <div class="bg-white p-10 rounded-[2.5rem] border border-black/5 shadow-sm hover:shadow-xl transition">
            <i class="fa-solid fa-bullseye text-2xl text-[#c4b5fd] mb-6"></i>
            <h3 class="text-xl font-serif font-bold italic mb-3">Mission</h3>
            <p class="text-[#4a4a4a] leading-relaxed">
                To provide a unified digital ecosystem for education,
                healthcare, and logistics — especially in hard-to-reach regions.
            </p>
        </div>

        <div class="bg-white p-10 rounded-[2.5rem] border border-black/5 shadow-sm hover:shadow-xl transition">
            <i class="fa-solid fa-eye text-2xl text-[#c4b5fd] mb-6"></i>
            <h3 class="text-xl font-serif font-bold italic mb-3">Vision</h3>
            <p class="text-[#4a4a4a] leading-relaxed">
                A Nepal where every student, farmer, traveler,
                and patient is connected to a trusted, intelligent system of care.
            </p>
        </div>

        <div class="bg-white p-10 rounded-[2.5rem] border border-black/5 shadow-sm hover:shadow-xl transition">
            <i class="fa-solid fa-heart text-2xl text-[#c4b5fd] mb-6"></i>
            <h3 class="text-xl font-serif font-bold italic mb-3">Values</h3>
            <p class="text-[#4a4a4a] leading-relaxed">
                Local-first thinking, privacy by design,
                and a relentless pursuit of excellence — even at high altitude.
            </p>
        </div>

    </div>

    <!-- ================= FOUNDERS PROMISE ================= -->
    <div class="bg-[#f5f2eb] rounded-[3rem] p-12 md:p-16 border border-dashed border-[#c4b5fd]">

        <h2 class="text-2xl md:text-3xl font-serif font-bold italic mb-6">
            The Founders’ Promise
        </h2>

        <p class="text-lg text-[#2a1b18] leading-relaxed italic opacity-80 max-w-4xl">
            “NEXA is not just software — it is a commitment to the citizens of Nepal.
            We measure success by the reliability of the grid,
            the clarity of information,
            and the dignity with which technology serves human life.”
        </p>

        <div class="mt-8 flex items-center gap-4">
            <div class="h-[2px] w-12 bg-[#2a1b18]"></div>
            <span class="text-xs uppercase tracking-widest font-semibold italic">
                The NEXA Labs Collective
            </span>
        </div>

    </div>

</section>

@endsection
