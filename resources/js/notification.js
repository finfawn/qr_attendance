window.showFloatingNotification = function(message, type = 'success') {
    // Remove any existing notifications first
    const existingNotifications = document.querySelectorAll('.floating-notification');
    existingNotifications.forEach(n => n.remove());

    const notification = document.createElement('div');
    
    // Updated color scheme with lighter colors
    const bgColor = type === 'success' ? 'bg-green-100' :
                    type === 'warning' ? 'bg-yellow-100' :
                    type === 'delete' ? 'bg-red-100' :  
                    type === 'error' ? 'bg-red-100' :
                    'bg-gray-100';
    
    // Updated text colors to match the background
    const textColor = type === 'success' ? 'text-green-800' :
                     type === 'warning' ? 'text-yellow-800' :
                     type === 'delete' ? 'text-red-800' :  
                     type === 'error' ? 'text-red-800' :
                     'text-gray-800';
    
    notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg ${bgColor} ${textColor} shadow-lg transition-all duration-300 transform translate-y-0 opacity-100 z-50 floating-notification`;
    
    // Use text symbols instead of SVG
    const symbol = type === 'success' ? '✓' :
                  type === 'warning' ? '⚠' :
                  type === 'delete' ? '×' :
                  type === 'error' ? '×' : 'ℹ';
    
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <span class="font-bold text-lg">${symbol}</span>
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