@php
    $role = auth()->user()->role ?? 'user';
    $currentRoute = Route::currentRouteName();
@endphp

<aside class="fixed left-0 top-0 z-50 h-full w-72 bg-white/95 backdrop-blur-xl border-r border-stone-200 shadow-2xl">

    <!-- Animated Background Blobs -->
    <div class="absolute inset-0 overflow-hidden opacity-20 pointer-events-none">
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-orange-200 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
        <div class="absolute top-1/3 -right-12 w-48 h-48 bg-stone-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-24 left-1/4 w-48 h-48 bg-orange-100 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <!-- Logo Section -->
    <div class="relative flex h-28 items-center px-8 border-b border-stone-200/60">
        <div class="flex items-center gap-4 w-full">
            <div class="relative">
                <div class="relative h-16 w-16 bg-white rounded-2xl shadow-lg flex items-center justify-center overflow-hidden">
                    <img src="/icon.png" alt="NEXO Logo" class="h-14 w-14 object-contain">
                </div>
            </div>
            <div class="flex-1">
                <div class="text-3xl font-serif font-black italic tracking-tight text-stone-900">
                    NEXO<span class="text-orange-600">.</span>
                </div>
                <div class="text-[8px] font-black text-stone-400 uppercase tracking-[0.3em] mt-0.5">Global Network</div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="relative mt-8 px-6 overflow-y-auto h-[calc(100vh-240px)] custom-scrollbar">
        <p class="px-4 mb-6 text-[9px] font-black text-stone-400 uppercase tracking-[0.4em] flex items-center gap-3">
            <span class="h-px flex-1 bg-gradient-to-r from-transparent via-stone-300 to-transparent"></span>
            <span>Modules</span>
            <span class="h-px flex-1 bg-gradient-to-r from-transparent via-stone-300 to-transparent"></span>
        </p>

        @php
            $modules = [
                'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-grip-vertical'],
                'education' => ['label' => 'EduSync', 'icon' => 'fa-book-open-reader'],
                'travel'    => ['label' => 'Voyage', 'icon' => 'fa-compass'],
                'health'    => ['label' => 'Health', 'icon' => 'fa-heart-pulse'],
                'agro'      => ['label' => 'Agro', 'icon' => 'fa-seedling'],
                'calendar'  => ['label' => 'Calendar', 'icon' => 'fa-calendar-days'],
            ];
        @endphp

        <div class="space-y-2">
            @foreach($modules as $key => $mod)
                @php $isActive = $currentRoute == $role.'.'.$key; @endphp
                <a href="{{ route($role.'.'.$key) }}"
                   class="relative flex items-center gap-4 rounded-2xl px-5 py-4 text-sm font-bold transition-all duration-300 overflow-hidden
                   {{ $isActive ? 'bg-orange-500 text-white shadow-lg' : 'text-stone-700 hover:text-orange-600 hover:bg-orange-50' }}">

                   <!-- Icon -->
                   <div class="relative z-10 flex h-10 w-10 items-center justify-center rounded-xl transition-colors duration-300
                       {{ $isActive ? 'bg-white/20 shadow-md' : 'bg-stone-100 group-hover:bg-orange-100' }}">
                       <i class="fa-solid {{ $mod['icon'] }} text-lg {{ $isActive ? 'text-white' : 'text-stone-700 group-hover:text-orange-600' }}"></i>
                   </div>

                   <!-- Label -->
                   <span class="relative z-10 font-serif font-bold italic text-base tracking-tight">
                       {{ $mod['label'] }}
                   </span>

                   <!-- Active Indicator -->
                   @if($isActive)
                       <div class="absolute right-4 top-1/2 -translate-y-1/2">
                           <div class="h-2 w-2 rounded-full bg-white animate-pulse"></div>
                       </div>
                   @endif
                </a>
            @endforeach
        </div>
    </nav>
</aside>

<style>
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -50px) scale(1.1); }
    50% { transform: translate(-20px, 20px) scale(0.9); }
    75% { transform: translate(50px, 50px) scale(1.05); }
}

.animate-blob {
    animation: blob 7s infinite;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(120, 113, 108, 0.3);
    border-radius: 10px;
    transition: background 0.3s;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(234, 88, 12, 0.5);
}
</style>
