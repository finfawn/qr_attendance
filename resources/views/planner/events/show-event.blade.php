<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Event Details') }}
            </h2>
            <a href="{{ route('planner.dashboard') }}" 
               class="text-gray-600 hover:text-gray-900 dark:hover:text-gray-400 inline-flex items-center"
               title="Back to Dashboard">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                
            </a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Manage registration Button Section -->
        <div class="mb-6">
            <a href="{{ route('events.manage-attendance', $event) }}"
               class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg shadow-lg flex items-center justify-center space-x-3 transition-all duration-200 transform hover:scale-[1.02]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="text-xl">Manage Attendance</span>
            </a>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Event Header -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $event->title }}
                                    </h3>
                                    <div class="mt-2">
                                        <span class="px-3 py-1 text-sm rounded-full {{ $event->getStatusColorClass() }}">
                                            {{ App\Models\Event::$statuses[$event->status] }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-6 ml-6">
                                    <form action="{{ route('events.update', $event) }}" method="POST" class="inline">
                                        @csrf
                                        <select name="status" 
                                                onchange="this.form.submit()"
                                                class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-1 py-1.5">
                                            @foreach(App\Models\Event::$statuses as $value => $label)
                                                <option value="{{ $value }}" {{ $event->status === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="title" value="{{ $event->title }}">
                                        <input type="hidden" name="description" value="{{ $event->description }}">
                                        <input type="hidden" name="date" value="{{ $event->date }}">
                                        <input type="hidden" name="start_time" value="{{ $event->start_time }}">
                                        <input type="hidden" name="end_time" value="{{ $event->end_time }}">
                                        <input type="hidden" name="location" value="{{ $event->location }}">
                                    </form>
                                    <a href="{{ route('events.edit', $event) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 dark:hover:text-yellow-400 inline-flex items-center"
                                       title="Edit Event">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="text-red-600 hover:text-red-900 dark:hover:text-red-400 p-1"
                                            title="Delete Event"
                                            onclick="return confirm('Are you sure you want to delete this event?')">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Event Information Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date and Time Section -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 1118 0 9 9 0 01-18 0z" />
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</h4>
                                            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</h4>
                                            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }} - 
                                                {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location and Details Section -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</h4>
                                            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-gray-100">
                                                {{ $event->location }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h4>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">
                                        {{ $event->description ?? 'No description provided.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code and Event Code Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <!-- QR Code -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                                <h3 class="text-lg font-semibold mb-6 text-gray-700 dark:text-gray-300 text-center">
                                    Event QR Code
                                </h3>
                                @if($qrCodeUrl)
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="bg-white p-3 rounded-lg shadow-sm">
                                            <img src="{{ $qrCodeUrl }}" 
                                                 alt="Event QR Code" 
                                                 class="w-40 h-40 object-contain">
                                        </div>
                                        <button onclick="downloadQRCode()" 
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Download QR
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Event Code -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                                <h3 class="text-lg font-semibold mb-6 text-gray-700 dark:text-gray-300 text-center">
                                    Event Code
                                </h3>
                                <div class="flex flex-col items-center space-y-4">
                                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 rounded-lg">
                                        <div class="text-2xl font-mono font-bold text-gray-700 dark:text-gray-300 tracking-wide">
                                            {{ $eventCode }}
                                        </div>
                                    </div>
                                    <button onclick="copyEventCode()" 
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Copy Code
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!-- Registration List -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Registration List ({{ $event->registrations->count() }})
                        </h3>
                        <button id="addAttendeeBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2m0 0H8m4 0h4m-4-8a3 3 0 100 6 3 3 0 000-6z" />
                            </svg>
                            Add Attendee
                        </button>
                    </div>

                    <!-- Add Attendee Modal -->
                    <div id="addAttendeeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-lg mx-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Add Attendee</h3>
                                <button id="closeAddAttendeeBtn" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <form action="{{ route('registration.store', $event) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Student</label>
                                    <select name="user_id" id="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" required>
                                        <option value="">Select a student</option>
                                        @php
                                            $registeredUserIds = $event->registrations->pluck('user_id')->toArray();
                                            $availableUsers = \App\Models\User::where('role', 'attendee')
                                                ->whereNotIn('id', $registeredUserIds)
                                                ->orderBy('name')
                                                ->get();
                                        @endphp
                                        @foreach($availableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->idno }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex justify-end mt-6">
                                    <x-secondary-button type="button" class="mr-3" id="cancelAddAttendee">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>
                                    <x-primary-button type="submit">
                                        {{ __('Add Attendee') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($event->registrations->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Number</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                                    @foreach($event->registrations as $registration)
                                        <tr class="{{ $registration->getRowColorClass() }} transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $registration->user->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $registration->user->idno }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $registration->user?->course ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $registration->getStatusColorClass() }}" 
                                                      data-registration-id="{{ $registration->id }}" 
                                                      data-status="{{ $registration->status }}">
                                                    {{ ucfirst($registration->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-3">
                                                    @if($registration->status === 'pending')
                                                        <form action="{{ route('registration.update', ['event' => $event->id, 'registration' => $registration->id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" class="text-green-600 hover:text-green-900 dark:hover:text-green-400">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('registration.update', ['event' => $event->id, 'registration' => $registration->id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button onclick="openEditModal('{{ $registration->id }}')" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    <form action="{{ route('registration.destroy', ['event' => $event->id, 'registration' => $registration->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this registration record?');">
                                                        @csrf
                                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-4">
                            @foreach($event->registrations as $registration)
                                <div class="bg-white dark:bg-gray-700 shadow rounded-lg p-4">
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $registration->user->name }}
                                            </div>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $registration->getStatusColorClass() }}">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <p>ID Number: {{ $registration->user->idno }}</p>
                                            <p>Course: {{ $registration->user->course ?? 'N/A' }}</p>
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            @if($registration->status === 'pending')
                                                <form action="{{ route('registration.update', ['event' => $event->id, 'registration' => $registration->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="text-green-600 hover:text-green-900 p-2">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('registration.update', ['event' => $event->id, 'registration' => $registration->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 p-2">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <button onclick="openEditModal('{{ $registration->id }}')" class="text-blue-600 hover:text-blue-900 p-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            <form action="{{ route('registration.destroy', ['event' => $event->id, 'registration' => $registration->id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 p-2" onclick="return confirm('Are you sure you want to delete this registration?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No attendees yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Registration Modal -->
    <div id="editRegistrationModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg p-8 max-w-lg w-full">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Registration Status</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="editRegistrationForm" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" id="editRegistrationId" name="registration_id">
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

    <style>
        #notification {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
    </style>
</x-app-layout>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addAttendeeBtn = document.getElementById('addAttendeeBtn');
        const addAttendeeModal = document.getElementById('addAttendeeModal');
        const closeAddAttendeeBtn = document.getElementById('closeAddAttendeeBtn');
        const cancelAddAttendeeBtn = document.getElementById('cancelAddAttendee');

        // Function to show modal
        function showModal() {
            addAttendeeModal.classList.remove('hidden');
        }

        // Function to hide modal
        function hideModal() {
            addAttendeeModal.classList.add('hidden');
        }

        // Add both click and touch events for opening modal
        addAttendeeBtn.addEventListener('click', showModal);
        addAttendeeBtn.addEventListener('touchstart', function(e) {
            e.preventDefault(); // Prevent double-firing on touch devices
            showModal();
        });

        // Add both click and touch events for closing modal
        closeAddAttendeeBtn.addEventListener('click', hideModal);
        closeAddAttendeeBtn.addEventListener('touchstart', function(e) {
            e.preventDefault();
            hideModal();
        });

        cancelAddAttendeeBtn.addEventListener('click', hideModal);
        cancelAddAttendeeBtn.addEventListener('touchstart', function(e) {
            e.preventDefault();
            hideModal();
        });

        // Close modal when clicking outside
        addAttendeeModal.addEventListener('click', function(e) {
            if (e.target === addAttendeeModal) {
                hideModal();
            }
        });

        // Add form submission handling
        const addAttendeeForm = document.querySelector('form[action="{{ route("registration.store", $event) }}"]');
        if (addAttendeeForm) {
            addAttendeeForm.addEventListener('submit', function(e) {
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = 'Adding...';
            });
        }

        // If there are any errors, show the modal
        @if($errors->any())
            showModal();
        @endif
    });

    const eventId = {{ $event->id }};

    function openEditModal(registrationId) {
        const modal = document.getElementById('editRegistrationModal');
        const form = document.getElementById('editRegistrationForm');
        const registrationIdInput = document.getElementById('editRegistrationId');
        
        // Update the form action URL to match the route we defined
        form.action = `/planner/events/${eventId}/registration/${registrationId}/update`;
        registrationIdInput.value = registrationId;
        
        // Get current status and set it in the select
        const statusCell = document.querySelector(`[data-registration-id="${registrationId}"]`);
        if (statusCell) {
            const currentStatus = statusCell.getAttribute('data-status');
            const statusSelect = document.getElementById('status');
            statusSelect.value = currentStatus;
        }
        
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('editRegistrationModal');
        modal.classList.add('hidden');
    }

    function downloadQRCode() {
            const link = document.createElement('a');
            link.href = "{{ $qrCodeUrl }}";
            link.download = "event-qr-code.png";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function copyEventCode() {
            navigator.clipboard.writeText("{{ $eventCode }}");
            alert('Event code copied to clipboard!');
        }
</script>
