document.addEventListener('DOMContentLoaded', function() {
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let events = [];

    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    // Fetch events from the database
    function fetchEvents() {
        fetch('/api/events/public')
            .then(response => response.json())
            .then(data => {
                events = data.map(event => ({
                    ...event,
                    date: new Date(event.date)
                }));
                updateCalendar();
            })
            .catch(error => console.error('Error fetching events:', error));
    }

    function updateCalendar() {
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const startingDay = firstDay.getDay();
        const monthLength = lastDay.getDate();

        // Update month and year display
        document.getElementById('currentMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;

        const calendarDays = document.getElementById('calendarDays');
        calendarDays.innerHTML = '';

        // Create grid cells for days
        for (let i = 0; i < startingDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'calendar-cell empty';
            calendarDays.appendChild(emptyCell);
        }

        for (let day = 1; day <= monthLength; day++) {
            const cell = document.createElement('div');
            cell.className = 'calendar-cell';
            
            const span = document.createElement('span');
            span.textContent = day;
            cell.appendChild(span);

            // Check if it's today
            const currentDateStr = new Date().toDateString();
            const cellDateStr = new Date(currentYear, currentMonth, day).toDateString();
            if (currentDateStr === cellDateStr) {
                cell.classList.add('today');
            }

            // Check if day has events
            const hasEvent = events.some(event => 
                event.date.getDate() === day && 
                event.date.getMonth() === currentMonth && 
                event.date.getFullYear() === currentYear
            );
            if (hasEvent) {
                cell.classList.add('has-event');
            }

            cell.addEventListener('click', () => showEvents(day));
            calendarDays.appendChild(cell);
        }

        // Update today's events
        updateTodayEvents();
    }

    function showEvents(day) {
        const selectedDate = new Date(currentYear, currentMonth, day);
        const dayEvents = events.filter(event => 
            event.date.getDate() === day && 
            event.date.getMonth() === currentMonth && 
            event.date.getFullYear() === currentYear
        );

        const eventList = document.getElementById('todayEvents');
        eventList.innerHTML = '';

        if (dayEvents.length === 0) {
            eventList.innerHTML = '<p class="text-muted">No events scheduled for this day.</p>';
            return;
        }

        dayEvents.forEach(event => {
            const eventItem = document.createElement('div');
            eventItem.className = 'event-item';
            eventItem.innerHTML = `
                <h5 class="mb-1">${event.title}</h5>
                <p class="mb-1"><i class="bi bi-clock me-2"></i>${event.time}</p>
                <p class="mb-0"><i class="bi bi-geo-alt me-2"></i>${event.location}</p>
                ${event.description ? `<p class="mt-2 text-muted">${event.description}</p>` : ''}
            `;
            eventList.appendChild(eventItem);
        });
    }

    function updateTodayEvents() {
        const today = new Date();
        if (today.getMonth() === currentMonth && today.getFullYear() === currentYear) {
            showEvents(today.getDate());
        } else {
            document.getElementById('todayEvents').innerHTML = 
                '<p class="text-muted">No events scheduled for today.</p>';
        }
    }

    // Event listeners for navigation buttons
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        updateCalendar();
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        updateCalendar();
    });

    // Initialize calendar
    fetchEvents();
});
