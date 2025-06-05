<?php
include('connection.php');
$year = date('Y');
// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // echo "Debug: ID received is " . htmlspecialchars($id) . "<br>";

    // Check if ID exists in IDP table
    $stmt_test = $conn->prepare("SELECT * FROM idp WHERE id = :id");
    $stmt_test->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_test->execute();
    // if (!$stmt_test->fetch()) {
    //     echo "Debug: No matching ID found in IDP table.<br>";
    // }

    // Fetch IDP details along with user and user_details information
    $stmt = $conn->prepare("
    SELECT idp.id, idp.objectives, idp.other_objective, users.empname,
        office.officeName AS officeName, user_details.position, user_details.jobdescription, 
        user_details.employmentstatus, user_details.datehired, 
        user_details.monthsintheposition, user_details.yearsiniit
    FROM idp
    JOIN users ON idp.userID = users.userID
    LEFT JOIN user_details ON users.userID = user_details.userID
    LEFT JOIN office ON user_details.officeID = office.officeID
    WHERE idp.id = :id
    ");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$idp_details = $stmt->fetch(PDO::FETCH_ASSOC);
    // if (!$idp_details) {
    //     echo "Debug: Query executed but returned no data.<br>";
    // } else {
    //     print_r($idp_details); // Output raw data for debugging
    // }

    // Fetch competencies linked to this IDP
    $stmt_competencies = $conn->prepare("
        SELECT ic.priority_no, c.competencyname, ic.workplace_learning, 
               ic.social_learning, ic.structured_learning, ic.resources_needed, 
               ic.accomplishment_indicator, ic.fromdate, ic.todate, ic.estimated_budget
        FROM idp_competencies ic
        JOIN competency c ON ic.competency_id = c.competency_id
        WHERE ic.idp_id = :id
        ORDER BY ic.priority_no ASC
    ");
    $stmt_competencies->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_competencies->execute();
    $competencies = $stmt_competencies->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the name of the supervisor and the date of approval
    // Output the data
    if ($idp_details) {
        echo "<div style='text-align: center; font-weight: bold; font-size: 20px;'>
        ANNUAL INDIVIDUAL DEVELOPMENT PLAN OF EMPLOYEE
      </div>";
        echo "<div style='text-align: center; font-weight: bold; font-size: 18px;'>
        Period: January 1 to December 31, $year
      </div>";
        echo "<br>";
        echo "<table cellspacing='5' cellpadding='5' width='100%'>";
        
        echo "<tr>
                <td><strong>Name of Employee:</strong></td>
                <td>" . htmlspecialchars($idp_details['empname']) . "</td>
                <td><strong>Office:</strong></td>
                <td>" . htmlspecialchars($idp_details['officeName']) . "</td>
              </tr>";
    
        echo "<tr>
                <td><strong>Current Position:</strong></td>
                <td>" . htmlspecialchars($idp_details['position']) . "</td>
                <td><strong>Status:</strong></td>
                <td>" . htmlspecialchars($idp_details['employmentstatus']) . "</td>
              </tr>";
    
        echo "<tr>
                <td><strong>Job Description:</strong></td>
                <td colspan='3'>" . htmlspecialchars($idp_details['jobdescription']) . "</td>
              </tr>";
    
        echo "<tr>
                <td><strong>Date Hired:</strong></td>
                <td>" . date('F d, Y', strtotime($idp_details['datehired'])) . "</td>
                <td><strong>Months in Position:</strong></td>
                <td>" . htmlspecialchars($idp_details['monthsintheposition']) . "</td>
              </tr>";
    
        echo "<tr>
                <td><strong>Years in IT:</strong></td>
                <td colspan='3'>" . htmlspecialchars($idp_details['yearsiniit']) . "</td>
              </tr>";
    
        echo "</table>";
        echo "<br>";

        echo "<p><strong>Objective:</strong> " . htmlspecialchars($idp_details['objectives']) . "</p>";
        echo "<p><strong>Other Objective:</strong> " . htmlspecialchars($idp_details['other_objective']) . "</p>";
 
        echo "<div style='text-align: center; font-weight: bold; font-size: 18px; margin-top: 20px;'>
        COMPETENCY ASSESSMENT AND DEVELOPMENT PLAN
      </div>";
      echo "<br>";
        if ($competencies) {
            echo "<table border='1' cellpadding='5' cellspacing='0' width='100%' style='border-collapse: collapse; border: 1px solid black;'>";
            echo "<tr style='border: 1px solid black;'>
                    <th style='border: 1px solid black;'>Priority No</th>
                    <th style='border: 1px solid black;'>Competency Name</th>
                    <th style='border: 1px solid black;'>Workplace Learning</th>
                    <th style='border: 1px solid black;'>Social Learning</th>
                    <th style='border: 1px solid black;'>Structured Learning</th>
                    <th style='border: 1px solid black;'>Resources Needed</th>
                    <th style='border: 1px solid black;'>From Date</th>
                    <th style='border: 1px solid black;'>To Date</th>
                    <th style='border: 1px solid black;'>Estimated Budget</th>
                </tr>";
            foreach ($competencies as $competency) {
                echo "<tr style='border: 1px solid black;'>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['priority_no']) . "</td>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['competencyname']) . "</td>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['workplace_learning']) . "</td>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['social_learning']) . "</td>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['structured_learning']) . "</td>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['resources_needed']) . "</td>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['fromdate']) . "</td>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['todate']) . "</td>
                        <td style='border: 1px solid black;'>" . htmlspecialchars($competency['estimated_budget']) . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No competencies found.</p>";
        }
    } else {
        echo "<p>IDP not found.</p>";
    }
}
?>

<html>
<p class="declaration">
    <br> <br><br>I DECLARE UNDER THE OATH THAT I HAVE PERSONALLY ACCOMPLISHED THIS DOCUMENT AND THAT I COMMIT MYSELF TO ITS CONTENTS.
</p>

<p style="display: flex; justify-content: space-between; text-align: center; margin-top: 20px;">
    <span>
    Approved by: __________________<br>
        Supervisor/Cost Center Head
    </span>
    <span>
        Approved by: __________________<br>
        Vice Chancellor/Agency Head
    </span>
    <span>
        Approved by: __________________<br>
        Human Resource Management Office
    </span>
</p>
</html>
<style>
    
    .declaration {
        font-weight: bold; 
        text-align: center; 
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        padding: 8px;
        text-align: left;
    }
    th {
        font-weight: bold;
        text-align: center;
    }
    /* Modal scrollable styles */
    .modal-content {
        max-height: 100vh; /* 80% of viewport height */
        overflow-y: auto; /* Enable vertical scrolling */
        padding: 20px;
    }
    .modal-content::-webkit-scrollbar {
        width: 8px;
    }
    .modal-content::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .modal-content::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    
    .modal-content::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
