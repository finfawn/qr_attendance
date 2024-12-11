<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information Update Form -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div x-data="{ open: false, qrCode: '' }">
                <!-- QR Code Display -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Your QR Code</h3>
                        <div class="mt-4">
                            @if ($qrCodePath)
                                <!-- Trigger the modal -->
                                <img 
                                    src="{{ $qrCodePath }}" 
                                    alt="QR Code" 
                                    class="w-32 h-32 cursor-pointer" 
                                    @click="open = true; qrCode = '{{ $qrCodePath }}'"
                                >
                            @else
                                <p class="text-sm text-gray-600">QR Code not available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            
                <!-- Modal -->
                <div 
                    x-show="open" 
                    x-transition 
                    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
                    @click.self="open = false" <!-- Close modal when clicking outside -->
                >
                    <div class="relative">
                        <span @click="open = false" class="absolute top-0 right-0 p-4 text-white cursor-pointer">&times;</span>
                        <img :src="qrCode" alt="QR Code" class="max-w-full max-h-full object-contain">
                    </div>
                </div>
            </div>

            <!-- Password Update Form -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account Form -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
