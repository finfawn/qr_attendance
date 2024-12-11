<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Register for Event Button -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <button id="registerEventBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg shadow-lg flex items-center justify-center space-x-3 transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Register for Event</span>
                    </button>
                </div>
            </div>

            <!-- Current Event Card -->
            @if($currentEvent)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg cursor-pointer" onclick="toggleEventDetails(this)">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $currentEvent->title }}</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $currentEvent->description }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                            Approved
                        </span>
                    </div>

                    <!-- Event Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($currentEvent->date)->format('F d, Y') }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($currentEvent->start_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($currentEvent->end_time)->format('h:i A') }}
                            </span>
                        </div>
                    </div>

                    <!-- Expandable Attendance Slots Section (Hidden by default) -->
                    <div class="attendance-slots hidden mt-6 border-t pt-6">
                        <!-- Current Attendance Slot -->
                        @if($currentSlot)
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Current Attendance Slot</h4>
                                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-900/30">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-gray-100">{{ $currentSlot->title }}</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($currentSlot->start_time)->format('h:i A') }} - 
                                                {{ \Carbon\Carbon::parse($currentSlot->end_time)->format('h:i A') }}
                                            </p>

                                            <div class="mt-3">
                                                @if($attendanceRecords->has($currentSlot->id))
                                                    @php $attendance = $attendanceRecords->get($currentSlot->id); @endphp
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                        {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                                           ($attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                                           'bg-red-100 text-red-800') }}">
                                                        @if($attendance->status === 'present')
                                                            ✓ Present at {{ \Carbon\Carbon::parse($attendance->scanned_at)->format('h:i A') }}
                                                        @elseif($attendance->status === 'late')
                                                            ⚠ Late at {{ \Carbon\Carbon::parse($attendance->scanned_at)->format('h:i A') }}
                                                        @else
                                                            ✕ Absent
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                        Not yet recorded - Please scan your QR code
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Past Attendance Slots -->
                        @php
                            $pastSlots = $currentEvent->attendanceSlots()
                                ->whereDate('date', '<=', now())
                                ->whereTime('absent_time', '<', now())
                                ->orderByDesc('date')
                                ->orderByDesc('start_time')
                                ->get();
                        @endphp

                        @if($pastSlots->count() > 0)
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Past Attendance Records</h4>
                                <div class="space-y-3">
                                    @foreach($pastSlots as $slot)
                                        @php
                                            $attendance = \App\Models\Attendance::where('attendance_slot_id', $slot->id)
                                                ->whereHas('registration', function($query) use ($currentEvent) {
                                                    $query->where('user_id', auth()->id())
                                                        ->where('event_id', $currentEvent->id);
                                                })
                                                ->first();
                                        @endphp
                                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <h5 class="font-medium text-gray-900 dark:text-gray-100">{{ $slot->title }}</h5>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ \Carbon\Carbon::parse($slot->date)->format('F d, Y') }} |
                                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - 
                                                        {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                    </p>
                                                    @if($attendance)
                                                        <div class="mt-2">
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                                {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                                                   ($attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                                                   'bg-red-100 text-red-800') }}">
                                                                @if($attendance->status === 'present')
                                                                    ✓ Present at {{ \Carbon\Carbon::parse($attendance->scanned_at)->format('h:i A') }}
                                                                @elseif($attendance->status === 'late')
                                                                    ⚠ Late at {{ \Carbon\Carbon::parse($attendance->scanned_at)->format('h:i A') }}
                                                                @else
                                                                    ✕ Absent
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Upcoming Attendance Slots -->
                        @if($upcomingSlots->count() > 0)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Upcoming Attendance Slots</h4>
                                <div class="space-y-3">
                                    @foreach($upcomingSlots as $slot)
                                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <h5 class="font-medium text-gray-900 dark:text-gray-100">{{ $slot->title }}</h5>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ \Carbon\Carbon::parse($slot->date)->format('F d, Y') }} |
                                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - 
                                                        {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                    </p>
                                                </div>
                                                <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                                    Upcoming
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!$currentSlot && $upcomingSlots->count() === 0)
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                No attendance slots available at the moment.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-500 dark:text-gray-400 text-center">
                    You are not registered for any events.
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Choice Modal -->
    <div id="registrationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Choose Registration Method</h3>
                <button id="closeRegistrationModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Two Choice Buttons -->
                <button id="scanQrChoice" class="w-full bg-blue-600 text-white py-4 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span>Scan QR Code</span>
                </button>

                <button id="manualCodeChoice" class="w-full bg-gray-600 text-white py-4 px-4 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center space-x-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Enter Code Manually</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Manual Code Entry Modal -->
    <div id="manualCodeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Enter Event Code</h3>
                <button id="closeManualCodeModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="eventCodeForm" action="{{ route('events.register') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="event_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Code</label>
                    <input type="text" id="event_code" name="event_code" 
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Enter event code">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Register
                </button>
            </form>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="qrScannerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Scan Event QR Code</h3>
                <button id="closeQrScannerModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="qr-reader"></div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const registerEventBtn = document.getElementById('registerEventBtn');
            const registrationModal = document.getElementById('registrationModal');
            const manualCodeModal = document.getElementById('manualCodeModal');
            const closeRegistrationModal = document.getElementById('closeRegistrationModal');
            const closeManualCodeModal = document.getElementById('closeManualCodeModal');
            const scanQrChoice = document.getElementById('scanQrChoice');
            const manualCodeChoice = document.getElementById('manualCodeChoice');
            const qrScannerModal = document.getElementById('qrScannerModal');
            const closeQrScannerModal = document.getElementById('closeQrScannerModal');

            // Registration Choice Modal
            registerEventBtn.addEventListener('click', () => {
                registrationModal.classList.remove('hidden');
            });

            closeRegistrationModal.addEventListener('click', () => {
                registrationModal.classList.add('hidden');
            });

            // Manual Code Entry
            manualCodeChoice.addEventListener('click', () => {
                registrationModal.classList.add('hidden');
                manualCodeModal.classList.remove('hidden');
            });

            closeManualCodeModal.addEventListener('click', () => {
                manualCodeModal.classList.add('hidden');
            });

            // QR Scanner
            let html5QrcodeScanner = null;

            scanQrChoice.addEventListener('click', () => {
                registrationModal.classList.add('hidden');
                qrScannerModal.classList.remove('hidden');
                
                if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "qr-reader",
                        { fps: 10, qrbox: {width: 250, height: 250} }
                    );
                    
                    html5QrcodeScanner.render((decodedText) => {
                        try {
                            // Parse the QR code data
                            const qrData = JSON.parse(decodedText);
                            
                            // Stop scanning
                            html5QrcodeScanner.clear();
                            qrScannerModal.classList.add('hidden');
                            
                            // Add loading state
                            const loadingMessage = document.createElement('div');
                            loadingMessage.textContent = 'Processing registration...';
                            loadingMessage.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded shadow';
                            document.body.appendChild(loadingMessage);
                            
                            // Send to server
                            fetch('/events/register-via-qr', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    qr_data: decodedText
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                document.body.removeChild(loadingMessage);
                                if (data.success) {
                                    alert(data.message);
                                    window.location.reload();
                                } else {
                                    alert(data.message || 'Error registering for event');
                                }
                            })
                            .catch(error => {
                                document.body.removeChild(loadingMessage);
                                console.error('Error:', error);
                                alert('An error occurred while registering. Please try again.');
                            });
                        } catch (error) {
                            console.error('Error parsing QR code:', error);
                            alert('Invalid QR code format. Please try again.');
                        }
                        console.log('Decoded QR:', decodedText);
                    });
                }
            });

            closeQrScannerModal.addEventListener('click', () => {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                }
                qrScannerModal.classList.add('hidden');
            });
        });

        function toggleEventDetails(element) {
            const attendanceSlots = element.querySelector('.attendance-slots');
            attendanceSlots.classList.toggle('hidden');
        }
    </script>
    @endpush
</x-app-layout>
