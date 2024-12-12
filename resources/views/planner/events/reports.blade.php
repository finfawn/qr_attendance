<x-app-layout>
    <div class="dashboard-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('Event Reports') }}
                </h2>
                <a href="{{ route('events.show', $event) }}" 
                   class="create-event-btn px-6 py-3 text-white rounded-lg transition-all duration-300 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    Back to Event
                </a>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Export and Filter Controls -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <div class="flex flex-wrap gap-4 justify-between items-center">
                    <!-- Date Range Filter -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">From:</label>
                            <input type="date" id="startDate" 
                                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">To:</label>
                            <input type="date" id="endDate" 
                                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <button onclick="filterByDate()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Apply Filter
                        </button>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button onclick="exportAsPDF()" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            PDF
                        </button>
                        
                        <button onclick="exportAsPNG()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            PNG
                        </button>

                        <button onclick="exportToExcel()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Overall Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Present Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/50">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Present</h4>
                            <div class="flex items-baseline">
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $stats['present_count'] }}
                                </p>
                                <p class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    ({{ number_format($percentages['present'], 1) }}%)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar cards for Late, Absent, and Total -->
                <!-- ... -->
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Attendance Trends Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Attendance Trends</h3>
                    <canvas id="trendChart"></canvas>
                </div>

                <!-- Course Distribution Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Course Distribution</h3>
                    <canvas id="courseChart"></canvas>
                </div>
            </div>

            <!-- Detailed Reports Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detailed Reports</h3>

                    <!-- Hierarchical Data Display -->
                    @foreach($hierarchicalStats as $course => $courseData)
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $course }}</h4>
                                <div class="text-sm text-gray-500">
                                    Total: {{ $courseData['total']['total'] }}
                                </div>
                            </div>

                            @foreach($courseData['years'] as $year => $yearData)
                                <div class="ml-6 mb-6">
                                    <h5 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-3">
                                        Year {{ $year }}
                                    </h5>

                                    @foreach($yearData['sections'] as $section => $sectionData)
                                        <div class="ml-6 mb-4">
                                            <div class="flex items-center justify-between">
                                                <h6 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Section {{ $section }}
                                                </h6>
                                                <div class="text-sm text-gray-500">
                                                    Present: {{ $sectionData['present'] }} |
                                                    Late: {{ $sectionData['late'] }} |
                                                    Absent: {{ $sectionData['absent'] }}
                                                </div>
                                            </div>

                                            <!-- Student List (Initially Hidden) -->
                                            <div class="mt-2 hidden student-list">
                                                @foreach($sectionData['students'] as $student)
                                                    <div class="ml-4 py-2 border-t border-gray-100 dark:border-gray-700">
                                                        <div class="flex justify-between items-center">
                                                            <div>
                                                                <span class="font-medium">{{ $student['name'] }}</span>
                                                                <span class="text-sm text-gray-500">({{ $student['idno'] }})</span>
                                                            </div>
                                                            <!-- Student's attendance history -->
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary Notes Section -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Summary Notes</h3>
                <textarea 
                    id="summaryNotes"
                    class="w-full p-4 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" 
                    placeholder="Add notes about this report..."
                    rows="4"></textarea>
                <div class="mt-4 flex justify-end">
                    <button onclick="saveSummaryNotes()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save Notes
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Include necessary scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    

<script>
    // Initialize charts
    const trendChart = new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['trends']['labels']) !!},
            datasets: [{
                label: 'Present',
                data: {!! json_encode($chartData['trends']['present']) !!},
                borderColor: 'rgba(34, 197, 94, 1)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true
            }, {
                label: 'Late',
                data: {!! json_encode($chartData['trends']['late']) !!},
                borderColor: 'rgba(234, 179, 8, 1)',
                backgroundColor: 'rgba(234, 179, 8, 0.1)',
                fill: true
            }, {
                label: 'Absent',
                data: {!! json_encode($chartData['trends']['absent']) !!},
                borderColor: 'rgba(239, 68, 68, 1)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Attendance Trends Over Time'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: true
                }
            }
        }
    });

    const courseChart = new Chart(document.getElementById('courseChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['courses']['labels']) !!},
            datasets: [{
                label: 'Present',
                data: {!! json_encode($chartData['courses']['present']) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
            }, {
                label: 'Late',
                data: {!! json_encode($chartData['courses']['late']) !!},
                backgroundColor: 'rgba(234, 179, 8, 0.5)',
            }, {
                label: 'Absent',
                data: {!! json_encode($chartData['courses']['absent']) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.5)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Attendance by Course'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: true
                },
                x: {
                    stacked: true
                }
            }
        }
    });

    // Date filtering functionality
    function filterByDate() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (!startDate || !endDate) {
            showFloatingNotification('Please select both start and end dates', 'warning');
            return;
        }

        // Show loading state
        showFloatingNotification('Filtering data...', 'info');

        // Reload page with date parameters
        window.location.href = `${window.location.pathname}?start_date=${startDate}&end_date=${endDate}`;
    }

    // Export functions
    function exportAsPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'a4');
        
        showFloatingNotification('Generating PDF...', 'info');

        // Get all content
        const content = document.querySelector('.max-w-7xl');
        
        html2canvas(content, {
            scale: 2,
            useCORS: true,
            logging: false
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 595.28; // A4 width
            const pageHeight = 841.89; // A4 height
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;

            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            doc.save(`${event.title}_report.pdf`);
            showFloatingNotification('PDF downloaded successfully!', 'success');
        });
    }

    function exportAsPNG() {
        showFloatingNotification('Generating PNG...', 'info');

        html2canvas(document.querySelector('.max-w-7xl'), {
            scale: 2,
            useCORS: true,
            logging: false
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `${event.title}_report.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
            
            showFloatingNotification('PNG downloaded successfully!', 'success');
        });
    }

    function exportToExcel() {
        showFloatingNotification('Generating Excel file...', 'info');

        const data = {!! json_encode($hierarchicalStats) !!};
        const wb = XLSX.utils.book_new();
        
        // Create worksheets for each type of data
        const summaryData = [
            ['Attendance Summary'],
            ['Status', 'Count', 'Percentage'],
            ['Present', {{ $stats['present_count'] }}, '{{ number_format($percentages["present"], 1) }}%'],
            ['Late', {{ $stats['late_count'] }}, '{{ number_format($percentages["late"], 1) }}%'],
            ['Absent', {{ $stats['absent_count'] }}, '{{ number_format($percentages["absent"], 1) }}%'],
            ['Total', {{ $stats['total_attendees'] }}, '100%']
        ];

        const summaryWS = XLSX.utils.aoa_to_sheet(summaryData);
        XLSX.utils.book_append_sheet(wb, summaryWS, 'Summary');

        // Create detailed report worksheet
        const detailedData = [];
        Object.entries(data).forEach(([course, courseData]) => {
            Object.entries(courseData.years).forEach(([year, yearData]) => {
                Object.entries(yearData.sections).forEach(([section, sectionData]) => {
                    detailedData.push([
                        course,
                        `Year ${year}`,
                        `Section ${section}`,
                        sectionData.present,
                        sectionData.late,
                        sectionData.absent,
                        sectionData.total
                    ]);
                });
            });
        });

        const detailedWS = XLSX.utils.aoa_to_sheet([
            ['Course', 'Year', 'Section', 'Present', 'Late', 'Absent', 'Total'],
            ...detailedData
        ]);
        XLSX.utils.book_append_sheet(wb, detailedWS, 'Detailed Report');

        // Save the file
        XLSX.writeFile(wb, `${event.title}_report.xlsx`);
        showFloatingNotification('Excel file downloaded successfully!', 'success');
    }

    // Toggle student list visibility
    document.querySelectorAll('.student-list').forEach(list => {
        const header = list.previousElementSibling;
        header.style.cursor = 'pointer';
        header.addEventListener('click', () => {
            list.classList.toggle('hidden');
        });
    });

    // Save summary notes
    function saveSummaryNotes() {
        const notes = document.getElementById('summaryNotes').value;
        
        // Here you would typically send this to the server
        // For now, we'll just show a success message
        showFloatingNotification('Summary notes saved successfully!', 'success');
    }

    // Make sure event title is available for filenames
    const event = {
        title: @json($event->title)
    };
</script>
    @endpush
</x-app-layout> 