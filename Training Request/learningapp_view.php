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

    // Fetch data from the learningapplication table for the given training ID
    $learningAppQuery = "SELECT * FROM learningapplication WHERE trainingid = :trainingid";
    $learningAppStmt = $conn->prepare($learningAppQuery);
    $learningAppStmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
    $learningAppStmt->execute();
    $learningAppResult = $learningAppStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch data from the learningapplication table for the logged-in user
    $learningAppQuery = "SELECT * FROM learningapplication WHERE userID = :userID";
    $learningAppStmt = $conn->prepare($learningAppQuery);
    $learningAppStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $learningAppStmt->execute();
    $learningAppResult = $learningAppStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Application Plan Preview</title>
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
            max-height: 90vh;
            overflow: hidden; /* Changed from overflow-y: auto */
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            width: 85px;
            height: auto;
        }

        .header-text {
            flex: 1;
            text-align: left;
            line-height: 0.4;
        }

        .form-title {
            text-align: center;
            margin: 0px 0;
            line-height: 0.5;
        }

        .form-title h4 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: black !important;
        }

        .form-container {
            border: 1px solid black;
            padding: 15px;
            margin-top: 10px;
            font-weight: normal;
            color: black;
        }
        .form-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px 40px;
        }

        .checkboxes {
            color: black;
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            line-height: 0;
        }

        .checkbox-item {
            display: block;
            margin: 5px 0;
            color: black;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 5px;
            vertical-align: middle;
        }

        .section-title {
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
            color: black;
        }


        .section-content {
            border-bottom: 1px solid black;
            padding: 8px 0;
            min-height: 24px;
            width: 100%;
            margin-bottom: 5px;
            color: black;
            vertical-align: bottom;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            color: black;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            font-size: 12px;
            color: black;
        }

        th {
            background-color: white;
            color: black;
            font-weight: bold;
        }

        .commitment {
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
        }

        .sign-line {
            width: 40%;
            border-top: 1px solid black;
            margin-top: 40px;
            display: inline-block;
            text-align: center;
            font-weight: bold;
        }

        @media print {
            .modal-overlay {
                position: static;
                padding: 0;
                background: none;
            }

            .modal-content {
                box-shadow: none;
            }
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

        .close-btn {
            background-color: grey;
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
        }

        .attachments-section {
            margin-top: 20px;
            padding: 15px;
            border-top: 1px solid #ddd;
        }

        .file-list {
            margin-top: 10px;
        }

        .file-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .file-link {
            color: maroon;
            text-decoration: none;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .no-files {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Learning Application Plan</h5>
            <div class="modal-buttons">
                <button onclick="printLearningApp()" class="print-btn">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button onclick="closeModal()" class="close-btn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>ss
            <!-- Header Section -->
        <div class="form-content">
            <div class="header">
                <img src="../assets/msu-iit-logo.png" class="logo" alt="MSU-IIT Logo">
                <div class="header-text">
                    <p style="color: black;">Republic of the Philippines</p>
                    <p style="font-family: 'Monotype Corsiva', 'Times New Roman', cursive; font-weight: bold; font-size: 16px; color: red; ">Mindanao State University</p>
                    <p style="font-weight: bold; color: red;">ILIGAN INSTITUTE OF TECHNOLOGY</p>
                    <p style="color: black;">Iligan City 9200 Philippines</p>
                    <a href="http://www.msuiit.edu.ph" style="text-decoration: none;">http://www.msuiit.edu.ph</a>
                </div>
                <div style="color: red; font-size: 12px;">L&D Form No. 3</div>
            </div>

            <hr style="border-top: 2px dashed black;">

            <!-- Form Title -->
            <div class="form-title">
                <h4>LEARNING APPLICATION PLAN</h4>
                <p style="color: black;">(To be submitted to HRMD together with Terminal Report of attendee)</p>
                <p style="color: green;">[CSC PRIME-HRM Evidence]</p>
            </div>
            <?php if (!empty($learningAppResult)): ?>
                <?php foreach ($learningAppResult as $row): ?>
                    <div class="checkboxes" style="margin: 5px 5px;">
                        <label class="checkbox-item">
                            <input type="checkbox" disabled <?= strpos($row['type'], 'Training') !== false ? 'checked' : ''; ?>> Training
                        </label><br>
                        <label class="checkbox-item">
                            <input type="checkbox" disabled <?= strpos($row['type'], 'Seminar') !== false ? 'checked' : ''; ?>> Seminar/Symposium/Workshop/Conference/Convention/Online attendance
                        </label><br>
                        <label class="checkbox-item">
                            <input type="checkbox" disabled <?= strpos($row['type'], 'Others') !== false ? 'checked' : ''; ?>> Others (please specify): 
                            <?php if(!empty($row['other_type'])): ?>
                                <span style="text-decoration: underline;"><?= htmlspecialchars($row['other_type']); ?></span>
                            <?php endif; ?>
                        </label>
                    </div>
            <div class="form-container">

                        <!-- Form Fields -->
                        <div class="section-title">TITLE:</div>
                        <div class="section-content"><?= htmlspecialchars($row['title']); ?></div>

                        <div class="section-title">ORGANIZER/SPONSOR OF PROGRAM:</div>
                        <div class="section-content"><?= htmlspecialchars($row['organizer']); ?></div>

                        <div class="section-title">INCLUSIVE DATES:</div>
                        <table>
                            <tr>
                                <td width="25%">
                                    FROM<br>
                                    <?= htmlspecialchars($row['from_date']); ?>
                                </td>
                                <td width="25%">
                                    TO<br>
                                    <?= htmlspecialchars($row['to_date']); ?>
                                </td>
                                <td width="25%">
                                    NO. OF DAYS<br>
                                    <?= htmlspecialchars($row['num_days']); ?>
                                </td>
                                <td width="25%">
                                    NO. OF HOURS<br>
                                    <?= htmlspecialchars($row['num_hours']); ?>
                                </td>
                            </tr>
                        </table>

                        <div class="section-title">VENUE:</div>
                        <div class="section-content"><?= htmlspecialchars($row['venue']); ?></div>

                        <div class="section-title">BRIEF LISTING OF LEARNING:</div>
                        <div class="section-content" style="min-height: 100px;"><?= nl2br(htmlspecialchars($row['brief_learning'])); ?></div>

                        <div class="section-title">RECOMMENDATION/ACTION STEPS TO APPLY LEARNING AT WORK:</div>
                        <table>
                            <tr>
                                <th>Function</th>
                                <th>Activity</th>
                                <th>Period</th>
                                <th>Resource Needed</th>
                                <th>Monitoring & Evaluation</th>
                            </tr>
                            <tr>
                                <td width="20%"><?= nl2br(htmlspecialchars($row['function'])); ?></td>
                                <td width="20%"><?= nl2br(htmlspecialchars($row['activity'])); ?></td>
                                <td width="20%"><?= nl2br(htmlspecialchars($row['period'])); ?></td>
                                <td width="20%"><?= nl2br(htmlspecialchars($row['resource_needed'])); ?></td>
                                <td width="20%"><?= nl2br(htmlspecialchars($row['moneval'])); ?></td>
                            </tr>
                        </table>

                        <p class="commitment">We hereby commit ourselves to implement this recommendation or action steps.</p>

                        <div style="text-align: center;">
                            <div class="sign-line">Employee</div>
                            <div class="sign-line" style="margin-left: 30px;">Supervisor</div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <p class="hrmd-receipt" style="margin-top: 30px; color: black;">Received copy for HRMD-L&D by: ___________________________</p>
            <div class="file-section">
                        <div class="section-title">ATTACHMENTS:</div>
                        <?php if (!empty($row['file_path'])): ?>
                            <?php 
                            $filePaths = explode(',', $row['file_path']); 
                            foreach ($filePaths as $filePath): 
                                $filePath = trim($filePath);
                                $fileName = basename($filePath);
                            ?>
                                <div>
                                    <a href="/HRLynk/Terminal Report/<?= htmlspecialchars($filePath); ?>" 
                                       target="_blank"
                                       class="file-link">
                                        <?= htmlspecialchars($fileName); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div>No attachments available</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
function printLearningApp() {
    // Create a clone of the content
    const contentClone = document.querySelector('.form-content').cloneNode(true);
    
    // Remove the attachments section from the clone
    const attachmentsSection = contentClone.querySelector('.file-section');
    if (attachmentsSection) {
        attachmentsSection.remove();
    }
    
    const printWindow = window.open('', '', 'width=800,height=1000');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Learning Application Plan</title>
            <style>
                @page {
                    size: 8.27in 11.69in;
                    margin: 0;
                }
                body {
                    font-family: "Times New Roman", Times, serif;
                    font-size: 12px;
                    line-height: 1;
                    color: black;
                    margin: 0;
                    padding: 0;
                }
                .form-content {
                    width: 8.27in;
                    padding: 0.5in 0.5in;
                    box-sizing: border-box;
                    margin: 0 auto;
                }
                .header {
                    display: flex;
                    align-items: left;
                    gap: 15px;
                    margin-bottom: 10px;
                }
                .logo {
                    width: 85px;
                    height: auto;
                }
                .header-text {
                    flex: 1;
                    text-align: left;
                    line-height: 0.5;
                }
                .form-title {
                    text-align: center;
                    margin: 20px 0;
                }
                .form-container {
                    border: 1px solid black;
                    padding: 15px;
                    margin-top: 10px;
                }
                .checkboxes {
                    margin: 15px 0;
                    line-height: 0;
                }
                .checkbox-item {
                    display: block;
                    margin: 5px 0;
                }
                .section-title {
                    font-weight: bold;
                    margin-top: 10px;
                }
                .section-content {
                    border-bottom: 1px solid black;
                    padding: 2px 0;
                    min-height: 24px;
                    vertical-align: bottom;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0;
                }
                th, td {
                    border: 1px solid black;
                    padding: 8px;
                    text-align: left;
                }
                .commitment {
                    margin-top: 20px;
                    font-weight: bold;
                }
                .sign-line {
                    width: 40%;
                    border-top: 1px solid black;
                    margin-top: 40px;
                    display: inline-block;
                    text-align: center;
                }
                .hrmd-receipt {
                    margin-top: 30px;
                }
                .file-section {
                    margin-top: 20px;
                    page-break-inside: avoid;
                }
                @media print {
                    body {
                        width: 8.27in;
                        height: 11.69in;
                    }
                    .form-content {
                        border: none;
                    }
                    .modal-header,
                    .modal-buttons {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="form-content">
                ${contentClone.innerHTML}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }, 500);
}

function closeModal() {
    if (window.parent !== window) {
        window.parent.postMessage('closeLearningModal', '*');
    } else {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            modal.remove();
            document.body.style.overflow = '';
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }
    }
}

</script>
</body>
</html>