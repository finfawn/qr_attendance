<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create New Attendance Slot
            </h2>
            <a href="{{ route('events.attendance-slots.index', $event) }}" 
                class="text-gray-600 hover:text-gray-900 dark:hover:text-gray-400 inline-flex items-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Event Info Card -->
                    <div class="mb-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            Event: {{ $event->title }}
                        </h3>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <div>
                                <span class="font-medium">Date:</span> 
                                {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
                            </div>
                            <div>
                                <span class="font-medium">Location:</span> 
                                {{ $event->location }}
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 dark:bg-red-900/50 border-l-4 border-red-500 p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please correct the following errors:</h3>
                            </div>
                            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-200">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('events.attendance-slots.store', $event) }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Attendance Slot Title
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                placeholder="e.g., Morning Session, Registration Check-in"
                                required>
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                            <input type="date" name="date" id="date" value="{{ old('date', $event->date) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                required>
                        </div>

                        <!-- Time Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                            <div>
                                <x-input-label for="start_time" value="Start Time" />
                                <x-text-input id="start_time" type="time" name="start_time" required />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Regular attendance begins
                                </p>
                            </div>

                            <div>
                                <x-input-label for="end_time" value="End Time" />
                                <x-text-input id="end_time" type="time" name="end_time" required />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Regular attendance ends, late period begins
                                </p>
                            </div>

                            <div>
                                <x-input-label for="absent_time" value="Cutoff Time" />
                                <x-text-input id="absent_time" type="time" name="absent_time" required />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Final cutoff - marked absent after this time
                                </p>
                            </div>
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t dark:border-gray-700">
                            <a href="{{ route('events.attendance-slots.index', $event) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Attendance Slot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const startTime = document.getElementById('start_time');
            const endTime = document.getElementById('end_time');
            const absentTime = document.getElementById('absent_time');

            form.addEventListener('submit', function(e) {
                const errors = [];

                // Convert times to comparable values
                const start = startTime.value;
                const end = endTime.value;
                
                if (end < start) {
                    errors.push('End time cannot be before start time');
                }

                if (errors.length > 0) {
                    e.preventDefault();
                    const errorMessage = errors.join('\n');
                    showNotification(errorMessage, 'error');
                }
            });

            function showNotification(message, type = 'error') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } transition-opacity duration-300 z-50`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
    @endpush
</x-app-layout> 