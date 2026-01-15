@extends('front.layouts.app')

@section('title', 'Login · NEXO.GLOBAL')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-6 py-16">

    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-12 space-y-8 relative z-10">

        <h2 class="text-3xl font-serif font-bold italic text-center">Welcome Back</h2>

        <form action="{{ route('login') }}" method="POST" class="space-y-6" onsubmit="return startLoginSequence(this)">
            @csrf

            <div>
                <label class="text-xs uppercase tracking-widest opacity-60">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-widest opacity-60">Password</label>
                <input type="password" name="password"
                       class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-between text-sm">
                <label class="flex items-center gap-1">
                    <input type="checkbox" name="remember"> Remember Me
                </label>
                <a href="{{ route('password.request') }}" class="text-[#c4b5fd] font-semibold hover:underline">
                    Forgot Password?
                </a>
            </div>

            <button type="submit"
                    class="w-full bg-[#2a1b18] text-white py-5 rounded-2xl uppercase tracking-widest text-xs font-semibold hover:bg-[#3d2b26] transition">
                Login
            </button>
        </form>

        <p class="text-center text-sm text-[#4a4a4a]">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-[#c4b5fd] font-semibold hover:underline">
                Register
            </a>
        </p>
    </div>
</div>

<!-- ================= ULTRA POLISHED LOADER ================= -->
<div id="loginLoader"
     class="fixed inset-0 z-[9999] flex items-center justify-center opacity-0 pointer-events-none transition-all duration-700">

    <!-- Blur Layer -->
    <div class="absolute inset-0 bg-white/40 backdrop-blur-2xl"></div>

    <!-- Loader Content -->
    <div class="relative flex flex-col items-center gap-6 scale-95 transition-transform duration-700"
         id="loaderContent">

        <!-- Circular Progress -->
        <div class="relative w-64 h-64">
            <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="45"
                        fill="none"
                        stroke="#eee"
                        stroke-width="2" />
                <circle id="progressCircle"
                        cx="50" cy="50" r="45"
                        fill="none"
                        stroke="#f97316"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-dasharray="283"
                        stroke-dashoffset="283" />
            </svg>

            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span id="progressText"
                      class="text-5xl font-serif italic font-bold transition-all">
                    0%
                </span>
                <span class="text-xs tracking-[0.35em] text-orange-600 mt-2">
                    CALIBRATING NODE
                </span>
            </div>
        </div>

        <p class="text-xs tracking-[0.45em] text-stone-400 uppercase">
            Namaste! Preparing your NEXO experience...
        </p>
    </div>
</div>

<!-- ================= SCRIPT ================= -->
<script>
function startLoginSequence(form) {
    const loader = document.getElementById('loginLoader');
    const content = document.getElementById('loaderContent');
    const circle = document.getElementById('progressCircle');
    const text = document.getElementById('progressText');

    let progress = 0;
    const circumference = 283;

    // Show loader
    loader.classList.remove('pointer-events-none', 'opacity-0');
    loader.classList.add('opacity-100');
    content.classList.remove('scale-95');
    content.classList.add('scale-100');

    // Disable submit button
    form.querySelector('button[type="submit"]').disabled = true;

    // Smooth, realistic jumps from 1 → 100 over ~6 seconds
    const totalDuration = 6000; // 6 seconds
    const steps = 120; // number of updates
    const incrementBase = 100 / steps;
    let currentStep = 0;

    const interval = setInterval(() => {
        // Slight random jump for realism
        const randomFactor = Math.random() * 0.5 + 0.75; // 0.75 to 1.25
        progress = Math.min(100, progress + incrementBase * randomFactor);
        currentStep++;

        // Update UI
        text.innerText = Math.floor(progress) + '%';
        circle.style.strokeDashoffset = circumference - (progress / 100) * circumference;

        if (currentStep >= steps || progress >= 100) {
            progress = 100;
            text.innerText = '100%';
            circle.style.strokeDashoffset = 0;
            clearInterval(interval);
            // Submit the form after delay
            form.submit();
        }
    }, totalDuration / steps);

    return false; // prevent immediate form submission
}

// Hide loader on page reload
window.addEventListener('pageshow', () => {
    const loader = document.getElementById('loginLoader');
    if (loader) loader.classList.add('opacity-0', 'pointer-events-none');
});
</script>
@endsection
