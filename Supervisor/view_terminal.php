<?php
session_start();
// Include database connection
include 'connection.php';

if (!isset($_SESSION['user'])) {
    // User is not authenticated, redirect to login page or display an error message
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user'];

if (isset($_GET['trainingid'])) {
    $trainingid = $_GET['trainingid'];

    // Fetch data from the terminal table for the given training ID
    $terminalQuery = "SELECT * FROM terminal WHERE trainingid = :trainingid";
    $terminalStmt = $conn->prepare($terminalQuery);
    $terminalStmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
    $terminalStmt->execute();
    $terminalResult = $terminalStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch data from the terminal table for the logged-in user
    $terminalQuery = "SELECT * FROM terminal WHERE userID = :userID";
    $terminalStmt = $conn->prepare($terminalQuery);
    $terminalStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $terminalStmt->execute();
    $terminalResult = $terminalStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Define the base folder for file paths
// Adjust this path based on your folder structure
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Terminal Report</title>
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
        }
        .modal-content {
            background: white;
            width: 875px;
            padding: 0;
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            color: black;
            position: relative;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }
        .close-btn {
            color: white;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .form-container {
            width: 800px;
            padding: 30px 40px;
            margin: 0 auto;
            border: 1px solid black;
            box-sizing: border-box;
            background: white;
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            color: black;
            line-height: 1.3;
            overflow-y: auto;
            flex: 1;
        }


        /* Copy all styles from terminalform.php */
        .header {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        .header-text {
            font-size: 13px;
            line-height: 0.2;
        }

        .logo-container img {
            height: 85px;
            width: auto;
        }

        .dashed-line {
            border-bottom: 2px dashed black;
            margin: 15px 0;
        }

        .center-title {
            text-align: center;
            margin: 15px 0;
            line-height: 1.5;
        }

        .center-title p {
            margin: 5px 0;
        }

        .center-title strong {
            font-size: 16px;
            display: block;
            margin-bottom: 5px;
        }

        .form-section {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid black;
        }

        .form-section label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-value {
            margin: 5px 0;
            min-height: 20px;
        }

        .checkboxes {
            margin: 15px 0;
            line-height: 1.5;
        }

        .checkbox-item {
            margin: 5px 0;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 5px;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .signature-block {
            text-align: left;
            flex: 1;
            margin: 0 15px;
        }

        .signature-line {
            border-bottom: 1px solid black;
            width: 70%;
            margin: 30px 0 5px;  /* Changed from margin: 30px auto 5px */
        }
        .modal-header {
            background-color: maroon;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .modal-title {
            font-family: "Times New Roman", Times, serif;
            font-size: 18px;
            margin: 0;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .print-btn {
            background-color: transparent;
            color: white;
            border: 1px solid white;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            background-color: white;
            color: maroon;
        }

        .close-btn {
            background-color: transparent;
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .date-row {
            display: flex;
            gap: 10px;
            margin-bottom: 2px;
        }

        .date-display {
            width: 120px;
            height: 24px;
            border: 1px solid black;
            padding: 2px 5px;
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            text-align: center;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .number-display {
            width: 80px;
            height: 24px;
            border: 1px solid black;
            padding: 2px 5px;
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            text-align: center;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
        }

        .date-labels {
            display: flex;
            gap: 10px;
            margin-top: 2px;
        }

        .date-labels span {
            width: 120px;
            text-align: center;
            font-size: 12px;
            color: black;
            vertical-align: middle;
        }
        .close-btn:hover {
            transform: scale(1.1);
        }

        @media print {
            .modal-header {
                display: none;
            }
            .modal-overlay {
                position: static;
                background: none;
                padding: 0;
            }
            .modal-content {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="modal-overlay">
        
        <?php if (!empty($terminalResult)): ?>
            <?php foreach ($terminalResult as $row): ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Terminal Report</h5>
                        <div class="modal-buttons">
                            <button onclick="printTerminalReport()" class="print-btn">
                                <i class="bi bi-printer"></i> Print
                            </button>
                            <button onclick="closeModal()" class="close-btn">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-container">
                        <div class="header">
                            <div class="logo-container">
                                <img src="../assets/msu-iit-logo.png" alt="MSU-IIT Logo">
                            </div>
                            <div class="header-text">
                                <p>Republic of the Philippines</p>
                                <p style="font-family: 'Monotype Corsiva', 'Times New Roman', cursive; font-weight: bold; font-size: 16px; color: red;">Mindanao State University</p>
                                <p style="font-weight: bold; color: red;">ILIGAN INSTITUTE OF TECHNOLOGY</p>
                                <p>Iligan City 9200 Philippines</p>
                                <p>http://www.msuiit.edu.ph</p>
                            </div>
                            <div style="margin-left: auto; text-align: right; font-size: 13px;">
                                <p>L&D Form No. 02</p>
                                <p>(TERMINAL REPORT)</p>
                            </div>
                        </div>

                        <div class="dashed-line"></div>

                        <div class="center-title">
                            <p><strong>TERMINAL REPORT</strong></p>
                            <p style="font-size: 13px;">(To be submitted to the HRMD by L&D Attendee together with L&D Form No. 3)</p>
                        </div>

                        <div class="checkboxes">
                            <label class="checkbox-item">
                                <input type="checkbox" disabled <?php echo (strpos($row['type'], 'Training') !== false) ? 'checked' : ''; ?>> Training
                            </label><br>
                            <label class="checkbox-item">
                                <input type="checkbox" disabled <?php echo (strpos($row['type'], 'Seminar') !== false) ? 'checked' : ''; ?>> Seminar/Symposium/Workshop/Conference/Convention/Online attendance
                            </label><br>
                            <label class="checkbox-item">
                                <input type="checkbox" disabled <?php echo (strpos($row['type'], 'Others') !== false) ? 'checked' : ''; ?>> Others (please specify): 
                                <?php if(!empty($row['others_specify'])): ?>
                                    <span style="text-decoration: underline;"><?php echo htmlspecialchars($row['others_specify']); ?></span>
                                <?php endif; ?>
                            </label>
                        </div>


                 <div class="form-section">
                            <label>TITLE:</label>
                            <div class="form-value"><?php echo htmlspecialchars($row['title']); ?></div>
                        </div>

                <div class="form-section">
                    <label>ORGANIZER/SPONSOR OF PROGRAM:</label>
                    <div class="form-value"><?= htmlspecialchars($row['sponsor']); ?></div>
                </div>

                <div class="form-section">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 2;">
                            <label>INCLUSIVE DATES:</label>
                            <div class="date-row">
                                <div class="date-display"><?= htmlspecialchars($row['fromdate']); ?></div>
                                <div class="date-display"><?= htmlspecialchars($row['todate']); ?></div>
                            </div>
                            <div class="date-labels">
                                <span>FROM</span>
                                <span>TO</span>
                            </div>
                        </div>
                        <div style="flex: 1;">
                            <label>NO. OF DAYS</label>
                            <div class="number-display"><?= htmlspecialchars($row['days']); ?></div>
                        </div>
                        <div style="flex: 1;">
                            <label>NO. OF HOURS</label>
                            <div class="number-display"><?= htmlspecialchars($row['hours']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <label>VENUE:</label>
                    <div class="form-value"><?= htmlspecialchars($row['venue']); ?></div>
                </div>

                <div class="form-section">
                    <label>OBJECTIVES:</label>
                    <div class="form-value"><?= nl2br(htmlspecialchars($row['objectives'])); ?></div>
                </div>

                <div class="form-section">
                    <label>BRIEF REPORT OF UNDERTAKING:</label>
                    <div class="form-value"><?= nl2br(htmlspecialchars($row['briefreport'])); ?></div>
                </div>

                <div class="form-section">
                    <label>SYNTHESIS OF LEARNING:</label>
                    <div class="form-value"><?= nl2br(htmlspecialchars($row['synthesis'])); ?></div>
                </div>
                <div class="form-section">
                    <label>ATTACHMENTS:</label>
                    <div style="line-height: 1; margin-left: 5px;">
                        <p>( ) Photocopy of proof of participation/attendance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;( ) Special Order</p>
                        <p>( ) Recommendation/Action Steps on how to apply the learning in work site.</p>
                    </div>
                </div>                    
                <div class="signature-section">
                            <div class="signature-block">
                                <p><strong>Prepared and Submitted by:</strong></p>
                                <div class="signature-line"></div>
                                <p>Attendee/Participant</p>
                            </div>
                            <div class="signature-block">
                                <p><strong>Reviewed/Evaluated by:</strong></p>
                                <div class="signature-line"></div>
                                <p>Cost Center Head</p>
                            </div>
                            
                        </div>
                        <div class="form-section" style="margin-top: 20px; border: none;">
                                <p style="margin-left: 15px;">Received at HRMD by: <span style="border-bottom: 1px solid black; display: inline-block; width: 300px;">&nbsp;</span></p>
                            </div>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="modal-content">

                <div class="form-container">
                    <p style="text-align: center; padding: 20px;">No terminal report data available.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<script>
function printTerminalReport() {
    const content = document.querySelector('.form-container').innerHTML;
    const printWindow = window.open('', '', 'width=800,height=1000');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Terminal Report</title>
            <style>
                @page {
                    size: 8.27in 11.69in;
                    margin: 0;
                }
                body {
                    font-family: "Times New Roman", Times, serif;
                    font-size: 12px;
                    line-height: 1.2;
                    color: black;
                    margin: 0;
                    padding: 0;
                }
                .form-container {
                    width: 8.27in;
                    padding: 0.5in 0.5in;
                    box-sizing: border-box;
                    margin: 0 auto;
                }
                .header {
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                    margin-bottom: 10px;
                }
                .logo-container img {
                    height: 60px;
                    width: auto;
                }
                .header-text {
                    font-size: 12px;
                    line-height: 1;
                }
                .header-text p {
                    margin: 1px 0;
                }
                .dashed-line {
                    border-bottom: 1px dashed black;
                    margin: 10px 0;
                }
                .form-section {
                    margin: 15px 0;
                    line-height: 1.2;
                }
                .form-value {
                    border: 1px solid black;
                    padding: 8px;
                    min-height: 24px;
                }
                .signature-section {
                    margin-top: 30px;
                    display: flex;
                    justify-content: space-between;
                }
                .signature-block {
                    text-align: center;
                    flex: 1;
                    margin: 0 15px;
                }
                .signature-line {
                    border-bottom: 1px solid black;
                    width: 70%;
                    margin: 30px auto 5px;
                }
                .center-title {
                    text-align: center;
                    margin: 15px 0;
                    line-height: 1.5;
                }

                .center-title p {
                    margin: 5px 0;
                }

                .center-title strong {
                    font-size: 16px;
                    display: block;
                    margin-bottom: 5px;
                }
                .form-section div[style*="line-height: 2.5"] {
                    line-height: 1 !important;
                    margin-left: 15px;
                }
                 .date-container {
                    margin-top: 5px;
                }
                .date-row {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                }
                .date-display {
                    width: 120px;
                    height: 24px;
                    border: 1px solid black;
                    padding: 2px 5px;
                    font-family: "Times New Roman", Times, serif;
                    font-size: 12px;
                    text-align: center;
                    background: white;
                    vertical-align: middle;
                }
                .number-display {
                    width: 80px;
                    height: 24px;
                    border: 1px solid black;
                    padding: 2px 5px;
                    font-family: "Times New Roman", Times, serif;
                    font-size: 12px;
                    text-align: center;
                    background: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    vertical-align: middle;
                }
                .date-labels {
                    display: flex;
                    gap: 10px;
                    margin-top: 2px;
                    font-size: 12px;
                }
                .date-labels span {
                    width: 120px;
                    text-align: center;
                }
                .date-labels span:nth-child(3),
                .date-labels span:nth-child(4) {
                    width: 80px;
                }       
                @media print {
                    body {
                        width: 8.27in;
                        height: 11.69in;
                    }
                    .form-container {
                        border: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="form-container">
                ${content}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for images to load before printing
    setTimeout(() => {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }, 500);
}
function closeModal() {
    if (window.parent !== window) {
        window.parent.postMessage('closeTerminalModal', '*');
    } else {
        const modal = document.querySelector('.modal-overlay');
        const body = document.body;
        
        if (modal) {
            // Remove the modal completely instead of just hiding it
            modal.remove();
            
            // Reset body styles
            body.style.overflow = '';
            body.classList.remove('modal-open');
            
            // Remove any backdrop elements
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }
    }
}
// Add this event listener after your existing JavaScript
$(document).ready(function() {
    window.addEventListener('message', function(event) {
        if (event.data === 'closeModal') {
            // Hide and remove the modal
            $('#terminalModal').modal('hide').remove();
            // Remove the backdrop
            $('.modal-backdrop').remove();
            // Reset body styles
            $('body')
                .removeClass('modal-open')
                .css({
                    'overflow': '',
                    'padding-right': ''
                });
            // Remove any remaining white overlay
            $('.modal-overlay').remove();
        }
    });
});
</script>