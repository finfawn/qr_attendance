@php
    $timezone = config('app.timezone', 'Asia/Manila');
    $now = now()->timezone($timezone);
    
    // Parse dates with timezone
    $startDateTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->start_time)->timezone($timezone);
    $endDateTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->end_time)->timezone($timezone);
    $absentDateTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->absent_time)->timezone($timezone);
    
    // Check if the dates match first
    $isToday = $now->isSameDay($startDateTime);
    
    // Determine primary status
    if ($now->lt($startDateTime)) {
        $status = 'upcoming';
        $statusText = 'Upcoming';
        $statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300';
    } elseif ($isToday && $now->between($startDateTime, $endDateTime)) {
        $status = 'active';
        $statusText = 'Active Now';
        $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300';
    } else {
        $status = 'ended';
        $statusText = 'Ended';
        $statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-300';
    }

    // Determine if current
    $isCurrent = $isToday && $now->between($startDateTime, $absentDateTime);
@endphp

<!-- Primary Status Badge -->
@if ($isCurrent)
    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 animate-pulse">
        Current
    </span>
@else
    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
        {{ $statusText }}
    </span>
@endif 