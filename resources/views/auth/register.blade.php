<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Register as')" />
            <select id="role" name="role" class="block mt-1 w-full" required>
                <option value="attendee">Attendee</option>
                <option value="planner">Event Planner</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- ID Number -->
        <div class="mt-4">
            <x-input-label for="idno" :value="__('ID Number')" />
            <x-text-input id="idno" class="block mt-1 w-full" type="text" name="idno" :value="old('idno')" placeholder="e.g., 2023-01-0225" pattern="^\d{4}-\d{2}-\d{4}$" required />
            <x-input-error :messages="$errors->get('idno')" class="mt-2" />
        </div>

        <!-- course -->
        <div class="mt-4">
            <x-input-label for="course" :value="__('Course')" />
            <select id="course" name="course" class="block mt-1 w-full" required>
                @foreach ($courses as $course)
                    <option value="{{ $course }}">{{ $course }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('course')" class="mt-2" />
        </div>

        <!-- Year -->
        <div class="mt-4">
            <x-input-label for="year" :value="__('Year')" />
            <select id="year" name="year" class="block mt-1 w-full" required>
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('year')" class="mt-2" />
        </div>

        <!-- Section -->
        <div class="mt-4">
            <x-input-label for="section" :value="__('Section')" />
            <x-text-input id="section" class="block mt-1 w-full" type="text" name="section" :value="old('section')" required />
            <x-input-error :messages="$errors->get('section')" class="mt-2" />
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mt-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                Register
            </button>
        </div>
    </form>
</x-guest-layout>
