@extends('layouts.app')

@section('content')
    <div x-data="carBooking()" class="flex flex-col items-center justify-center py-20 space-y-12">

        <h2 class="text-4xl font-bold text-stone-900 uppercase font-serif tracking-tight italic">Car Booking</h2>

        <!-- Car Info Card -->
        <div
            class="bg-white border border-black/10 rounded-[3rem] shadow-2xl w-full max-w-4xl p-10 flex flex-col md:flex-row gap-10">

            <!-- Car Image -->
            <div class="md:w-1/2 rounded-[2rem] overflow-hidden shadow-lg">
                <img :src="`/${car.image_url}` ?? 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800'"
                    class="w-full h-full object-cover" :alt="car.name">
            </div>

            <!-- Car Details -->
            <div class="md:w-1/2 space-y-4">
                <h3 class="text-3xl font-bold font-serif italic text-stone-900" x-text="car.name"></h3>

                <div class="grid grid-cols-2 gap-4 mt-4 text-stone-700">
                    <div class="space-y-1">
                        <p class="text-sm font-black uppercase tracking-widest">Type</p>
                        <p class="text-base font-serif italic" x-text="car.type"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-black uppercase tracking-widest">Transmission</p>
                        <p class="text-base font-serif italic" x-text="car.transmission"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-black uppercase tracking-widest">Seating Capacity</p>
                        <p class="text-base font-serif italic" x-text="car.seatingCapacity + ' Seater'"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-black uppercase tracking-widest">Fuel Type</p>
                        <p class="text-base font-serif italic" x-text="car.fuelType"></p>
                    </div>
                    <div class="space-y-1 col-span-2">
                        <p class="text-sm font-black uppercase tracking-widest">Price Per Day</p>
                        <p class="text-lg font-bold text-orange-600" x-text="'Rs. ' + car.pricePerDay"></p>
                    </div>
                </div>

                <!-- Input & Pay Button -->
                <div class="mt-6" x-show="!receipt">
                    <label class="block text-stone-500 font-bold text-sm mb-2">Number of Days</label>
                    <input type="number" x-model.number="days" min="1"
                        class="w-full border border-stone-300 rounded-xl px-4 py-2 mb-4">

                    <button @click="confirmBooking()"
                        class="w-full bg-[#1c1917] text-white px-6 py-3 rounded-xl font-black text-[12px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-md">
                        Pay Now
                    </button>
                </div>

                <!-- Receipt -->
                <div x-show="receipt" class="mt-6 bg-green-50 border border-green-400 p-6 rounded-2xl space-y-4">
                    <h3 class="text-xl font-bold text-green-700 uppercase">Payment Successful!</h3>

                    <div class="space-y-1">
                        <p><strong>Car Name:</strong> <span x-text="car.name"></span></p>
                        <p><strong>Type:</strong> <span x-text="car.type"></span></p>
                        <p><strong>Transmission:</strong> <span x-text="car.transmission"></span></p>
                        <p><strong>Seating Capacity:</strong> <span x-text="car.seatingCapacity"></span></p>
                        <p><strong>Fuel Type:</strong> <span x-text="car.fuelType"></span></p>
                        <p><strong>Price Per Day:</strong> Rs. <span x-text="car.pricePerDay"></span></p>
                        <p><strong>Number of Days:</strong> <span x-text="days"></span></p>
                        <p><strong>Total Amount:</strong> Rs. <span x-text="days * car.pricePerDay"></span></p>
                        <p><strong>Payment Date:</strong> <span x-text="new Date().toLocaleString()"></span></p>
                    </div>

                    <p class="mt-2 text-sm text-green-800">You can visit nearby Nexo showroom to receive your rented car.
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
        window.carBooking = function() {
            let carData = sessionStorage.getItem('selectedCar');
            if (!carData) {
                window.location.href = "{{ route('user.travel') }}";
                return {};
            }

            return {
                car: JSON.parse(carData),
                days: 1,
                receipt: null,

                async payCar() {
                    if (!this.car) return;
                    if (this.days < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Input',
                            text: 'Please enter a valid number of days',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Show loading
                    Swal.fire({
                        title: 'Processing Payment...',
                        text: 'Please wait while we process your rental',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch("{{ route('user.travel.carPayment') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                carId: this.car.id,
                                days: this.days,
                                total: this.days * this.car.pricePerDay
                            })
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Rental Successful!',
                                text: 'Your car rental has been confirmed',
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
                    if (!this.car) return;
                    if (this.days < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Input',
                            text: 'Please enter a valid number of days',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    const total = this.days * this.car.pricePerDay;
                    Swal.fire({
                        title: 'Confirm Car Rental',
                        html: `
                            <div class="text-left">
                                <p class="mb-2"><strong>Car:</strong> ${this.car.name}</p>
                                <p class="mb-2"><strong>Type:</strong> ${this.car.type}</p>
                                <p class="mb-2"><strong>Days:</strong> ${this.days}</p>
                                <p class="mb-2"><strong>Price per Day:</strong> Rs. ${this.car.pricePerDay.toLocaleString()}</p>
                                <p class="text-lg font-bold text-orange-600"><strong>Total Amount:</strong> Rs. ${total.toLocaleString()}</p>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Rent Now!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#1c1917',
                        cancelButtonColor: '#dc2626',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.payCar();
                        }
                    });
                },

                downloadPDF() {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF();

                    const total = this.days * this.car.pricePerDay;
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
                    doc.text("NEXO CAR RENTAL", pageWidth / 2, 20, {
                        align: 'center'
                    });

                    doc.setFontSize(12);
                    doc.setFont(undefined, 'normal');
                    doc.text("Premium Vehicle Rental Services", pageWidth / 2, 30, {
                        align: 'center'
                    });

                    // Receipt title
                    doc.setTextColor(28, 25, 23);
                    doc.setFontSize(18);
                    doc.setFont(undefined, 'bold');
                    doc.text("RENTAL RECEIPT", pageWidth / 2, 60, {
                        align: 'center'
                    });

                    // Divider line
                    doc.setDrawColor(200, 200, 200);
                    doc.setLineWidth(0.5);
                    doc.line(20, 65, pageWidth - 20, 65);

                    // Receipt details section
                    let yPos = 80;

                    // Section: Vehicle Information
                    doc.setFontSize(14);
                    doc.setFont(undefined, 'bold');
                    doc.setTextColor(45, 122, 45); // Green accent
                    doc.text("VEHICLE INFORMATION", 20, yPos);
                    yPos += 10;

                    doc.setFontSize(11);
                    doc.setFont(undefined, 'normal');
                    doc.setTextColor(60, 60, 60);

                    const vehicleInfo = [{
                            label: 'Vehicle Name:',
                            value: this.car.name
                        },
                        {
                            label: 'Type:',
                            value: this.car.type
                        },
                        {
                            label: 'Transmission:',
                            value: this.car.transmission
                        },
                        {
                            label: 'Seating Capacity:',
                            value: `${this.car.seatingCapacity} Seater`
                        },
                        {
                            label: 'Fuel Type:',
                            value: this.car.fuelType
                        }
                    ];

                    vehicleInfo.forEach(item => {
                        doc.setFont(undefined, 'bold');
                        doc.text(item.label, 25, yPos);
                        doc.setFont(undefined, 'normal');
                        doc.text(item.value, 75, yPos);
                        yPos += 8;
                    });

                    yPos += 5;

                    // Section: Rental Details
                    doc.setFontSize(14);
                    doc.setFont(undefined, 'bold');
                    doc.setTextColor(45, 122, 45);
                    doc.text("RENTAL DETAILS", 20, yPos);
                    yPos += 10;

                    doc.setFontSize(11);
                    doc.setFont(undefined, 'normal');
                    doc.setTextColor(60, 60, 60);

                    const rentalInfo = [{
                            label: 'Daily Rate:',
                            value: `Rs. ${this.car.pricePerDay.toLocaleString()}`
                        },
                        {
                            label: 'Rental Duration:',
                            value: `${this.days} ${this.days === 1 ? 'Day' : 'Days'}`
                        },
                        {
                            label: 'Transaction Date:',
                            value: date
                        }
                    ];

                    rentalInfo.forEach(item => {
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
                    doc.text("PICKUP INFORMATION", pageWidth / 2, yPos + 8, {
                        align: 'center'
                    });

                    doc.setFont(undefined, 'normal');
                    doc.setTextColor(60, 60, 60);
                    doc.text("Please visit your nearest Nexo showroom to collect your rented vehicle.", pageWidth / 2,
                        yPos + 16, {
                            align: 'center'
                        });

                    // Footer
                    doc.setFontSize(9);
                    doc.setTextColor(150, 150, 150);
                    doc.text("Thank you for choosing Nexo Car Rental!", pageWidth / 2, pageHeight - 20, {
                        align: 'center'
                    });
                    doc.text("For inquiries, contact us at support@nexocarrental.com", pageWidth / 2, pageHeight - 15, {
                        align: 'center'
                    });

                    // Divider above footer
                    doc.setDrawColor(200, 200, 200);
                    doc.line(20, pageHeight - 25, pageWidth - 20, pageHeight - 25);

                    // Save the PDF
                    doc.save(`Nexo_${this.car.name.replace(/\s+/g, '_')}_Receipt.pdf`);
                }
            }
        }
    </script>
@endsection
