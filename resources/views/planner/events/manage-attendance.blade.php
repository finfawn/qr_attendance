<x-app-layout>
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Attendance') }} - {{ $event->title }}
            </h2>
            <a href="{{ route('events.show', $event) }}" 
               class="text-gray-600 hover:text-gray-900 dark:hover:text-gray-400 inline-flex items-center"
               title="Back to Event">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            Attendance Slots
                        </h2>
                        <a href="{{ route('events.attendance-slots.create', $event) }}" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Attendance Slot
                        </a>
                    </div>

                    <!-- Attendance Slots List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            // Group slots by status
                            $currentSlots = collect();
                            $upcomingSlots = collect();
                            $endedSlots = collect();

                            foreach($event->attendanceSlots as $slot) {
                                $timezone = config('app.timezone', 'Asia/Manila');
                                $now = now()->timezone($timezone);
                                
                                // Parse dates with timezone - Combine date and time
                                $startDateTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->start_time)->timezone($timezone);
                                $endDateTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->end_time)->timezone($timezone);
                                $absentDateTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->absent_time)->timezone($timezone);
                                
                                // Check if the dates match first
                                $isToday = $now->isSameDay($startDateTime);
                                
                                // Determine if current - must be today and within time window
                                $isCurrent = $isToday && $now->between($startDateTime, $absentDateTime);

                                if ($isCurrent) {
                                    $currentSlots->push($slot);
                                } elseif ($now->lt($startDateTime)) {
                                    $upcomingSlots->push($slot);
                                } else {
                                    $endedSlots->push($slot);
                                }
                            }

                            // Sort each group by date and time
                            $currentSlots = $currentSlots->sortBy(function($slot) {
                                return $slot->date . ' ' . $slot->start_time;
                            });
                            $upcomingSlots = $upcomingSlots->sortBy(function($slot) {
                                return $slot->date . ' ' . $slot->start_time;
                            });
                            $endedSlots = $endedSlots->sortByDesc(function($slot) {
                                return $slot->date . ' ' . $slot->start_time;
                            });
                        @endphp

                        {{-- Current Slots --}}
                        @foreach($currentSlots as $slot)
                            @include('planner.events._slot_card', ['slot' => $slot])
                        @endforeach

                        {{-- Upcoming Slots --}}
                        @foreach($upcomingSlots as $slot)
                            @include('planner.events._slot_card', ['slot' => $slot])
                        @endforeach

                        {{-- Ended Slots --}}
                        @foreach($endedSlots as $slot)
                            @include('planner.events._slot_card', ['slot' => $slot])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="qrScannerModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Scan QR Code
                    </h3>
                    <button type="button" onclick="closeQrScannerModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <div id="reader"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
                    function ensureHttps(url) {
                if (window.location.protocol === 'https:') {
                    return url.replace('http:', 'https:');
                }
                return url;
            }
        document.addEventListener('DOMContentLoaded', function() {
            const addSlotBtn = document.getElementById('addSlotBtn');
            const qrScannerModal = document.getElementById('qrScannerModal');
            const eventId = {{ $event->id }};
            let html5QrcodeScanner = null;
            let currentSlotId = null;

            addSlotBtn.addEventListener('click', function() {
                openSlotModal();
            });

            function openSlotModal(slotId = null) {
                const modalTitle = document.getElementById('slotModalTitle');
                if (slotId) {
                    modalTitle.textContent = 'Edit Attendance Slot';
                    slotForm.action = "{{ route('events.attendance-slots.update', ['event' => $event->id, 'attendance_slot' => ':slotId']) }}".replace(':slotId', slotId);
                    slotForm.method = 'POST';
                    let methodInput = slotForm.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        slotForm.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';
                } else {
                    modalTitle.textContent = 'Add Attendance Slot';
                    slotForm.action = "{{ route('events.attendance-slots.store', ['event' => $event->id]) }}";
                    slotForm.method = 'POST';
                    const methodInput = slotForm.querySelector('input[name="_method"]');
                    if (methodInput) {
                        methodInput.remove();
                    }
                    // Clear form when adding new slot
                    slotForm.reset();
                }
                slotModal.classList.remove('hidden');
            }

            function closeSlotModal() {
                slotModal.classList.add('hidden');
                slotForm.reset();
            }

            window.openQrScanner = function(slotId) {
                currentSlotId = slotId;
                const qrScannerModal = document.getElementById('qrScannerModal');
                qrScannerModal.classList.remove('hidden');

                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", 
                    { 
                        fps: 10,
                        qrbox: {width: 250, height: 250},
                        aspectRatio: 1.0
                    }
                );

                html5QrcodeScanner.render(onScanSuccess);
            }

            window.closeQrScannerModal = function() {
                const qrScannerModal = document.getElementById('qrScannerModal');
                qrScannerModal.classList.add('hidden');
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                }
            }

            function onScanSuccess(qrCode) {
                // Don't clear the scanner - let it keep running
                // html5QrcodeScanner.clear();

                // Send the QR code data to the server
                fetch('/registration/record', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        qr_code: qrCode,
                        slot_id: currentSlotId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        // Update the attendee count without page refresh
                        updateAttendeeCount(currentSlotId);
                        
                        // Show notification with appropriate status color
                        const statusMessage = data.status === 'present' ? 'Present' :
                                            data.status === 'late' ? 'Late' :
                                            'Absent';
                        
                        showFloatingNotification(
                            `${data.attendee?.name || 'Student'} - ${statusMessage}: ${data.message}`,
                            data.status === 'present' ? 'success' :
                            data.status === 'late' ? 'warning' : 'error'
                        );
                    } else {
                        showFloatingNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFloatingNotification('Failed to record attendance. Please try again.', 'error');
                });
            }

            // Function to update attendee count via AJAX
            function updateAttendeeCount(slotId) {
                fetch(`/planner/events/${eventId}/attendance-slots/${slotId}/count`)
                    .then(response => response.json())
                    .then(data => {
                        const countElement = document.querySelector(`[data-slot-id="${slotId}"] .attendee-count`);
                        if (countElement) {
                            countElement.textContent = `${data.count} Attendees`;
                        }
                    })
                    .catch(error => console.error('Error updating count:', error));
            }

            // Updated notification function to handle all cases
            function showFloatingNotification(message, type = 'success') {
                // Remove any existing notifications first
                const existingNotifications = document.querySelectorAll('.notification');
                existingNotifications.forEach(n => n.remove());

                const notification = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-green-500' :
                                type === 'warning' ? 'bg-yellow-500' :
                                'bg-red-500';
                
                notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${bgColor} transition-all duration-300 transform translate-y-0 opacity-100 z-50 notification`;
                
                notification.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${type === 'success' 
                                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                                : type === 'warning'
                                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
                                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}
                        </svg>
                        <span>${message}</span>
                    </div>
                `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translate-y-2';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            window.openSlotModal = openSlotModal;
            window.closeSlotModal = closeSlotModal;
            window.openQrScanner = openQrScanner;
            window.closeQrScannerModal = closeQrScannerModal;
            window.editSlot = function(slotId) {
                fetch(ensureHttps("{{ route('events.attendance-slots.edit', ['event' => $event->id, 'attendance_slot' => ':slotId']) }}".replace(':slotId', slotId)), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Populate the form with existing values
                    document.getElementById('title').value = data.slot.title;
                    document.getElementById('start_time').value = data.slot.start_time;
                    document.getElementById('end_time').value = data.slot.end_time;
                    document.getElementById('absent_time').value = data.slot.absent_time;
                    
                    // Open the modal with edit mode
                    openSlotModal(slotId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to load attendance slot data', 'error');
                });
            };

            // Form submission handler for create/update
            document.getElementById('slotForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch(ensureHttps(this.action), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.success) {
                        closeSlotModal();
                        showNotification('Attendance slot created successfully', 'success');
                        window.location.reload();
                    } else if (data) {
                        showNotification(data.message || 'Failed to create attendance slot', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to create attendance slot', 'error');
                    // If it's a validation error, the page will be reloaded with the error messages
                    if (error instanceof SyntaxError) {
                        window.location.reload();
                    }
                });
            });

            // Handle delete button clicks
            document.querySelectorAll('form[action*="attendance-slots"]').forEach(form => {
                if (form.id !== 'slotForm') { // Only attach to delete forms, not the slot form
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        if (confirm('Are you sure you want to delete this attendance slot?')) {
                            fetch(ensureHttps(form.action), {
                                method: 'POST',
                                body: new FormData(form),
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    showNotification('Attendance slot deleted successfully', 'delete');
                                    location.reload();
                                } else {
                                    showNotification(data.message || 'Failed to delete attendance slot', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showNotification('An error occurred. Please try again.', 'error');
                            });
                        }
                    });
                }
            });

            // Remove any other showNotification functions and use showFloatingNotification instead
            window.showNotification = showFloatingNotification;
        });
    </script>
    @endpush
</x-app-layout>
@vite(['resources/css/app.css', 'resources/js/app.js'])