window.showFloatingNotification = function(message, type = 'success') {
    // Remove any existing notifications first
    const existingNotifications = document.querySelectorAll('.floating-notification');
    existingNotifications.forEach(n => n.remove());

    const notification = document.createElement('div');
    
    // Updated color scheme with lighter colors
    const bgColor = type === 'success' ? 'bg-green-100' :
                    type === 'warning' ? 'bg-yellow-100' :
                    type === 'delete' ? 'bg-red-100' :  // New specific type for delete actions
                    type === 'error' ? 'bg-red-100' :
                    'bg-gray-100';
    
    // Updated text colors to match the background
    const textColor = type === 'success' ? 'text-green-800' :
                     type === 'warning' ? 'text-yellow-800' :
                     type === 'delete' ? 'text-red-800' :  // New specific type for delete actions
                     type === 'error' ? 'text-red-800' :
                     'text-gray-800';
    
    notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg ${bgColor} ${textColor} shadow-lg transition-all duration-300 transform translate-y-0 opacity-100 z-50 floating-notification`;
    
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                    : type === 'warning'
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
                    : (type === 'delete' || type === 'error')
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'}
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Remove the notification after 10 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translate-y-2';
        setTimeout(() => notification.remove(), 300);
    }, 10000);
}

// Make it globally available
window.showNotification = window.showFloatingNotification; 