<?php
include('connection.php');

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if ID exists in IDP table
    $stmt_test = $conn->prepare("SELECT * FROM idp WHERE id = :id");
    $stmt_test->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_test->execute();

    // Fetch IDP details along with user and user_details information
    $stmt = $conn->prepare("
        SELECT idp.id, idp.objectives, idp.other_objective, status, users.empname,
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

    // Fetch competencies linked to this IDP
    $stmt_competencies = $conn->prepare("
        SELECT ic.priority_no, c.competencyname, ic.workplace_learning, 
               ic.social_learning, ic.structured_learning, ic.resources_needed, 
               ic.accomplishment_indicator, ic.fromdate, ic.todate, ic.estimated_budget
        FROM idp_competencies ic
        JOIN competency c ON ic.competency_id = c.competency_id
        WHERE ic.idp_id = :id
    ");
    $stmt_competencies->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_competencies->execute();
    $competencies = $stmt_competencies->fetchAll(PDO::FETCH_ASSOC);

    // Output the data
    if ($idp_details) {
        echo "<table class='no-border-table' cellspacing='5' cellpadding='5' width='100%'>";
        
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
                <td>" . htmlspecialchars(date('F - d - Y', strtotime($idp_details['datehired']))) . "</td>
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
            echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>";
            echo "<tr>
                    <th>Priority No.</th>
                    <th>Competency</th>
                    <th>Workplace Learning</th>
                    <th>Social Learning</th>
                    <th>Structured Learning</th>
                    <th>Resources Needed</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Estimated Budget</th>
                </tr>";
            foreach ($competencies as $competency) {
                echo "<tr>
                        <td>" . htmlspecialchars($competency['priority_no']) . "</td>
                        <td>" . htmlspecialchars($competency['competencyname']) . "</td>
                        <td>" . htmlspecialchars($competency['workplace_learning']) . "</td>
                        <td>" . htmlspecialchars($competency['social_learning']) . "</td>
                        <td>" . htmlspecialchars($competency['structured_learning']) . "</td>
                        <td>" . htmlspecialchars($competency['resources_needed']) . "</td>
                        <td>" . htmlspecialchars($competency['fromdate']) . "</td>
                        <td>" . htmlspecialchars($competency['todate']) . "</td>
                        <td>" . htmlspecialchars($competency['estimated_budget']) . "</td>
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

<?php
// Check if the IDP status is "Approved" or "Rejected"
$isDisabled = ($idp_details['status'] === 'Approved' || $idp_details['status'] === 'Rejected');
?>

<form action="update.php" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <button type="submit" name="approve" class="btn btn-success" 
            <?php echo $isDisabled ? 'disabled' : ''; ?> 
            id="approve-btn" 
            style="<?php echo $idp_details['status'] === 'Rejected' ? 'display: none;' : ''; ?>">
        Approve
    </button>
    <button type="submit" name="reject" class="btn btn-danger" 
            <?php echo $isDisabled ? 'disabled' : ''; ?> 
            id="reject-btn" 
            style="<?php echo $idp_details['status'] === 'Approved' ? 'display: none;' : ''; ?>">
        Reject
    </button>
</form>

<script>
document.querySelector('button[name="approve"]').addEventListener('click', function () {
    document.getElementById('reject-btn').style.display = 'none';
});

document.querySelector('button[name="reject"]').addEventListener('click', function () {
    document.getElementById('approve-btn').style.display = 'none';
});
</script>

</html>
<script>
// Function to send AJAX request to update the status
function updateStatus(id, status) {
    if (!confirm(`Are you sure you want to ${status.toLowerCase()} this IDP?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', status);

    console.log("Sending request...", { id, status });

    fetch('update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response received:", data); // Debugging response
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status.');
    });
}


// Attach event listeners to buttons
console.log("ID Passed: ", <?= json_encode($id) ?>);

document.querySelector('.action-btn.approve').addEventListener('click', () => {
    console.log("Approve button clicked.");
    updateStatus(<?= json_encode($id) ?>, 'Approved');
});
document.querySelector('.action-btn.reject').addEventListener('click', () => {
    console.log("Reject button clicked.");
    updateStatus(<?= json_encode($id) ?>, 'Rejected');
});


</script>
<style>
.declaration {
    font-weight: bold; 
    text-align: left; 
}
/* General table styles */
table {
    border-collapse: collapse;
    width: 100%;
    border: 1px solid black;
}
td, th {
    padding: 8px;
    text-align: left;
    border: 1px solid black;
}
th {
    font-weight: bold;
    text-align: left;
}

/* Specific styles for the no-border table */
.no-border-table {
    border: none;
}
.no-border-table td, .no-border-table th {
    border: none;
}
</style>