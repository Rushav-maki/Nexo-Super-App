@extends('layouts.app')

@section('content')
    <div x-data="hotelBooking()" class="flex flex-col items-center justify-center py-20 space-y-12">

        <h2 class="text-4xl font-bold text-stone-900 uppercase font-serif tracking-tight italic">Hotel Booking</h2>

        <!-- Hotel Info Card -->
        <div
            class="bg-white border border-black/10 rounded-[3rem] shadow-2xl w-full max-w-4xl p-10 flex flex-col md:flex-row gap-10">

            <!-- Hotel Image -->
            <div class="md:w-1/2 rounded-[2rem] overflow-hidden shadow-lg">
                <img :src="hotel.image || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800'"
                    class="w-full h-full object-cover" :alt="hotel.name">
            </div>

            <!-- Hotel Details -->
            <div class="md:w-1/2 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-3xl font-bold font-serif italic text-stone-900" x-text="hotel.name"></h3>
                        <p class="text-sm text-stone-400 mt-2 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-orange-600"></i>
                            <span x-text="hotel.location"></span>
                        </p>
                    </div>
                    <div
                        class="bg-[#1c1917]/90 text-white px-4 py-2 rounded-full text-sm font-black uppercase tracking-widest">
                        ★ <span x-text="hotel.rating || '4.5'"></span>
                    </div>
                </div>

                <!-- Amenities -->
                <div class="flex flex-wrap gap-2 mt-4" x-show="hotel.amenities && hotel.amenities.length > 0">
                    <template x-for="(amenity, aidx) in (hotel.amenities || [])" :key="aidx">
                        <span
                            class="text-[9px] font-black uppercase tracking-widest bg-stone-50 px-3 py-1.5 rounded-full text-stone-900"
                            x-text="amenity"></span>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4 text-stone-700">
                    <div class="space-y-1">
                        <p class="text-sm font-black uppercase tracking-widest">Location</p>
                        <p class="text-base font-serif italic" x-text="hotel.location"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-black uppercase tracking-widest">Rating</p>
                        <p class="text-base font-serif italic">★ <span x-text="hotel.rating || '4.5'"></span></p>
                    </div>
                    <div class="space-y-1 col-span-2">
                        <p class="text-sm font-black uppercase tracking-widest">Price Per Night</p>
                        <p class="text-lg font-bold text-orange-600" x-text="'Rs. ' + (hotel.pricePerNight || '5000')"></p>
                    </div>
                </div>

                <!-- Input & Pay Button -->
                <div class="mt-6" x-show="!receipt">
                    <label class="block text-stone-500 font-bold text-sm mb-2">Number of Nights</label>
                    <input type="number" x-model.number="nights" min="1"
                        class="w-full border border-stone-300 rounded-xl px-4 py-2 mb-4">

                    <button @click="confirmBooking()"
                        class="w-full bg-[#1c1917] text-white px-6 py-3 rounded-xl font-black text-[12px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-md">
                        Pay Now
                    </button>
                </div>

                <!-- Receipt -->
                <div x-show="receipt" class="mt-6 bg-green-50 border border-green-400 p-6 rounded-2xl space-y-4">
                    <h3 class="text-xl font-bold text-green-700 uppercase">Booking Successful!</h3>

                    <div class="space-y-1">
                        <p><strong>Hotel Name:</strong> <span x-text="hotel.name"></span></p>
                        <p><strong>Location:</strong> <span x-text="hotel.location"></span></p>
                        <p><strong>Rating:</strong> ★ <span x-text="hotel.rating || '4.5'"></span></p>
                        <template x-if="hotel.amenities && hotel.amenities.length > 0">
                            <p><strong>Amenities:</strong> <span x-text="(hotel.amenities || []).join(', ')"></span></p>
                        </template>
                        <p><strong>Price Per Night:</strong> Rs. <span x-text="hotel.pricePerNight || '5000'"></span></p>
                        <p><strong>Number of Nights:</strong> <span x-text="nights"></span></p>
                        <p><strong>Total Amount:</strong> Rs. <span x-text="nights * (hotel.pricePerNight || 5000)"></span>
                        </p>
                        <p><strong>Booking Date:</strong> <span x-text="new Date().toLocaleString()"></span></p>
                    </div>

                    <p class="mt-2 text-sm text-green-800">Your hotel booking is confirmed! Please proceed to the hotel at
                        your check-in time.
                    </p>

                    <button @click="downloadPDF()"
                        class="mt-4 bg-orange-600 text-white px-6 py-3 rounded-xl font-black text-[12px] uppercase tracking-widest hover:bg-orange-700 transition-all shadow-md">
                        Download PDF Receipt
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jsPDF CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        window.hotelBooking = function() {
            let hotelData = sessionStorage.getItem('selectedHotel');
            if (!hotelData) {
                window.location.href = "{{ route('user.travel') }}";
                return {};
            }

            return {
                hotel: JSON.parse(hotelData),
                nights: 1,
                receipt: null,

                async payHotel() {
                    if (!this.hotel) return;
                    if (this.nights < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Input',
                            text: 'Please enter a valid number of nights',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Show loading
                    Swal.fire({
                        title: 'Processing Payment...',
                        text: 'Please wait while we process your booking',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch("{{ route('user.travel.hotelPayment') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                hotelName: this.hotel.name,
                                location: this.hotel.location,
                                nights: this.nights,
                                total: this.nights * (this.hotel.pricePerNight || 5000),
                                hotelData: this.hotel
                            })
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Booking Successful!',
                                text: 'Your hotel booking has been confirmed',
                                confirmButtonText: 'Great!'
                            });
                            // Show receipt
                            this.receipt = true;
                        } else {
                            throw new Error(data.message || 'Payment failed');
                        }
                    } catch (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Payment Failed',
                            text: err.message || 'An error occurred while processing your payment',
                            confirmButtonText: 'Try Again'
                        });
                    }
                },

                confirmBooking() {
                    if (!this.hotel) return;
                    if (this.nights < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Input',
                            text: 'Please enter a valid number of nights',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    const total = this.nights * (this.hotel.pricePerNight || 5000);
                    Swal.fire({
                        title: 'Confirm Hotel Booking',
                        html: `
                            <div class="text-left">
                                <p class="mb-2"><strong>Hotel:</strong> ${this.hotel.name}</p>
                                <p class="mb-2"><strong>Location:</strong> ${this.hotel.location}</p>
                                <p class="mb-2"><strong>Nights:</strong> ${this.nights}</p>
                                <p class="mb-2"><strong>Price per Night:</strong> Rs. ${(this.hotel.pricePerNight || 5000).toLocaleString()}</p>
                                <p class="text-lg font-bold text-orange-600"><strong>Total Amount:</strong> Rs. ${total.toLocaleString()}</p>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Book Now!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#1c1917',
                        cancelButtonColor: '#dc2626',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.payHotel();
                        }
                    });
                },

                downloadPDF() {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF();

                    const pricePerNight = this.hotel.pricePerNight || 5000;
                    const total = this.nights * pricePerNight;
                    const date = new Date().toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const pageWidth = doc.internal.pageSize.getWidth();
                    const pageHeight = doc.internal.pageSize.getHeight();

                    // Header background
                    doc.setFillColor(28, 25, 23); // #1c1917
                    doc.rect(0, 0, pageWidth, 45, 'F');

                    // Company name in header
                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(24);
                    doc.setFont(undefined, 'bold');
                    doc.text("NEXO TRAVEL", pageWidth / 2, 20, {
                        align: 'center'
                    });

                    doc.setFontSize(12);
                    doc.setFont(undefined, 'normal');
                    doc.text("Premium Hotel Booking Services", pageWidth / 2, 30, {
                        align: 'center'
                    });

                    // Receipt title
                    doc.setTextColor(28, 25, 23);
                    doc.setFontSize(18);
                    doc.setFont(undefined, 'bold');
                    doc.text("HOTEL BOOKING RECEIPT", pageWidth / 2, 60, {
                        align: 'center'
                    });

                    // Divider line
                    doc.setDrawColor(200, 200, 200);
                    doc.setLineWidth(0.5);
                    doc.line(20, 65, pageWidth - 20, 65);

                    // Receipt details section
                    let yPos = 80;

                    // Section: Hotel Information
                    doc.setFontSize(14);
                    doc.setFont(undefined, 'bold');
                    doc.setTextColor(45, 122, 45); // Green accent
                    doc.text("HOTEL INFORMATION", 20, yPos);
                    yPos += 10;

                    doc.setFontSize(11);
                    doc.setFont(undefined, 'normal');
                    doc.setTextColor(60, 60, 60);

                    const hotelInfo = [{
                            label: 'Hotel Name:',
                            value: this.hotel.name
                        },
                        {
                            label: 'Location:',
                            value: this.hotel.location
                        },
                        {
                            label: 'Rating:',
                            value: `${this.hotel.rating || '4.5'}`
                        }
                    ];

                    if (this.hotel.amenities && this.hotel.amenities.length > 0) {
                        hotelInfo.push({
                            label: 'Amenities:',
                            value: this.hotel.amenities.join(', ')
                        });
                    }

                    hotelInfo.forEach(item => {
                        doc.setFont(undefined, 'bold');
                        doc.text(item.label, 25, yPos);
                        doc.setFont(undefined, 'normal');
                        // Handle long values
                        const lines = doc.splitTextToSize(item.value, pageWidth - 90);
                        doc.text(lines, 75, yPos);
                        yPos += lines.length * 8;
                    });

                    yPos += 5;

                    // Section: Booking Details
                    doc.setFontSize(14);
                    doc.setFont(undefined, 'bold');
                    doc.setTextColor(45, 122, 45);
                    doc.text("BOOKING DETAILS", 20, yPos);
                    yPos += 10;

                    doc.setFontSize(11);
                    doc.setFont(undefined, 'normal');
                    doc.setTextColor(60, 60, 60);

                    const bookingInfo = [{
                            label: 'Price Per Night:',
                            value: `Rs. ${pricePerNight.toLocaleString()}`
                        },
                        {
                            label: 'Number of Nights:',
                            value: `${this.nights} ${this.nights === 1 ? 'Night' : 'Nights'}`
                        },
                        {
                            label: 'Booking Date:',
                            value: date
                        }
                    ];

                    bookingInfo.forEach(item => {
                        doc.setFont(undefined, 'bold');
                        doc.text(item.label, 25, yPos);
                        doc.setFont(undefined, 'normal');
                        doc.text(item.value, 75, yPos);
                        yPos += 8;
                    });

                    yPos += 10;

                    // Total amount box
                    doc.setFillColor(240, 248, 255);
                    doc.roundedRect(20, yPos - 5, pageWidth - 40, 20, 3, 3, 'F');

                    doc.setFontSize(14);
                    doc.setFont(undefined, 'bold');
                    doc.setTextColor(28, 25, 23);
                    doc.text("TOTAL AMOUNT:", 25, yPos + 7);

                    doc.setFontSize(16);
                    doc.setTextColor(45, 122, 45);
                    doc.text(`Rs. ${total.toLocaleString()}`, pageWidth - 25, yPos + 7, {
                        align: 'right'
                    });

                    yPos += 35;

                    // Information box
                    doc.setFillColor(255, 250, 240);
                    doc.setDrawColor(45, 122, 45);
                    doc.setLineWidth(0.5);
                    doc.roundedRect(20, yPos, pageWidth - 40, 25, 3, 3, 'FD');

                    doc.setFontSize(10);
                    doc.setFont(undefined, 'bold');
                    doc.setTextColor(45, 122, 45);
                    doc.text("CHECK-IN INFORMATION", pageWidth / 2, yPos + 8, {
                        align: 'center'
                    });

                    doc.setFont(undefined, 'normal');
                    doc.setTextColor(60, 60, 60);
                    doc.text("Your hotel booking is confirmed! Please proceed to the hotel at your check-in time.",
                        pageWidth / 2,
                        yPos + 16, {
                            align: 'center'
                        });

                    // Footer
                    doc.setFontSize(9);
                    doc.setTextColor(150, 150, 150);
                    doc.text("Thank you for choosing Nexo Travel!", pageWidth / 2, pageHeight - 20, {
                        align: 'center'
                    });
                    doc.text("For inquiries, contact us at support@nexotravel.com", pageWidth / 2, pageHeight - 15, {
                        align: 'center'
                    });

                    // Divider above footer
                    doc.setDrawColor(200, 200, 200);
                    doc.line(20, pageHeight - 25, pageWidth - 20, pageHeight - 25);

                    // Save the PDF
                    const hotelName = this.hotel.name.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
                    doc.save(`Nexo_${hotelName}_Booking_Receipt.pdf`);
                }
            }
        }
    </script>
@endsection
