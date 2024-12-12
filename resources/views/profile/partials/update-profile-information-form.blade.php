<section class="max-w-2xl p-6 mx-auto bg-white rounded-md shadow-md dark:bg-gray-800">
    <header class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200 capitalize">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-gray-700 dark:text-gray-200" />
            <x-text-input id="name" name="name" type="text" 
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" 
                :value="old('name', $user->name)" 
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-gray-700 dark:text-gray-200" />
            <x-text-input id="email" name="email" type="email" 
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" 
                :value="old('email', $user->email)" 
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- ID Number -->
        <div>
            <x-input-label for="idno" :value="__('ID Number')" class="text-gray-700 dark:text-gray-200" />
            <x-text-input id="idno" name="idno" type="text" 
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" 
                :value="old('idno', $user->idno)" 
                required placeholder="YYYY-MM-DDDD" />
            <x-input-error class="mt-2" :messages="$errors->get('idno')" />
        </div>

        <!-- Course -->
        <div>
            <x-input-label for="course" :value="__('Course')" class="text-gray-700 dark:text-gray-200" />
            <select id="course" name="course" 
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40"
                required>
                <option value="{{ old('course', $user->course) }}" selected>{{ old('course', $user->course) }}</option>
                @foreach ($courses as $course)
                    <option value="{{ $course }}">{{ $course }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('course')" />
        </div>

        <!-- Year Level -->
        <div>
            <x-input-label for="year" :value="__('Year Level')" class="text-gray-700 dark:text-gray-200" />
            <select id="year" name="year" 
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40"
                required>
                <option value="{{ old('year', $user->year) }}" selected>{{ old('year', $user->year) }}</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('year')" />
        </div>

        <!-- Section -->
        <div>
            <x-input-label for="section" :value="__('Section')" class="text-gray-700 dark:text-gray-200" />
            <x-text-input id="section" name="section" type="text" 
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" 
                :value="old('section', $user->section)" 
                required />
            <x-input-error class="mt-2" :messages="$errors->get('section')" />
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/50 rounded-md">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" 
                        class="underline text-sm text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-gray-800">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif

        <div class="flex items-center justify-end pt-4 border-t dark:border-gray-700">
            <x-primary-button class="px-6 py-2 leading-5 text-white transition-colors duration-200 transform bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">
                {{ __('Save Changes') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="ml-3 text-sm text-green-600 dark:text-green-400">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
