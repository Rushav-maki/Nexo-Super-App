@extends('front.layouts.app')

@section('title', 'Register · NEXA.GLOBAL')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-6 py-16">

    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-12 space-y-8">

        <h2 class="text-3xl font-serif font-bold italic text-center">Create Your Account</h2>

        <form action="{{ route('register') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="text-xs uppercase tracking-widest opacity-60">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-widest opacity-60">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-widest opacity-60">Contact</label>
                <input type="text" name="contact" value="{{ old('contact') }}"
                       class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                @error('contact')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-widest opacity-60">Password</label>
                <input type="password" name="password"
                       class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
                @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-widest opacity-60">Confirm Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full mt-2 bg-[#f5f2eb] rounded-2xl px-6 py-4 font-semibold focus:ring-1 focus:ring-[#c4b5fd] border-none">
            </div>

            <button type="submit"
                    class="w-full bg-[#2a1b18] text-white py-5 rounded-2xl uppercase tracking-widest text-xs font-semibold hover:bg-[#3d2b26] transition">
                Register
            </button>

        </form>

        <p class="text-center text-sm text-[#4a4a4a]">
            Already have an account? <a href="{{ route('login') }}" class="text-[#c4b5fd] font-semibold hover:underline">Login</a>
        </p>
    </div>
</div>
@endsection
