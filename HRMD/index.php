<?php
session_start();
require_once 'connection.php';

try {
    // Fetch office names from the database
    $stmt = $conn->prepare("SELECT officeID, officeName FROM office");
    $stmt->execute();
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching offices: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - HRMD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Ensure the main container does not expand beyond the viewport */
        .container {
            max-width: 1400px; /* Increased max-width */
            margin: 0 auto;
        }
 
        /* Ensure the sidebar does not expand beyond its content */
        .sidebar {
            width: 180px;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            background-color: #f8f9fa;
        }
        h4 {
            font-size: 20px;
            font-weight: 500;
            color: gold;
            text-align: center;
            margin-top: 20px;
            shadow: 0.5px 0.5px maroon;
            text-shadow: 0.5px 0.5px maroon;
        }
        /* Ensure the main content is properly contained */
        .main {
            margin-left: 200px;
            padding: 20px;
        }

        /* Ensure the chart container is properly contained */
        #analytics .container {
            max-width: 1400px; /* Increased max-width */
            margin: 0 auto; 
            display: grid;
            grid-template-columns: repeat(2, 1fr);  
            grid-gap: 20px;
        }

        /* Custom styles for the chart boxes */
        .chart-box {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            text-align: center;
            height: 550px; /* Adjusted height */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Ensure the chart canvas is properly contained */
        #idpChart, #trainingChart {
            max-width: 100%;
            height: 100%; /* Adjusted height to fit within the chart box */
        }

        .year-menu {
            display: flex;
            justify-content: space-between; /* Align left and right sections */
            align-items: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .left-section {
            display: flex;
            align-items: center;
            gap: 10px; /* Add spacing between elements */
        }

        .year-menu label {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .year-menu select {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .short-select {
            width: 100px; /* Shorter width for dropdowns */
        }
        .right-section {
            display: flex;
            align-items: center;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 10px;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .sidebar-navigation {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .sidebar-navigation a {
                width: 100%;
                text-align: center;
                padding: 10px;
                border-bottom: 1px solid #ddd;
            }

            #analytics .container {
                grid-template-columns: 1fr;
            }
        }

        /* Change the font color of the date cells */
        .fc-daygrid-day-number {
            color: #000000; /* Change this to your desired color */
        }

        /* Change the font color of the event titles */
        .fc-event-title {
            color: #000000; /* Change this to your desired color */
        }

        .fc-toolbar-title {
            color: #000000; /* Change this to your desired color */
        }

        /* Tooltip styles */
        .tooltip {
            position: absolute;
            z-index: 1000;
            background: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: none;
            color: #000; /* Set text color to black */
        }
        #employeeTrainingReportTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #employeeTrainingReportTable th,
        #employeeTrainingReportTable td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
            color: #686b87;
        }

        #employeeTrainingReportTable th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        #employeeTrainingReportTable tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #employeeTrainingReportTable tbody tr:hover {
            background-color: #f1f1f1;
        }
        /* Competency Chart Container */
        #competencyChartContainer {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            text-align: center;
            height: 550px; /* Adjusted height */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Competency Chart Title */
        #competencyChartContainer h1 {
            font-size: 30px !important;
            font-weight: bold;
            margin-bottom: 20px;
            color: #686b87;
        }

        /* Competency Chart Canvas */
        #competencyChart {
            max-width: 100%;
            height: 80% !important; /* Adjusted height to fit within the container */
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            #competencyChartContainer {
                height: auto;
                min-height: 400px;
            }
        }
            
    </style>
<body>
    <div class="sidebar">
        <div class="logo">
            <a href="index.php" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
                <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
                <a href="../Homepage/logout.php" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
                    <h4>HRMD Portal</h4>
            </a>
        </div>
        <nav class="sidebar-navigation">
            <a href="index.php" class="sidebar-link active">Home</a>
            <a href="../HRMD/idpdashboard.php" class="sidebar-link">IDP</a>
            <a href="../HRMD/trainingrequestdashboard.php" class="sidebar-link">Training History</a>
        </nav>
        <nav class="sidebar-navigation side-nav">
            <a href="../Profile/profiledisplay.php" class="icon-button" aria-label="Profile"><i class="bi bi-person-circle"></i> Profile</a>
            <a href="../Homepage/logout.php" class="icon-button" aria-label="Logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </div>

    <main class="main">
        <!-- Year Selection Menu -->
        <div class="year-menu">
            <div class="left-section">
                <label for="yearSelect">Year:</label>
                <select id="yearSelect" class="short-select">
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                </select>

                <label for="officeSelect" style="margin-left: 10px;">Office:</label>
                <select id="officeSelect" class="short-select">
                    <option value="all">All Offices</option>
                    <?php foreach ($offices as $office): ?>
                        <option value="<?php echo htmlspecialchars($office['officeID']); ?>">
                            <?php echo htmlspecialchars($office['officeName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <section class="py-1" id="analytics">
            <div class="container py-5 row gx-5 justify-content-center">
                <div class="chart-box">
                    <div class="text-center my-5">
                        <h1 style="font-size: 30px !important;" class="display-5 fw-bolder">Individual Development Plan</h1>
                        <canvas id="idpChart"></canvas>
                    </div>
                </div>
                <div class="chart-box">
                    <div class="text-center my-5">
                        <h1 style="font-size: 30px !important;" class="display-5 fw-bolder">Training Requests</h1>
                        <canvas id="trainingChart"></canvas>
                    </div>
                </div>
                
            </div>
            <br>
            <div class="competencyChartContainer">
                    <div class="text-center my-5">
                        <h1 style="font-size: 30px !important; text-align: center !important;" class="display-5 fw-bolder">Competency Selections</h1>
                        <canvas id="competencyChart"></canvas>
                    </div>
                </div>
                <br>
                <br>
 
            <div class="chart-box">
                <div class="text-center my-5">
                    <h1 style="font-size: 30px !important; text-align: center !important;" class="display-5 fw-bolder">Employees Training Report</h1>
                    <table id="employeeTrainingReportTable" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Position</th>
                                <th>Number of Completed Trainings</th>
                                <th>Total Training Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" style="text-align: center;">No data available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://unpkg.com/phosphor-icons'></script>
    <script>
        let idpChartInstance;
        let trainingChartInstance;
        let competencyChartInstance;

        document.addEventListener('DOMContentLoaded', function () {
        const yearSelect = document.getElementById('yearSelect');
        const officeSelect = document.getElementById('officeSelect');

        function updateCharts() {
        const selectedYear = yearSelect.value;
        const selectedOffice = officeSelect.value;

        updateIdpChart(selectedYear, selectedOffice);
        updateTrainingChart(selectedYear, selectedOffice);
        updateCompetencyChart(selectedYear, selectedOffice);
        updateEmployeeTrainingReport(selectedYear, selectedOffice);
    }

        yearSelect.addEventListener('change', updateCharts);
        officeSelect.addEventListener('change', updateCharts);

        // Initialize charts with the default year and office
        updateCharts();
    });
    function updateIdpChart(year, office) {
    fetch(`get_idp_data.php?year=${year}&office=${office}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('idpChart').getContext('2d');
            if (idpChartInstance) {
                idpChartInstance.destroy();
            }

            const formattedData = data.reduce((acc, item) => {
                acc[item.status_key] = {
                    count: parseInt(item.count),
                    employees: item.employees ? item.employees.split(',') : []
                };
                return acc;
            }, {
                submitted: { count: 0, employees: [] },
                to_be_reviewed: { count: 0, employees: [] },
                approved: { count: 0, employees: [] },
                rejected: { count: 0, employees: [] }
            });

            idpChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Submitted', 'To Be Reviewed', 'Approved', 'Rejected'],
                    datasets: [{
                        label: 'Number of Employees',
                        data: [
                            formattedData.submitted.count,
                            formattedData.to_be_reviewed.count,
                            formattedData.approved.count,
                            formattedData.rejected.count
                        ],
                        backgroundColor: [
                            'rgba(0, 128, 128, 0.2)',
                            'rgba(255, 165, 0, 0.2)',
                            'rgba(255, 255, 0, 0.2)',
                            'rgba(255, 0, 0, 0.2)'
                        ],
                        borderColor: [
                            'rgba(0, 128, 128, 1)',
                            'rgba(255, 165, 0, 1)',
                            'rgba(255, 255, 0, 1)',
                            'rgba(255, 0, 0, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const statusMap = {
                                        'Submitted': 'submitted',
                                        'To Be Reviewed': 'to_be_reviewed',
                                        'Approved': 'approved',
                                        'Rejected': 'rejected'
                                    };
                                    const status = statusMap[context.label];
                                    const employees = formattedData[status].employees;

                                    // Format employee names vertically
                                    return employees.length > 0
                                        ? 'Employees:\n' + employees.join('\n')
                                        : 'No employees';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching IDP data:', error));
}
function updateTrainingChart(year, office) {
    fetch(`get_training_data.php?year=${year}&office=${office}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('trainingChart').getContext('2d');
            if (trainingChartInstance) {
                trainingChartInstance.destroy();
            }

            // Format the data to include employee names
            const formattedData = data.reduce((acc, item) => {
                acc[item.status_key] = {
                    count: parseInt(item.count),
                    employees: item.employees ? item.employees.split(',') : []
                };
                return acc;
            }, {
                submitted: { count: 0, employees: [] },
                to_be_reviewed: { count: 0, employees: [] },
                approved: { count: 0, employees: [] },
                rejected: { count: 0, employees: [] },
                completed: { count: 0, employees: [] }
            });

            trainingChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Submitted', 'To Be Reviewed', 'Approved', 'Rejected', 'Completed'],
                    datasets: [{
                        label: 'Number of Employees',
                        data: [
                            formattedData.submitted.count,
                            formattedData.to_be_reviewed.count,
                            formattedData.approved.count,
                            formattedData.rejected.count,
                            formattedData.completed.count
                        ],
                        backgroundColor: [
                            'rgba(0, 128, 128, 0.2)', // Teal for 'Submitted'
                            'rgba(255, 165, 0, 0.2)', // Orange for 'To Be Reviewed'
                            'rgba(255, 255, 0, 0.2)', // Yellow for 'Approved'
                            'rgba(255, 0, 0, 0.2)', // Red for 'Rejected'
                            'rgba(0, 128, 0, 0.2)' // Green for 'Completed'
                        ],
                        borderColor: [
                            'rgba(0, 128, 128, 1)', // Teal for 'Submitted'
                            'rgba(255, 165, 0, 1)', // Orange for 'To Be Reviewed'
                            'rgba(255, 255, 0, 1)', // Yellow for 'Approved'
                            'rgba(255, 0, 0, 1)', // Red for 'Rejected'
                            'rgba(0, 128, 0, 1)' // Green for 'Completed'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const statusMap = {
                                        'Submitted': 'submitted',
                                        'To Be Reviewed': 'to_be_reviewed',
                                        'Approved': 'approved',
                                        'Rejected': 'rejected',
                                        'Completed': 'completed'
                                    };
                                    const status = statusMap[context.label];
                                    const employees = formattedData[status].employees;

                                    // Format employee names vertically
                                    return employees.length > 0
                                        ? 'Employees:\n' + employees.join('\n')
                                        : 'No employees';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching Training Requests data:', error));
}

function updateCompetencyChart(year, office) {
    fetch(`get_competency_data.php?year=${year}&office=${office}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('competencyChart').getContext('2d');
            if (competencyChartInstance) {
                competencyChartInstance.destroy();
            }

            // Calculate the maximum value in the dataset
            const maxValue = Math.max(...data.competencies.map(c => c.count));

            competencyChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.competencies.map(c => c.name),
                    datasets: [{
                        label: 'Competency Selections',
                        data: data.competencies.map(c => c.count),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                max: maxValue, // Set the maximum value dynamically
                                stepSize: 1, // Ensure whole number steps
                                callback: function (value) {
                                    return Math.floor(value); // Ensure whole numbers
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching Competency data:', error));
}
function updateEmployeeTrainingReport(year, office) {
    // Fetch both training report and training hours data
    Promise.all([
        fetch(`get_training_report.php?year=${year}&office=${office}`).then(res => res.json()),
        fetch(`get_training_hours.php?year=${year}&office=${office}`).then(res => res.json())
    ])
    .then(([trainingData, hoursData]) => {
        const tableBody = document.querySelector('#employeeTrainingReportTable tbody');
        tableBody.innerHTML = ''; // Clear existing rows

        // Create a map to combine data by employee name
        const combinedData = new Map();

        // Process training data
        trainingData.forEach(row => {
            combinedData.set(row.employee_name, {
                employee_name: row.employee_name,
                position: '',
                completed_count: row.completed_training_count,
                total_hours: 0
            });
        });

        // Process hours data
        hoursData.forEach(row => {
            if (combinedData.has(row.employee_name)) {
                // Update existing entry
                const existing = combinedData.get(row.employee_name);
                existing.position = row.employee_position;
                existing.total_hours = row.total_hours;
            } else {
                // Create new entry
                combinedData.set(row.employee_name, {
                    employee_name: row.employee_name,
                    position: row.employee_position,
                    completed_count: 0,
                    total_hours: row.total_hours
                });
            }
        });

        // Check if we have any data
        if (combinedData.size === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" style="text-align: center;">No data available</td></tr>';
            return;
        }

        // Create table rows
        combinedData.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.employee_name}</td>
                <td>${row.position || 'N/A'}</td>
                <td>${row.completed_count}</td>
                <td>${row.total_hours}</td>
            `;
            tableBody.appendChild(tr);
        });
    })
    .catch(error => {
        console.error('Error fetching employee training report data:', error);
        const tableBody = document.querySelector('#employeeTrainingReportTable tbody');
        tableBody.innerHTML = '<tr><td colspan="4" style="text-align: center;">Error loading data</td></tr>';
    });
}

    </script>
</body>
</html>