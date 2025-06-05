<?php
session_start();
include('updatefunction.php'); // Ensure this file connects to your database using PDO

$competencies = []; // Initialize $competencies to an empty array

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch existing records for multiple competencies
    $sql = "SELECT * FROM idp_competencies WHERE idp_id = ? AND userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id, $_SESSION['user']]);
    $competencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$competencies) {
        die("No competencies found for this IDP.");
    }
}

// Fetch competencies excluding already accepted ones for the specific user
$sql_competencies = "SELECT competency_id, competencyname 
                     FROM competency 
                     WHERE competency_id NOT IN (
                         SELECT competency_id 
                         FROM idp_competencies 
                         WHERE idp_id IN (
                             SELECT id 
                             FROM idp 
                             WHERE status = 'Approved' AND userID = ?
                         )
                     )";
$stmt_competencies = $conn->prepare($sql_competencies);
$stmt_competencies->execute([$_SESSION['user']]);
$competencies_list = $stmt_competencies->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit AIDP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styling */
        body {
            font-family: "Be Vietnam Pro", sans-serif;
            background-color: #fdfcff;
            margin: 0;
        }

        .container {
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
        }

        .competency-section {
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .competency-section h4 {
            margin-bottom: 15px;
            color: maroon;
            font-weight: bold;
        }

        .competency-section:nth-child(odd) {
            background-color: #fdfdfd;
        }

        .competency-section:nth-child(even) {
            background-color: #f7f7f7;
        }

        .page-title {
            font-size: 32px;
            font-weight: bold;
            color: maroon;
            margin-bottom: 20px;
            text-align: center;
        }

        .btn {
            margin-top: 20px;
        }

        textarea {
            overflow: hidden;
            resize: none; /* Disable manual resizing */
            min-height: 50px; /* Ensure a reasonable default height */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="page-title">Edit Annual Individual Development Plan (AIDP)</h2>
        <form method="POST" action="updatefunction.php" class="table-responsive">
            <input type="hidden" name="idp_id" value="<?= $id ?>">
            <?php foreach ($competencies as $index => $competency): ?>
                <div class="competency-section">
                    <h4>Competency <?= $index + 1 ?></h4>
                    <input type="hidden" name="competencies[<?= $index ?>][id]" value="<?= $competency['id'] ?>">
                    <input type="hidden" name="competencies[<?= $index ?>][userID]" value="<?= $competency['userID'] ?>">
                    <table class="table table-bordered">
                        <tr>
                            <th>Priority No</th>
                            <td><input type="number" class="form-control" name="competencies[<?= $index ?>][priority_no]" value="<?= $competency['priority_no'] ?>" required></td>
                        </tr>
                        <tr>
                            <th>Competency</th>
                            <td>
                                <select name="competencies[<?= $index ?>][competency_id]" class="form-control" required>
                                    <option value="">Select Competency</option>
                                    <?php foreach ($competencies_list as $comp): ?>
                                        <option value="<?= $comp['competency_id'] ?>" <?= $comp['competency_id'] == $competency['competency_id'] ? 'selected' : '' ?>>
                                            <?= $comp['competencyname'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Workplace Learning</th>
                            <td><textarea class="form-control auto-expand" name="competencies[<?= $index ?>][workplace_learning]"><?= $competency['workplace_learning'] ?></textarea></td>
                        </tr>
                        <tr>
                            <th>Social Learning</th>
                            <td><textarea class="form-control auto-expand" name="competencies[<?= $index ?>][social_learning]"><?= $competency['social_learning'] ?></textarea></td>
                        </tr>
                        <tr>
                            <th>Structured Learning</th>
                            <td><textarea class="form-control auto-expand" name="competencies[<?= $index ?>][structured_learning]"><?= $competency['structured_learning'] ?></textarea></td>
                        </tr>
                        <tr>
                            <th>Resources Needed</th>
                            <td><input type="text" class="form-control" name="competencies[<?= $index ?>][resources_needed]" value="<?= $competency['resources_needed'] ?>" required></td>
                        </tr>
                        <tr>
                            <th>Accomplishment Indicator</th>
                            <td><input type="text" class="form-control" name="competencies[<?= $index ?>][accomplishment_indicator]" value="<?= $competency['accomplishment_indicator'] ?>" required></td>
                        </tr>
                        <tr>
                            <th>From Date</th>
                            <td>
                                <select name="competencies[<?= $index ?>][fromdate]" class="form-control" required>
                                    <option value="Q1" <?= $competency['fromdate'] == 'Q1' ? 'selected' : '' ?>>Q1</option>
                                    <option value="Q2" <?= $competency['fromdate'] == 'Q2' ? 'selected' : '' ?>>Q2</option>
                                    <option value="Q3" <?= $competency['fromdate'] == 'Q3' ? 'selected' : '' ?>>Q3</option>
                                    <option value="Q4" <?= $competency['fromdate'] == 'Q4' ? 'selected' : '' ?>>Q4</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>To Date</th>
                            <td>
                                <select name="competencies[<?= $index ?>][todate]" class="form-control" required>
                                    <option value="Q1" <?= $competency['todate'] == 'Q1' ? 'selected' : '' ?>>Q1</option>
                                    <option value="Q2" <?= $competency['todate'] == 'Q2' ? 'selected' : '' ?>>Q2</option>
                                    <option value="Q3" <?= $competency['todate'] == 'Q3' ? 'selected' : '' ?>>Q3</option>
                                    <option value="Q4" <?= $competency['todate'] == 'Q4' ? 'selected' : '' ?>>Q4</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Estimated Budget</th>
                            <td><input type="text" class="form-control" name="competencies[<?= $index ?>][estimated_budget]" value="<?= $competency['estimated_budget'] ?>" required></td>
                        </tr>
                    </table>
                </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">Update</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function autoExpand(field) {
            field.style.height = "auto"; // Reset height
            field.style.height = field.scrollHeight + "px"; // Expand to fit content
        }

        document.querySelectorAll(".auto-expand").forEach(function (textarea) {
            textarea.addEventListener("input", function () {
                autoExpand(this);
            });
            autoExpand(textarea); // Expand on page load if there's existing text
        });
    });
</script>