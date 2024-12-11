import './bootstrap';
import './notification';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Import QR Scanner
import QrScanner from 'qr-scanner';

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    const scanButton = document.getElementById('scanQrCodeBtn');
    const modal = document.getElementById('qrScannerModal');
    const closeBtn = document.getElementById('closeQrScannerBtn');
    const videoElem = document.getElementById('qr-video');
    let qrScanner = null;

    if (scanButton) {
        console.log('Scan button found');
        scanButton.addEventListener('click', async function() {
            console.log('Scan button clicked');
            if (modal) {
                modal.classList.remove('hidden');
                console.log('Modal shown');
                await startScanner();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
            if (qrScanner) {
                qrScanner.stop();
            }
        });
    }

    async function startScanner() {
        try {
            if (!qrScanner) {
                qrScanner = new QrScanner(
                    videoElem,
                    result => onScanSuccess(result.data),
                    {
                        highlightScanRegion: true,
                        highlightCodeOutline: true,
                    }
                );
            }
            await qrScanner.start();
            console.log('Scanner started');
        } catch (error) {
            console.error('Scanner error:', error);
        }
    }

    function onScanSuccess(qrCodeMessage) {
        console.log('QR Code scanned:', qrCodeMessage);
        // Stop scanning once we get a result
        if (qrScanner) {
            qrScanner.stop();
        }

        // Send the QR code to the server for verification
        fetch(`/attendance/verify-qr/${qrCodeMessage}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const scanResult = document.getElementById('scanResult');
            scanResult.classList.remove('hidden');
            const resultElement = scanResult.querySelector('p');
            
            if (data.success) {
                scanResult.classList.add('bg-green-100', 'text-green-800');
                resultElement.textContent = 'Attendance marked successfully!';
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                scanResult.classList.add('bg-red-100', 'text-red-800');
                resultElement.textContent = data.message || 'Invalid QR code';
                startScanner();
            }
        })
        .catch(error => {
            const scanResult = document.getElementById('scanResult');
            scanResult.classList.remove('hidden');
            scanResult.classList.add('bg-red-100', 'text-red-800');
            scanResult.querySelector('p').textContent = 'Error processing QR code';
            startScanner();
        });
    }
});

// Initialize QR Scanner with proper worker path
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Starting script execution');
    
    // Copy QR Scanner worker file during build
    const workerUrl = new URL(
        '../../node_modules/qr-scanner/qr-scanner-worker.min.js',
        import.meta.url
    );
    
    // Set worker path
    QrScanner.WORKER_PATH = workerUrl.pathname;

    const addAttendeeModal = document.getElementById('addAttendeeModal');
    const addAttendeeBtn = document.getElementById('addAttendeeBtn');
    const closeAddAttendeeBtn = document.getElementById('closeAddAttendeeBtn');
    const cancelAddAttendee = document.getElementById('cancelAddAttendee');
    const registeredListToggle = document.getElementById('registeredListToggle');
    const registeredListContent = document.getElementById('registeredListContent');
    const dropdownArrow = document.getElementById('dropdownArrow');

    console.log('Modal element found:', addAttendeeModal);
    console.log('Button element found:', addAttendeeBtn);
    console.log('Close button element found:', closeAddAttendeeBtn);
    console.log('Cancel button element found:', cancelAddAttendee);
    console.log('Registered list toggle element found:', registeredListToggle);
    console.log('Registered list content element found:', registeredListContent);
    console.log('Dropdown arrow element found:', dropdownArrow);

    // Registered List Dropdown
    if (registeredListToggle && registeredListContent && dropdownArrow) {
        console.log('Registered list toggle is present - Adding click listener');
        registeredListToggle.addEventListener('click', function(e) {
            console.log('Registered list toggle clicked');
            console.log('Event:', e);
            registeredListContent.classList.toggle('hidden');
            dropdownArrow.classList.toggle('rotate-180');
            this.setAttribute('aria-expanded', 
                this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true'
            );
        });
    } else {
        console.error('Registered list toggle or content or dropdown arrow not found during initialization');
    }

    // Add Attendee Modal Functionality
    if (addAttendeeBtn && addAttendeeModal) {
        console.log('Adding click listener to Add Attendee button');
        addAttendeeBtn.addEventListener('click', () => {
            console.log('Add Attendee button clicked');
            addAttendeeModal.classList.remove('hidden');
            addAttendeeModal.setAttribute('aria-hidden', 'false');
        });

        // Close modal with X button
        closeAddAttendeeBtn?.addEventListener('click', function() {
            console.log('Close Add Attendee button clicked');
            addAttendeeModal.classList.add('hidden');
            addAttendeeModal.setAttribute('aria-hidden', 'true');
        });

        // Close modal with Cancel button
        cancelAddAttendee?.addEventListener('click', function() {
            console.log('Cancel Add Attendee button clicked');
            addAttendeeModal.classList.add('hidden');
            addAttendeeModal.setAttribute('aria-hidden', 'true');
        });

        // Close modal when clicking outside
        addAttendeeModal.addEventListener('click', function(e) {
            if (e.target === addAttendeeModal) {
                addAttendeeModal.classList.add('hidden');
                addAttendeeModal.setAttribute('aria-hidden', 'true');
            }
        });
    } else {
        console.error('Add Attendee button or modal not found:', { 
            buttonFound: !!addAttendeeBtn, 
            modalFound: !!addAttendeeModal 
        });
    }

    // Rest of your existing code...
    function openEditModal(attendanceId) {
        const modal = document.getElementById('editRegistrationModal');
        const form = document.getElementById('editRegistrationForm');
        const registrationIdInput = document.getElementById('editRegistrationId');
        
        console.log('Opening edit modal for attendance ID:', attendanceId);
        console.log('Modal element found:', modal);
        console.log('Form element found:', form);
        console.log('Registration ID input element found:', registrationIdInput);
        
        // Set the form action and attendance ID
        form.action = "{{ route('attendance.update', ['event' => $event->id, 'attendance' => ':attendance_id']) }}".replace(':attendance_id', attendanceId);
        registrationIdInput.value = attendanceId;
        
        // Get current status and set it in the select
        const statusCell = document.querySelector(`[data-attendance-id="${attendanceId}"]`);
        if (statusCell) {
            const currentStatus = statusCell.getAttribute('data-status');
            const statusSelect = document.getElementById('status');
            statusSelect.value = currentStatus;
        }
        
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('editRegistrationModal');
        console.log('Closing edit modal');
        console.log('Modal element found:', modal);
        modal.classList.add('hidden');
    }

    // QR Scanner Modal
    const qrScannerModal = document.getElementById('qrScannerModal');
    const scanQrCodeBtn = document.getElementById('scanQrCodeBtn');
    const closeQrScannerBtn = document.getElementById('closeQrScannerBtn');
    const scanResult = document.getElementById('scanResult');
    const videoElem = document.getElementById('qr-video');
    let qrScanner = null;

    // Initialize QR Scanner
    if (scanQrCodeBtn && videoElem) {
        qrScanner = new QrScanner(
            videoElem,
            result => onScanSuccess(result.data),
            {
                highlightScanRegion: true,
                highlightCodeOutline: true,
                returnDetailedScanResult: true,
            }
        );

        // Open Scanner
        scanQrCodeBtn.addEventListener('click', function() {
            qrScannerModal.classList.remove('hidden');
            qrScanner.start();
        });

        // Close Scanner
        closeQrScannerBtn?.addEventListener('click', function() {
            qrScannerModal.classList.add('hidden');
            qrScanner.stop();
        });
    }

    function onScanSuccess(decodedText) {
        // Stop scanning
        qrScanner.stop();

        // Show result
        scanResult.classList.remove('hidden');
        scanResult.querySelector('p').textContent = 'Processing...';

        // Send to server
        fetch('/attendance/record', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                qr_code: decodedText,
                event_id: eventId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showFloatingNotification(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showFloatingNotification(data.message, 'warning');
                qrScanner.start(); // Restart scanner after error
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showFloatingNotification('Error recording attendance', 'error');
            qrScanner.start(); // Restart scanner after error
        });
    }
});
