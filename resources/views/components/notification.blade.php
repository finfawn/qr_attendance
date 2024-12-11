@if (session('success') || session('error'))
    <div id="notification" class="fixed top-4 right-4 z-50 w-80 p-4 rounded-lg shadow-lg {{ session('error') ? 'bg-red-100 dark:bg-red-800' : (str_contains(strtolower(session('success')), 'delete') || str_contains(strtolower(session('success')), 'removed') ? 'bg-red-100 dark:bg-red-800' : 'bg-green-100 dark:bg-green-800') }}">
        <div class="flex justify-between items-center">
            <div class="flex-1 mr-2">
                <p class="{{ session('error') ? 'text-red-800 dark:text-red-200' : (str_contains(strtolower(session('success')), 'delete') || str_contains(strtolower(session('success')), 'removed') ? 'text-red-800 dark:text-red-200' : 'text-green-800 dark:text-green-200') }}">
                    {{ session('success') ?? session('error') }}
                </p>
            </div>
            <button onclick="closeNotification()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <script>
        function closeNotification() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }

        // Auto-hide notification after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.getElementById('notification');
            if (notification) {
                // Add transition styles
                notification.style.transition = 'opacity 0.3s ease-in-out, transform 0.3s ease-in-out';
                
                setTimeout(() => {
                    closeNotification();
                }, 5000);
            }
        });
    </script>

    <style>
        #notification {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
    </style>
@endif
