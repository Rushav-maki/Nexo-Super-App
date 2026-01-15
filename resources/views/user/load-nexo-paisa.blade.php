@extends('layouts.app')

@section('content')
<div class="space-y-24 pb-32">

    {{-- Header --}}
    <section class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-12 animate-slide-up">
        <div class="space-y-4">
            <div class="flex items-center gap-3 text-orange-700 font-bold uppercase tracking-widest text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-orange-600 animate-pulse"></span>
                Load Nexo Paisa
            </div>
            <h2 class="text-6xl md:text-8xl font-serif italic font-bold tracking-tight leading-none animate-float">
                Add Funds<span class="text-orange-600">.</span>
            </h2>
            <p class="text-xl md:text-2xl text-stone-600 font-medium max-w-2xl leading-relaxed">
                Load Nexo Paisa from your bank account. 1 NRP = 1 Nexo Paisa.
            </p>
        </div>
    </section>

    {{-- Load Form --}}
    <section class="max-w-2xl mx-auto animate-fade-in">
        <div class="nexo-card p-12 space-y-8 hover:shadow-2xl transition-shadow duration-300">
            <h3 class="text-3xl font-serif font-bold italic">Bank Transfer</h3>

            <form id="loadForm" class="space-y-6">
                @csrf
                <div class="animate-fade-in">
                    <label for="bank" class="block text-sm font-medium text-stone-700 mb-2">Select Bank</label>
                    <select id="bank" name="bank" required
                            class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 hover:border-orange-400">
                        <option value="">Choose your bank</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank }}">{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="animate-fade-in">
                    <label for="amount" class="block text-sm font-medium text-stone-700 mb-2">Amount (NRP)</label>
                    <input type="number" id="amount" name="amount" min="1" max="100000" required
                           class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 hover:border-orange-400"
                           placeholder="Enter amount in NRP">
                    <p class="text-xs text-stone-500 mt-2">1 NRP = 1 Nexo Paisa</p>
                </div>

                <button type="button" id="loadBtn"
                        class="w-full bg-orange-600 text-white py-4 px-6 rounded-lg font-semibold hover:bg-orange-700 transition-all duration-300 hover:scale-105 hover:shadow-lg animate-bounce-in disabled:opacity-50 disabled:cursor-not-allowed transform active:scale-95">
                    <span id="buttonText">Load Nexo Paisa</span>
                    <i class="fa-solid fa-arrow-right ml-2 transition-transform duration-300 group-hover:translate-x-1"></i>
                </button>
            </form>
        </div>
    </section>

</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('loadBtn').addEventListener('click', async function() {
    const bank = document.getElementById('bank').value;
    const amount = document.getElementById('amount').value;
    const button = this;
    const buttonText = document.getElementById('buttonText');

    if (!bank || !amount) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please select a bank and enter an amount.'
        });
        return;
    }

    if (amount < 1 || amount > 100000) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Amount',
            text: 'Amount must be between 1 and 100,000 NRP.'
        });
        return;
    }

    // Check if user is authenticated
    if (!{{ auth()->check() ? 'true' : 'false' }}) {
        Swal.fire({
            icon: 'error',
            title: 'Authentication Required',
            text: 'You need to be logged in to load Nexo Paisa.',
            confirmButtonText: 'Go to Login'
        }).then(() => {
            window.location.href = '{{ route("login") }}';
        });
        return;
    }

    // Confirmation dialog
    const result = await Swal.fire({
        title: 'Confirm Load',
        text: `Load ${amount} NRP from ${bank}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Load Now'
    });

    if (!result.isConfirmed) {
        return;
    }

    // Disable button and show loading
    button.disabled = true;
    buttonText.textContent = 'Processing...';
    button.classList.add('animate-pulse');

    // Show loading
    Swal.fire({
        title: 'Processing Payment...',
        text: 'Please wait while we process your bank transfer.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const formData = new FormData();
        formData.append('bank', bank);
        formData.append('amount', parseFloat(amount));
        formData.append('_token', '{{ csrf_token() }}');

        const url = window.location.origin + '/load-nexo-paisa';
        console.log('Making request to:', url);
        console.log('Form data:', { bank, amount, _token: '{{ csrf_token() }}' });

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'  // Include cookies for session
        });

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        if (response.status === 401) {
            Swal.fire({
                icon: 'error',
                title: 'Session Expired',
                text: 'Your session has expired. Please log in again.',
                confirmButtonText: 'Go to Login'
            }).then(() => {
                window.location.href = '{{ route("login") }}';
            });
            return;
        }

        if (response.status === 419) {
            Swal.fire({
                icon: 'error',
                title: 'CSRF Token Expired',
                text: 'Please refresh the page and try again.',
                confirmButtonText: 'Refresh Page'
            }).then(() => {
                window.location.reload();
            });
            return;
        }

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response error:', errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Load Successful!',
                text: data.message,
                confirmButtonText: 'Great!'
            });
            // Reset form
            document.getElementById('loadForm').reset();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Load Failed',
                text: data.message || 'Something went wrong. Please try again.'
            });
        }
    } catch (error) {
        console.error('Network error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Unable to connect to the server. Please check your internet connection and try again.'
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        buttonText.textContent = 'Load Nexo Paisa';
        button.classList.remove('animate-pulse');
    }
});
</script>
@endsection
