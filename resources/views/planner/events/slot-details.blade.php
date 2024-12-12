<x-app-layout>
    <div class="dashboard-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Time Slot Details') }}
                </h2>
                <a href="{{ route('events.manage-attendance', $event) }}" 
                   class="create-event-btn px-6 py-3 text-white rounded-lg transition-all duration-300 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    Back to Attendance
                </a>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Slot Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div class="space-y-4 flex-grow">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $slot->title }}</h3>
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</span>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($slot->date)->format('F d, Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</span>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - 
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Absent After</span>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($slot->absent_time)->format('g:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <!-- Edit Button -->
                            <a href="{{ route('events.attendance-slots.edit', ['event' => $event, 'attendance_slot' => $slot]) }}" 
                                class="inline-flex items-center justify-center w-10 h-10 border border-transparent rounded-full text-blue-600 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <!-- Delete Button -->
                            <form action="{{ route('events.attendance-slots.destroy', ['event' => $event, 'attendance_slot' => $slot]) }}" 
                                method="POST" 
                                class="delete-slot-form"
                                onsubmit="return false;">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                    onclick="deleteSlot(this.closest('form'))"
                                    class="inline-flex items-center justify-center w-10 h-10 border border-transparent rounded-full text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>

                            <!-- Scan QR Button -->
                            <button onclick="openQrScanner()" 
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transition-all duration-150 ease-in-out hover:scale-105">
                                <svg class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h7v7H3z M14 3h7v7h-7z M3 14h7v7H3z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14h3v3h-3z M20 14v3h-3 M20 20h-6"/>
                                    <rect x="5" y="5" width="3" height="3" stroke-width="2"/>
                                    <rect x="16" y="5" width="3" height="3" stroke-width="2"/>
                                    <rect x="5" y="16" width="3" height="3" stroke-width="2"/>
                                </svg>
                                <span class="font-medium">Scan QR Code</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendees List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Attendees</h3>
                    
                    <!-- Desktop View -->
                    <div class="hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Year & Section</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($registrations as $registration)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $registration->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $registration->user->idno }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $registration->user->course }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $registration->user->year }} - {{ $registration->user->section }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($registration->attendance)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $registration->attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                                       ($registration->attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                                       'bg-red-100 text-red-800') }}"
                                                    data-attendance-id="{{ $registration->attendance->id }}"
                                                    data-status="{{ $registration->attendance->status }}"
                                                    data-registration-id="{{ $registration->id }}">
                                                    {{ ucfirst($registration->attendance->status) }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Not Scanned
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $registration->attendance ? \Carbon\Carbon::parse($registration->attendance->scanned_at)->format('g:i A') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($registration->attendance)
                                                <button onclick="editStatus('{{ $registration->attendance->id }}', '{{ $registration->id }}')" 
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2 2 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View -->
                    <div class="md:hidden space-y-4">
                        @foreach($registrations as $registration)
                            <div class="bg-white dark:bg-gray-700 shadow rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ $registration->user->name }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $registration->user->idno }}
                                        </p>
                                    </div>
                                    @if($registration->attendance)
                                    <button onclick="editStatus('{{ $registration->attendance->id }}', '{{ $registration->id }}')" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2 2 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $registration->user->course }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Year {{ $registration->user->year }} - {{ $registration->user->section }}
                                    </p>
                                    <div class="mt-2 flex justify-between items-center">
                                        @if($registration->attendance)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $registration->attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                               ($registration->attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($registration->attendance->status) }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $registration->attendance->scanned_at ? \Carbon\Carbon::parse($registration->attendance->scanned_at)->format('g:i A') : '-' }}
                                        </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Not Scanned
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="qrScannerModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl w-full max-w-4xl p-8 shadow-2xl">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Attendance Scanner
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $slot->title }}
                        </p>
                    </div>
                    <button type="button" onclick="closeQrScannerModal()" 
                        class="rounded-lg p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Scanner Section -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                        <div id="reader" class="overflow-hidden rounded-lg"></div>
                        <div class="mt-4 text-center">
                            <div class="flex items-center justify-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}</span>
                            </div>
                            <div class="mt-1 text-xs text-red-600 dark:text-red-400">
                                Absent after {{ \Carbon\Carbon::parse($slot->absent_time)->format('g:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Results Section -->
                    <div class="relative">
                        <!-- Initial State -->
                        <div id="initialScanState" class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-xl p-6 text-center h-full flex flex-col items-center justify-center">
                            <div class="animate-bounce mb-6">
                                <svg class="w-20 h-20 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2m0 0H8m4 0h4m-4-8a3 3 0 100 6 3 3 0 000-6z"/>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Ready to Scan</h4>
                            <p class="text-gray-600 dark:text-gray-300 mb-6 max-w-xs mx-auto">
                                Position the QR code within the scanner frame to record attendance
                            </p>
                            <div class="animate-pulse flex justify-center">
                                <div class="h-1.5 w-16 bg-blue-200 dark:bg-blue-400 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Scan Result -->
                        <div id="scanResult" class="hidden h-full">
                            <div class="bg-white dark:bg-gray-700 rounded-xl p-6 h-full shadow-sm">
                                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-gray-600">
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">Scanned Attendee</h4>
                                        <p id="scanTime" class="text-xs text-gray-500 dark:text-gray-400 mt-1"></p>
                                    </div>
                                    <button onclick="resetScanner()" 
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/50 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Scan Another
                                    </button>
                                </div>
                                <div class="space-y-4">
                                    <!-- Name -->
                                    <div class="bg-gray-50/80 dark:bg-gray-600/50 rounded-lg p-4 transform transition-all hover:scale-[1.02]">
                                        <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</label>
                                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100" id="attendeeName"></p>
                                    </div>
                                    <!-- ID Number -->
                                    <div class="bg-gray-50/80 dark:bg-gray-600/50 rounded-lg p-4 transform transition-all hover:scale-[1.02]">
                                        <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">ID Number</label>
                                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100" id="attendeeIdno"></p>
                                    </div>
                                    <!-- Course -->
                                    <div class="bg-gray-50/80 dark:bg-gray-600/50 rounded-lg p-4 transform transition-all hover:scale-[1.02]">
                                        <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Course</label>
                                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100" id="attendeeCourse"></p>
                                    </div>
                                    <!-- Year & Section -->
                                    <div class="bg-gray-50/80 dark:bg-gray-600/50 rounded-lg p-4 transform transition-all hover:scale-[1.02]">
                                        <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Year & Section</label>
                                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100" id="attendeeYearSection"></p>
                                    </div>
                                    <!-- Status -->
                                    <div class="bg-gray-50/80 dark:bg-gray-600/50 rounded-lg p-4 transform transition-all hover:scale-[1.02]">
                                        <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</label>
                                        <div class="mt-2" id="attendeeStatus"></div>
                                    </div>
                                    <!-- Message -->
                                    <div class="mt-6 text-center">
                                        <div class="inline-block px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-600">
                                            <p class="text-sm font-medium" id="attendeeMessage"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Status Modal -->
    <div id="editStatusModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Edit Attendance Status
                    </h3>
                    <button type="button" onclick="closeEditStatusModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="editStatusForm" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditStatusModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Slot Modal -->
    <div id="editSlotModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Edit Attendance Slot
                    </h3>
                    <button type="button" onclick="closeEditSlotModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="editSlotForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text" name="title" id="edit_title" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="edit_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                        <input type="date" name="date" id="edit_date" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="edit_start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                        <input type="time" name="start_time" id="edit_start_time" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="edit_end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                        <input type="time" name="end_time" id="edit_end_time" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="edit_absent_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Absent After</label>
                        <input type="time" name="absent_time" id="edit_absent_time" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditSlotModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrcodeScanner = null;
        let currentAttendanceId = null;
        let currentRegistrationId = null;

        // Helper function to ensure HTTPS URLs
        function ensureHttps(url) {
            if (window.location.protocol === 'https:') {
                return url.replace('http:', 'https:');
            }
            return url;
        }

        function openQrScanner() {
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

        function closeQrScannerModal() {
            const qrScannerModal = document.getElementById('qrScannerModal');
            const scanResult = document.getElementById('scanResult');
            const initialState = document.getElementById('initialScanState');
            
            qrScannerModal.classList.add('hidden');
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }
            
            // Reset states
            scanResult.classList.add('hidden');
            initialState.classList.remove('hidden');
        }

        function resetScanner() {
            const scanResult = document.getElementById('scanResult');
            const initialState = document.getElementById('initialScanState');
            
            // Add fade-out effect
            scanResult.style.opacity = '0';
            
            setTimeout(() => {
                // Reset states
                scanResult.classList.add('hidden');
                initialState.classList.remove('hidden');
                
                // Clear previous attendee details
                document.getElementById('attendeeName').textContent = '';
                document.getElementById('attendeeIdno').textContent = '';
                document.getElementById('attendeeCourse').textContent = '';
                document.getElementById('attendeeYearSection').textContent = '';
                document.getElementById('attendeeStatus').innerHTML = '';
                document.getElementById('attendeeMessage').textContent = '';
                document.getElementById('scanTime').textContent = '';
                
                // Add fade-in effect
                void initialState.offsetWidth;
                initialState.style.opacity = '1';
                
                // Restart the scanner
                html5QrcodeScanner.render(onScanSuccess);
            }, 200);
        }

        function onScanSuccess(qrCode) {
            // Stop scanning after successful scan
            html5QrcodeScanner.clear();

            // Show loading state
            const scanResult = document.getElementById('scanResult');
            const initialState = document.getElementById('initialScanState');
            
            // Hide initial state and show scan result with fade effect
            initialState.style.opacity = '0';
            setTimeout(() => {
                initialState.classList.add('hidden');
                scanResult.classList.remove('hidden');
                // Trigger reflow
                void scanResult.offsetWidth;
                scanResult.style.opacity = '1';
            }, 200);
            
            document.getElementById('attendeeMessage').textContent = 'Processing...';
            document.getElementById('attendeeMessage').className = 'text-sm font-medium text-gray-600 dark:text-gray-400';
            document.getElementById('scanTime').textContent = `Scanning at ${new Date().toLocaleTimeString()}`;

            // Parse underscore-separated QR code
            let qrData;
            try {
                const cleanQrCode = qrCode.trim();
                const [registrationId, eventId, userId, timestamp] = cleanQrCode.split('_');
                
                if (!registrationId || !eventId || !userId || !timestamp) {
                    throw new Error('Invalid QR code format: missing required fields');
                }

                qrData = {
                    registration_id: registrationId,
                    event_id: eventId,
                    user_id: userId,
                    timestamp: timestamp
                };

                if (qrData.event_id !== '{{ $event->id }}') {
                    throw new Error('This QR code is for a different event');
                }

            } catch (error) {
                console.error('QR Code Parse Error:', error);
                const messageElement = document.getElementById('attendeeMessage');
                messageElement.className = 'text-sm font-medium text-red-600 dark:text-red-400';
                messageElement.textContent = error.message || 'Invalid QR code format. Please try again.';
                
                setTimeout(resetScanner, 2000);
                return;
            }

            // Get the scan URL and ensure it's HTTPS
            const scanUrl = ensureHttps('{{ route('events.attendance-slots.scan', ['event' => $event, 'attendance_slot' => $slot]) }}');

            // Send the QR code to the server
            fetch(scanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    qr_code: qrData
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Failed to record attendance');
                }

                // Update attendee details if data exists
                if (data.attendee) {
                    // Add fade-in effect for each detail
                    const details = ['Name', 'Idno', 'Course', 'YearSection'];
                    details.forEach((detail, index) => {
                        setTimeout(() => {
                            const element = document.getElementById('attendee' + detail);
                            element.textContent = detail === 'YearSection' ?
                                `Year ${data.attendee.year} - ${data.attendee.section}` :
                                data.attendee[detail.toLowerCase()] || 'N/A';
                            element.style.opacity = '0';
                            void element.offsetWidth;
                            element.style.opacity = '1';
                        }, index * 100);
                    });
                    
                    // Update status with appropriate styling and icon
                    const statusDiv = document.getElementById('attendeeStatus');
                    const statusConfig = {
                        present: {
                            class: 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border-green-200 dark:border-green-800',
                            icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                        },
                        late: {
                            class: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 border-yellow-200 dark:border-yellow-800',
                            icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                        },
                        absent: {
                            class: 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 border-red-200 dark:border-red-800',
                            icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                        }
                    };

                    const config = statusConfig[data.status] || statusConfig.absent;
                    statusDiv.innerHTML = `
                        <span class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border ${config.class}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${config.icon}
                            </svg>
                            ${data.status ? data.status.toUpperCase() : 'Unknown'}
                        </span>`;

                    // Update message with appropriate styling
                    const messageElement = document.getElementById('attendeeMessage');
                    const messageClass = data.status === 'present' ? 'text-green-600 dark:text-green-400' :
                                       data.status === 'late' ? 'text-yellow-600 dark:text-yellow-400' :
                                       'text-red-600 dark:text-red-400';
                    messageElement.className = `text-sm font-medium ${messageClass}`;
                    messageElement.textContent = data.message || getStatusMessage(data.status);
                    
                    // Update scan time with success indicator
                    document.getElementById('scanTime').textContent = `Successfully scanned at ${new Date().toLocaleTimeString()}`;
                    
                    // Reload the page after 3 seconds to show updated attendance
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    throw new Error('Invalid attendee data received');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messageElement = document.getElementById('attendeeMessage');
                messageElement.className = 'text-sm font-medium text-red-600 dark:text-red-400';
                messageElement.textContent = error.message || 'Failed to record attendance. Please try again.';
                
                setTimeout(resetScanner, 2000);
            });
        }

        function getStatusMessage(status) {
            switch(status) {
                case 'present':
                    return 'Attendance recorded successfully! You are on time.';
                case 'late':
                    return 'Attendance recorded. Please try to arrive earlier next time.';
                case 'absent':
                    return 'You have been marked as absent due to late arrival.';
                default:
                    return 'Attendance status recorded.';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('qrScannerModal');
            if (event.target == modal) {
                closeQrScannerModal();
            }
        }

        function editStatus(attendanceId, registrationId) {
            currentAttendanceId = attendanceId;
            currentRegistrationId = registrationId;
            const modal = document.getElementById('editStatusModal');
            modal.classList.remove('hidden');

            // Get current status from the row
            const statusCell = document.querySelector(`[data-attendance-id="${attendanceId}"]`);
            if (statusCell) {
                const currentStatus = statusCell.dataset.status;
                document.getElementById('status').value = currentStatus;
            }
        }

        function closeEditStatusModal() {
            const modal = document.getElementById('editStatusModal');
            modal.classList.add('hidden');
            currentAttendanceId = null;
            currentRegistrationId = null;
        }

        // Add form submission handler
        document.getElementById('editStatusForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = "{{ route('events.attendance-slots.update-status', ['event' => $event, 'attendance_slot' => $slot, 'registration' => ':registration']) }}"
                .replace(':registration', currentRegistrationId);

            fetch(ensureHttps(url), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Close modal
                    closeEditStatusModal();
                    
                    // Show success message
                    showNotification('Status updated successfully', 'success');
                    
                    // Reload page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'Failed to update status', 'error');
            });
        });

        // Notification function
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } transition-opacity duration-300`;
            notification.style.zIndex = '9999';
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        function editSlot() {
            const modal = document.getElementById('editSlotModal');
            modal.classList.remove('hidden');

            // Fetch current slot data
            fetch(ensureHttps("{{ route('events.attendance-slots.edit', ['event' => $event, 'attendance_slot' => $slot]) }}"), {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.slot) {
                    // Populate form with current values
                    document.getElementById('edit_title').value = data.slot.title;
                    document.getElementById('edit_date').value = data.slot.date;
                    document.getElementById('edit_start_time').value = data.slot.start_time;
                    document.getElementById('edit_end_time').value = data.slot.end_time;
                    document.getElementById('edit_absent_time').value = data.slot.absent_time;

                    // Set form action
                    const form = document.getElementById('editSlotForm');
                    form.action = "{{ route('events.attendance-slots.update', ['event' => $event, 'attendance_slot' => $slot]) }}";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to load attendance slot data', 'error');
            });
        }

        function closeEditSlotModal() {
            const modal = document.getElementById('editSlotModal');
            modal.classList.add('hidden');
        }

        // Add form submission handler for edit slot form
        document.getElementById('editSlotForm').addEventListener('submit', function(e) {
            e.preventDefault();

            fetch(ensureHttps(this.action), {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeEditSlotModal();
                    showNotification('Attendance slot updated successfully', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Failed to update attendance slot');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'Failed to update attendance slot', 'error');
            });
        });

        function deleteSlot(form) {
            if (confirm('Are you sure you want to delete this attendance slot?')) {
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showFloatingNotification('Attendance slot deleted successfully', 'success');
                        // Redirect to the attendance slots index page
                        window.location.href = data.redirect;
                    } else {
                        showFloatingNotification(data.message || 'Failed to delete attendance slot', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFloatingNotification('An error occurred while deleting the attendance slot', 'error');
                });
            }
        }

        function showFloatingNotification(message, type = 'success') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            
            notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${bgColor} transition-all duration-300 transform translate-y-0 opacity-100 z-50`;
            
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' 
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
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

        // Add CSS for transitions
        const style = document.createElement('style');
        style.textContent = `
            #initialScanState, #scanResult {
                transition: opacity 0.2s ease-in-out;
            }
            #attendeeName, #attendeeIdno, #attendeeCourse, #attendeeYearSection {
                transition: opacity 0.2s ease-in-out;
            }
            .hover\\:scale-\\[1\\.02\\]:hover {
                transition: transform 0.2s ease-in-out;
            }
        `;
        document.head.appendChild(style);
    </script>
    @endpush
</x-app-layout>