<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Calendar - IFMMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <style>
        .priority-urgent { background-color: #FEE2E2; border-color: #EF4444; }
        .priority-due-soon { background-color: #FEF3C7; border-color: #F59E0B; }
        .priority-scheduled { background-color: #DBEAFE; border-color: #3B82F6; }
        .priority-completed { background-color: #D1FAE5; border-color: #10B981; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-12 gap-6">
            <!-- Calendar Section -->
            <div class="col-span-9">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- Resource Panel -->
            <div class="col-span-3 space-y-6">
                <!-- Tasks Legend -->
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <h3 class="font-bold mb-4">Priority Levels</h3>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded priority-urgent mr-2"></div>
                            <span>Urgent</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded priority-due-soon mr-2"></div>
                            <span>Due Soon</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded priority-scheduled mr-2"></div>
                            <span>Scheduled</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded priority-completed mr-2"></div>
                            <span>Completed</span>
                        </div>
                    </div>
                </div>

                <!-- Resource Allocation -->
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <h3 class="font-bold mb-4">Resources</h3>
                    <div class="space-y-4">
                        <!-- Mechanics -->
                        <div>
                            <h4 class="text-sm font-semibold mb-2">Mechanics Available</h4>
                            <div class="flex items-center justify-between text-sm">
                                <span>Total: 8</span>
                                <span class="text-green-600">Available: 5</span>
                            </div>
                        </div>
                        <!-- Service Bays -->
                        <div>
                            <h4 class="text-sm font-semibold mb-2">Service Bays</h4>
                            <div class="flex items-center justify-between text-sm">
                                <span>Total: 6</span>
                                <span class="text-green-600">Available: 3</span>
                            </div>
                        </div>
                        <!-- Parts Status -->
                        <div>
                            <h4 class="text-sm font-semibold mb-2">Parts Status</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Critical Parts</span>
                                    <span class="text-green-600">In Stock</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Regular Parts</span>
                                    <span class="text-yellow-600">Low Stock</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                editable: true,
                droppable: true,
                events: [
                    {
                        title: 'Oil Change - Truck 101',
                        start: '2024-01-15',
                        className: 'priority-scheduled'
                    },
                    {
                        title: 'Brake Inspection - Truck 203',
                        start: '2024-01-16',
                        className: 'priority-urgent'
                    }
                ],
                eventDrop: function(info) {
                    // Handle event drag & drop
                    console.log('Event dropped on: ' + info.event.startStr);
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
