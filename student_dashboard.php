<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if student is logged in
if (!isset($_SESSION['student'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student'];

$conn = new mysqli("localhost", "root", "12345678", "student_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student marks
$stmt = $conn->prepare("SELECT marks.subject, marks.assessment_type, marks.marks
                        FROM marks
                        WHERE marks.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data for Chart.js
$subjects = [];
$marks = [];
$total_marks = 500; // Total possible marks

while ($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject'];
    $marks[] = $row['marks'];
}

$conn->close();

// HTML with embedded CSS and Chart.js
echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2, h4 {
            text-align: center;
            color: #333;
        }
        .logout-btn {
            margin-top: 20px;
            text-align: center;
        }
        .chart-container {
            margin-top: 30px;
            text-align: center;
            max-width: 300px; /* Adjust max-width of the chart */
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }
        /* Optional: Further styling to control the canvas size */
        canvas {
            width: 100% !important; /* Adjust canvas to fit the container */
            height: 100% !important;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Welcome to Your Dashboard</h2>
    <h4>Your Performance Overview</h4>';

    // Check if any marks were found
    if (count($subjects) > 0) {
        // Create Pie chart to show marks as a percentage of total possible marks
        echo '
        <div class="chart-container">
            <canvas id="marksChart"></canvas>
        </div>
        <script>
            var ctx = document.getElementById("marksChart").getContext("2d");
            var marksChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: ' . json_encode($subjects) . ',
                    datasets: [{
                        label: "Marks",
                        data: ' . json_encode($marks) . ',
                        backgroundColor: [
                            "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#FF5733", "#33FF57"
                        ],
                        borderColor: "#fff",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    var total = ' . $total_marks . ';
                                    var currentValue = tooltipItem.raw;
                                    var percentage = Math.floor((currentValue / total) * 100);
                                    return tooltipItem.label + ": " + currentValue + " (" + percentage + "%)";
                                }
                            }
                        }
                    }
                }
            });
        </script>';

    } else {
        echo "<div class='alert alert-info'>No marks found for you.</div>";
    }

    // Logout button to end session and redirect to student login page
    echo '
    <div class="logout-btn">
        <a href="student_logout.php" class="btn btn-danger w-100">Logout</a>
    </div>
</div>

</body>
</html>';
?>
