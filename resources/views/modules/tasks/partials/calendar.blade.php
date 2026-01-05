<!-- Calendar View Section - Independent Component -->
<div class="section-card">
    <div class="section-card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="section-card-title">
                    <i class="bx bx-calendar me-2"></i>
                    Task Calendar
                </h3>
                <p class="section-card-subtitle">Visual calendar view of all tasks and their deadlines</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="calendarViewPrev()">
                    <i class="bx bx-chevron-left"></i> Previous
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="calendarViewToday()">
                    <i class="bx bx-calendar"></i> Today
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="calendarViewNext()">
                    Next <i class="bx bx-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="section-card-body">
        <div id="calendar-container" style="min-height: 600px;">
            <div id="tasks-calendar"></div>
        </div>
    </div>
</div>

<script>
let tasksCalendar = null;
let calendarInitAttempts = 0;
const MAX_CALENDAR_INIT_ATTEMPTS = 10;

function initializeCalendar() {
    if (tasksCalendar) {
        tasksCalendar.destroy();
    }
    
    const calendarEl = document.getElementById('tasks-calendar');
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }
    
    // Check if FullCalendar is loaded - try multiple ways
    let CalendarClass = null;
    if (typeof FullCalendar !== 'undefined' && FullCalendar.Calendar) {
        CalendarClass = FullCalendar.Calendar;
    } else if (typeof window.FullCalendar !== 'undefined' && window.FullCalendar.Calendar) {
        CalendarClass = window.FullCalendar.Calendar;
    } else {
        calendarInitAttempts++;
        
        if (calendarInitAttempts >= MAX_CALENDAR_INIT_ATTEMPTS) {
            calendarEl.innerHTML = '<div class="alert alert-danger"><i class="bx bx-error-circle"></i> Failed to load calendar library. Please refresh the page.</div>';
            return;
        }
        
        // Show loading message and retry after a short delay
        calendarEl.innerHTML = '<div class="alert alert-info"><i class="bx bx-loader bx-spin"></i> Loading calendar library...</div>';
        setTimeout(function() {
            initializeCalendar();
        }, 300);
        return;
    }
    
    // Reset attempts counter on successful load
    calendarInitAttempts = 0;
    
    // Clear any loading messages
    if (calendarEl.querySelector('.alert')) {
        calendarEl.innerHTML = '';
    }
    
    tasksCalendar = new CalendarClass(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(info, successCallback, failureCallback) {
            // Fetch tasks data for the visible date range
            fetch('{{ route("modules.tasks.action") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    action: 'get_calendar_events',
                    start: info.startStr,
                    end: info.endStr
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successCallback(data.events || []);
                } else {
                    failureCallback(new Error(data.message || 'Failed to load events'));
                }
            })
            .catch(error => {
                console.error('Error loading calendar events:', error);
                failureCallback(error);
            });
        },
        eventClick: function(info) {
            if (typeof viewTaskDetails === 'function') {
                viewTaskDetails(info.event.extendedProps.taskId);
            } else {
                console.error('viewTaskDetails function not found');
            }
        },
        height: 'auto',
        eventDisplay: 'block'
    });
    
    tasksCalendar.render();
}

function calendarViewPrev() {
    if (tasksCalendar) {
        tasksCalendar.prev();
    }
}

function calendarViewToday() {
    if (tasksCalendar) {
        tasksCalendar.today();
    }
}

function calendarViewNext() {
    if (tasksCalendar) {
        tasksCalendar.next();
    }
}

// Initialize when tab is shown
$('#calendar-tab').on('shown.bs.tab', function() {
    setTimeout(initializeCalendar, 100);
});
</script>

