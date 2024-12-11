<div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-200 relative group">
    <!-- Status Badge - Top Left -->
    @php
        $statusClass = $now->between($startTime, $endTime) ? 'bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800' :
                      ($now->between($endTime, $absentTime) ? 'bg-yellow-50 text-yellow-700 border-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800' :
                      'bg-gray-50 text-gray-700 border-gray-100 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800');
    @endphp
    <div class="absolute top-3 left-3">
        <span class="px-2.5 py-1 text-xs font-medium rounded-full border {{ $statusClass }}">
            {{ $now->between($startTime, $endTime) ? 'Active' : 
               ($now->between($endTime, $absentTime) ? 'Late Period' : 'Ended') }}
        </span>
    </div>

    <!-- Delete Button - Top Right -->
    <form action="{{ route('events.attendance-slots.destroy', ['event' => $event, 'attendance_slot' => $slot]) }}" 
        method="POST" 
        class="absolute top-2 right-2 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
        @csrf
        @method('DELETE')
        <button type="submit" 
            class="p-1.5 rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/50 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    </form>

    <!-- Card Content -->
    <div class="p-6">
        <div class="flex flex-col">
            <!-- Title -->
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
                {{ $slot->title }}
            </h4>

            <!-- Time Info -->
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($slot->date)->format('F d, Y') }}
                </div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                </div>
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Cutoff: {{ \Carbon\Carbon::parse($slot->absent_time)->format('h:i A') }}
                </div>
            </div>

            <!-- Attendee Count -->
            <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="attendee-count" data-slot-id="{{ $slot->id }}">
                        {{ $slot->attendances->count() }} Attendees
                    </span>
                </span>
                <span class="text-sm text-blue-600 dark:text-blue-400">View Details →</span>
            </div>
        </div>
    </div>

    <!-- Clickable overlay -->
    <a href="{{ route('events.attendance-slots.show', ['event' => $event, 'attendance_slot' => $slot]) }}" 
        class="absolute inset-0 z-10">
        <span class="sr-only">View details for {{ $slot->title }}</span>
    </a>
</div> 