<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard - User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Planners Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Event Planners</h3>
                @forelse($planners as $course => $coursePlanners)
                    <div class="mb-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <button onclick="toggleSection('planners-{{ Str::slug($course) }}')" class="flex justify-between items-center w-full">
                                <span class="text-lg font-medium">{{ $course ?? 'Uncategorized' }}</span>
                                <span class="text-sm text-gray-500">({{ $coursePlanners->count() }} planners)</span>
                            </button>
                        </div>
                        <div id="planners-{{ Str::slug($course) }}" class="hidden">
                            <div class="p-4">
                                <!-- Desktop View -->
                                <div class="hidden md:block">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Name</th>
                                                <th class="text-left">ID Number</th>
                                                <th class="text-left">Email Status</th>
                                                <th class="text-left">Events</th>
                                                <th class="text-left">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($coursePlanners as $planner)
                                                <tr class="border-t">
                                                    <td class="py-2">{{ $planner->name }}</td>
                                                    <td>{{ $planner->idno ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="px-2 py-1 rounded text-sm {{ $planner->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $planner->email_verified_at ? 'Verified' : 'Unverified' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button onclick="toggleEvents('planner-{{ $planner->id }}')" class="text-blue-600 hover:text-blue-800">
                                                            View Events ({{ $planner->events->count() }})
                                                        </button>
                                                        <div id="planner-{{ $planner->id }}" class="hidden mt-2 pl-4">
                                                            @if($planner->events->count() > 0)
                                                                <ul class="list-disc">
                                                                    @foreach($planner->events as $event)
                                                                        <li>{{ $event->title }} ({{ $event->date }})</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p class="text-gray-500">No events created</p>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('admin.users.delete', $planner) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this planner?')" 
                                                                class="text-red-600 hover:text-red-800">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Mobile View -->
                                <div class="md:hidden space-y-4">
                                    @foreach($coursePlanners as $planner)
                                        <div class="border rounded-lg p-4">
                                            <h4 class="font-semibold">{{ $planner->name }}</h4>
                                            <p class="text-sm text-gray-600">ID: {{ $planner->idno ?? 'N/A' }}</p>
                                            <div class="mt-2">
                                                <span class="px-2 py-1 rounded text-sm {{ $planner->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $planner->email_verified_at ? 'Verified' : 'Unverified' }}
                                                </span>
                                            </div>
                                            <div class="mt-3">
                                                <button onclick="toggleEvents('planner-mobile-{{ $planner->id }}')" class="text-blue-600">
                                                    View Events ({{ $planner->events->count() }})
                                                </button>
                                                <div id="planner-mobile-{{ $planner->id }}" class="hidden mt-2">
                                                    @if($planner->events->count() > 0)
                                                        <ul class="list-disc pl-4">
                                                            @foreach($planner->events as $event)
                                                                <li>{{ $event->title }} ({{ $event->date }})</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-gray-500">No events created</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <form action="{{ route('admin.users.delete', $planner) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this planner?')" 
                                                        class="text-red-600">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No planners found.</p>
                @endforelse
            </div>

            <!-- Attendees Section -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Attendees</h3>
                @forelse($attendees as $course => $courseAttendees)
                    <div class="mb-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <button onclick="toggleSection('attendees-{{ Str::slug($course) }}')" class="flex justify-between items-center w-full">
                                <span class="text-lg font-medium">{{ $course ?? 'Uncategorized' }}</span>
                                <span class="text-sm text-gray-500">({{ $courseAttendees->count() }} attendees)</span>
                            </button>
                        </div>
                        <div id="attendees-{{ Str::slug($course) }}" class="hidden">
                            <div class="p-4">
                                <!-- Desktop View -->
                                <div class="hidden md:block">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Name</th>
                                                <th class="text-left">ID Number</th>
                                                <th class="text-left">Email Status</th>
                                                <th class="text-left">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($courseAttendees as $attendee)
                                                <tr class="border-t">
                                                    <td class="py-2">{{ $attendee->name }}</td>
                                                    <td>{{ $attendee->idno ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="px-2 py-1 rounded text-sm {{ $attendee->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $attendee->email_verified_at ? 'Verified' : 'Unverified' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('admin.users.delete', $attendee) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this attendee?')" 
                                                                class="text-red-600 hover:text-red-800">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Mobile View -->
                                <div class="md:hidden space-y-4">
                                    @foreach($courseAttendees as $attendee)
                                        <div class="border rounded-lg p-4">
                                            <h4 class="font-semibold">{{ $attendee->name }}</h4>
                                            <p class="text-sm text-gray-600">ID: {{ $attendee->idno ?? 'N/A' }}</p>
                                            <div class="mt-2">
                                                <span class="px-2 py-1 rounded text-sm {{ $attendee->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $attendee->email_verified_at ? 'Verified' : 'Unverified' }}
                                                </span>
                                            </div>
                                            <div class="mt-3">
                                                <form action="{{ route('admin.users.delete', $attendee) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this attendee?')" 
                                                        class="text-red-600">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No attendees found.</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            section.classList.toggle('hidden');
        }

        function toggleEvents(eventId) {
            const events = document.getElementById(eventId);
            events.classList.toggle('hidden');
        }
    </script>
    @endpush
</x-app-layout>