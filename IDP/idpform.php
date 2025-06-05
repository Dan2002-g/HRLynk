<?php
include('idpfunction.php');

// Fetch competencies excluding those already approved in the Training form
$sql = "
    SELECT competency_id, competencyname 
    FROM competency 
    WHERE competency_id NOT IN (
        SELECT competency_id 
        FROM idp_competencies 
        WHERE idp_id IN (
            SELECT id 
            FROM idp 
            WHERE userID = :userID AND status = 'Approved'
        )
    )
    AND competency_id NOT IN (
        SELECT idpcompetency 
        FROM training 
        WHERE userID = :userID AND status = 'Approved'
    )
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userID', $_SESSION['user'], PDO::PARAM_INT);
$stmt->execute();
$competencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Individual Development Plan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <script src='https://unpkg.com/phosphor-icons'></script>
    <!-- Include jQuery and Select2 CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <!-- Preload competency options as a JavaScript array -->
    <script>
    // Pass the PHP competencies array to JavaScript
    const competencyOptions = <?php echo json_encode($competencies); ?>;
    let competencyCount = 1; // Initialize a counter for competencies

    document.addEventListener('DOMContentLoaded', function () {
        // Function to attach auto-expand behavior to textareas
        function attachAutoExpandListeners() {
            document.querySelectorAll('.auto-expand').forEach(textarea => {
                textarea.removeEventListener('input', autoExpand); // Remove existing listener to prevent duplicates
                textarea.addEventListener('input', autoExpand);   // Attach new listener
            });
        }

        // Function to auto-expand textareas
        function autoExpand() {
            this.style.height = 'auto'; // Reset height to recompute
            this.style.height = this.scrollHeight + 'px'; // Adjust height to fit content
        }

        // Bind listeners to existing textareas
        attachAutoExpandListeners();

        // Add a new competency row
        window.addCompetency = function () { // UPDATED: Changed to `window.addCompetency` for global access
            competencyCount++; // Increment counter for new competency
            const tbody = document.getElementById('competency-table-body'); // Get the table body

            // Create the competency options HTML from the JavaScript array
            let optionsHTML = '<option value="">Select Competency</option>';
            competencyOptions.forEach(option => {
                optionsHTML += `<option value="${option.competency_id}">${option.competencyname}</option>`;
            });

            // Create a new row with input fields and a delete button
            const newRow = `
                <tr id="competency-row-${competencyCount}">
                    <td>
                        <select name="competency_id_${competencyCount}" id="competency_id_${competencyCount}" class="formbold-form-input">
                            ${optionsHTML}
                        </select>
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <input type="number" name="priority_no_${competencyCount}" class="formbold-form-input" style="width: 80%;">
                    </td>
                    <td><textarea class="formbold-form-input auto-expand" required name="workplace_learning_${competencyCount}"></textarea></td>
                    <td><textarea class="formbold-form-input auto-expand" required name="social_learning_${competencyCount}"></textarea></td>
                    <td><textarea class="formbold-form-input auto-expand" required name="structured_learning_${competencyCount}"></textarea></td>
                    <td><textarea class="formbold-form-input auto-expand" required name="resources_needed_${competencyCount}"></textarea></td>
                    <td><textarea class="formbold-form-input auto-expand" required name="accomplishment_indicator_${competencyCount}"></textarea></td>
                    <td><input type="date" name="fromdate_${competencyCount}" class="formbold-form-input"></td>
                    <td><input type="date" name="todate_${competencyCount}" class="formbold-form-input"></td>
                    <td><textarea class="formbold-form-input auto-expand" required name="estimated_budget_${competencyCount}"></textarea></td>
                    <td><button type="button" class="delete-btn" onclick="deleteCompetency(${competencyCount})">Delete Competency</button></td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', newRow); // Append the new row

            attachAutoExpandListeners(); // UPDATED: Reapply auto-expand behavior to the new row
        };

        // Delete a competency row
        window.deleteCompetency = function (id) { // UPDATED: Changed to `window.deleteCompetency` for global access
            const row = document.getElementById(`competency-row-${id}`);
            if (row) {
                row.remove(); // Remove the row from the table
            }
        };
    });
</script>
<style>


        .guidelines {
            background: #fff;
            padding: 20px;
            border-left: 5px solid #007bff;
            border-radius: 5px;
            margin-top: 20px;
        }

        .guidelines h2 {
            font-weight: bold;
            color:maroon;
            text-align: center;
            font-size: 22px;
        }

        .guidelines p {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            margin: 10px 0;
        }

        .guidelines p b {
            color: #333;
            font-weight: bold;
        }

        .tooltip {
    position: relative;
    display: inline-block;
    border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: max-content;
    max-width: 800px; /* Allow unlimited expansion */
    background-color: #555;
    color: #fff;
    text-align: left; /* Align text naturally */
    border-radius: 6px;
    padding: 5px 10px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%); /* Center horizontally */
    opacity: 0;
    transition: opacity 0.3s;
    white-space: normal; /* Allow text wrapping */
    word-wrap: break-word; /* Break long words if necessary */
}

.tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}
    .guidelines {
        background: #fff;
        padding: 20px;
        border-left: 5px solid #007bff;
        border-radius: 5px;
        margin-top: 20px;
        transition: max-height 0.3s ease, padding 0.3s ease;
        overflow: hidden;
    }

    .guidelines.collapsed {
        max-height: 0;
        padding: 0 20px;
    }

    .guidelines-toggle {
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        color: maroon;
        margin-bottom: 10px;
    }

    .guidelines-toggle i {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .guidelines.collapsed + .guidelines-toggle i {
        transform: rotate(180deg);
    }
</style>
<style>
    .guidelines {
        background: #fff;
        padding: 0 20px; /* Adjust padding for collapsed state */
        border-left: 5px solid #007bff;
        border-radius: 5px;
        margin-top: 20px;
        max-height: 0; /* Start collapsed */
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }

    .guidelines.expanded {
        max-height: 1000px; /* Large enough to fit content */
        padding: 20px;
    }

    .guidelines-toggle {
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        color: maroon;
        margin-bottom: 10px;
    }

    .guidelines-toggle i {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .guidelines-toggle i.rotated {
        transform: rotate(180deg); /* Rotate the arrow */
    }
</style>
<style>
    .guidelines-title {
        font-weight: bold;
        font-size: 20px; /* Slightly larger font size */
        color: maroon; /* Set font color to maroon */
        text-align: center;
        margin-bottom: 10px;
    }
</style>
<style>
    .horizontal-timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px 0;
        position: relative;
    }

    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex-shrink: 0;
        width: 150px;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        background-color: maroon;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 10px;
    }

    .timeline-content {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        color: #333;
        font-size: 14px;
        line-height: 1.6;
    }

    .timeline-line {
        flex-grow: 1;
        height: 2px;
        background-color: #ddd;
        margin: 0 10px;
    }

    .guidelines {
        overflow-x: auto; /* Allow horizontal scrolling for smaller screens */
    }

    @media (max-width: 768px) {
        .horizontal-timeline {
            flex-direction: column;
            align-items: flex-start;
        }

        .timeline-line {
            width: 2px;
            height: 20px;
            margin: 10px 0;
        }
    }
</style>
<style>
    .rejection-guidelines {
        margin-top: 20px;
        padding: 10px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        color: #333;
    }

    .rejection-guidelines h3 {
        font-size: 18px;
        font-weight: bold;
        color: maroon;
        margin-bottom: 10px;
    }

    .rejection-guidelines ul {
        list-style-type: disc;
        margin-left: 20px;
        padding-left: 0;
    }

    .rejection-guidelines ul li {
        font-size: 16px;
        line-height: 1.6;
        color: #333;
    }
</style>
<style>
    .vertical-timeline {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        margin: 20px 0;
    }

    .timeline-step {
        display: flex;
        flex-direction: row;
        align-items: center;
        text-align: left;
        margin-bottom: 20px;
        width: 100%;
        position: relative;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        background-color: maroon;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        font-size: 18px;
        margin-right: 10px;
        z-index: 1;
    }

    .timeline-line {
        width: 2px;
        background-color: #ddd;
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        z-index: 0;
    }

    .timeline-step:not(:last-child)::after {
        content: '';
        position: absolute;
        width: 2px;
        height: calc(100% + 20px); /* Extend the line to connect steps */
        background-color: #ddd;
        left: 20px;
        top: 40px; /* Align with the bottom of the icon */
        z-index: 0;
    }

    .timeline-content {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        color: #333;
        font-size: 14px;
        line-height: 1.6;
        flex-grow: 1;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const guidelines = document.querySelector('.guidelines');
        const toggleButton = document.querySelector('.guidelines-toggle');

        toggleButton.addEventListener('click', function () {
            guidelines.classList.toggle('expanded');
            toggleButton.querySelector('i').classList.toggle('rotated'); // Rotate the arrow
        });
    });
</script>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="../Homepage/index.html" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
            <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
        </a>
        <a href="../Homepage/logout.php" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
        <h4>Employee Portal</h4>
    </div>
    <div class="header-navigation">
        <nav class="sidebar-navigation">
            <a href="../Homepage/index.html" class="sidebar-link active">Home</a>
            <a href="../IDP/idpdashboard.php" class="sidebar-link">IDP</a>
            <a href="../Training Request/trainingform.php" class="sidebar-link">Request Training</a>
            <a href="../Training Request/trainingdashboard.php" class="sidebar-link">Training History</a>
        </nav>
        <nav class="sidebar-navigation side-nav">
        <a href="../Profile/profiledisplay.php" class="icon-button" aria-label="Profile"><i class="bi bi-person-circle"></i> Profile</a>
        <a href="../Homepage/logout.php" class="icon-button" aria-label="Logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </div>
</div>

<main class="main">
          <div class="responsive-wrapper">
              <div class="main-header">
                  <h1>Individual Development Plan</h1>
              </div>
                  <!-- Guidelines Section -->
    <div class="guidelines-toggle">
        <span class="guidelines-title">Guidelines for Completing the Annual Individual Development Plan (AIDP)</span>
        <i class="bi bi-chevron-down"></i>
    </div>
    <div class="guidelines">
        <div class="vertical-timeline">
            <div class="timeline-line"></div>
            <div class="timeline-step">
                <div class="timeline-icon">1</div>
                <div class="timeline-content">
                    <p><b>Step 1:</b> Employee, together with the supervisor/head, assesses their understanding of duties and responsibilities (i.e., core functions, support functions, and other assignments in relation to the overall unit's functions).</p>
                </div>
            </div>
            <div class="timeline-step">
                <div class="timeline-icon">2</div>
                <div class="timeline-content">
                    <p><b>Step 2:</b> List down required competencies and identify areas needing improvement. Determine ways to bridge the competency gap (e.g., coaching, orientation programs, formal training).</p>
                </div>
            </div>
            <div class="timeline-step">
                <div class="timeline-icon">3</div>
                <div class="timeline-content">
                    <p><b>Step 3:</b> Schedule a meeting with your supervisor to discuss, review, and finalize the proposed AIDP. The supervisor can refer to performance ratings and observations.</p>
                </div>
            </div>
            <div class="timeline-step">
                <div class="timeline-icon">4</div>
                <div class="timeline-content">
                    <p><b>Step 4:</b> Submit your IDP and wait for the approval from your supervisor, responsibility center head, and cluster head. Submit your competency and wait for the approval of the supervisor and HR.</p>
                </div>
            </div>
            <div class="timeline-step">
                <div class="timeline-icon">5</div>
                <div class="timeline-content">
                    <p><b>Step 5:</b> Your AIDP serves as a reference for evaluating and approving requests for training, conventions, and learning activities.</p>
                </div>
            </div>
        </div>
        <div class="rejection-guidelines">
            <h3>What happens if your forms are rejected?</h3>
            <ul>
                <li>Go to IDP and fill out another IDP form.</li>
            </ul>
        </div>
    </div>
    </div>
              <div class="horizontal-tabs">
                  <a class="horizontal" href="idpdashboard.php" style="color: black;">Dashboard</a>
              </div>
  
              <div class="formbold-form-wrapper">
                  <form method="POST" action="idpfunction.php">
                      <div class="formbold-mb-3 objective-section">
                          <h2>Objective: (Check the appropriate box)</h2>
                          <label><input type="checkbox" name="objectives[]" value="To meet competencies of current position/designation"> To meet competencies of current position/designation.</label><br>
                          <label><input type="checkbox" name="objectives[]" value="To increase competencies of current position/designation"> To increase competencies of current position/designation.</label><br>
                          <label><input type="checkbox" name="objectives[]" value="To acquire new competencies"> To acquire new competencies required in current position/designation.</label><br>
                          <label>Others, please specify: <input type="text" name="other_objective" id="other_objective" class="formbold-form-input short-input" placeholder="Specify"></label>
                      </div>
                      
                      <div id="competency-forms">
                          <div class="form-container">
                              <h2>Competency Development</h2>
                              <div class="form-row">
                                  <div class="form-group">
                                      <label for="competency_id">Competency (KSA) to Develop:</label>
                                      <select name="competency_id[]" required>
    <option value="">Select Competency</option>
    <?php foreach ($competencies as $competency): ?>
        <option value="<?= $competency['competency_id']; ?>"><?= $competency['competencyname']; ?></option>
    <?php endforeach; ?>
</select>
                                  </div>
                                  <div class="form-group">
                                      <label for="priority_no">Priority No.:</label>
                                      <input type="number" id="prionum" required name="priority_no[]">
                                  </div>
                              </div>
                              <div class="form-row">
                                  <div class="form-group">
                                      <label for="workplace_learning">Workplace Learning:<span class="tooltip">ⓘ<span class="tooltiptext">Refers to learning from hands-on experiences or learning in the workplace such as (shadowing, job enlargement, cross-training and posting, job rotation, special job assignments, benchmarking, exposure/field visits, on-the-job training, and work improvement teams)</span></span></label>
                                      <textarea required name="workplace_learning[]"></textarea>
                                  </div>
                                  <div class="form-group">
                                      <label for="social_learning">Social Learning:<span class="tooltip">ⓘ<span class="tooltiptext">this means interation with people  such as (coaching, mentoring, behavior modelling, feedback in performance discussions, feedback in recognition of good performance, conversations either interpersonal and inter-departmental communication, and communities of practice).</span></span></label>
                                      <textarea required name="social_learning[]"></textarea>
                                  </div>
                              </div>
                              <div class="form-row">
                                  <div class="form-group">
                                      <label for="structured_learning">Structured Learning:<span class="tooltip">ⓘ<span class="tooltiptext">refers to training and education such as (training programs, face-to-face and web-based interaction, blended learning, education programs and/or advanced studies, self study programs and/or e-learning, professional conferences, and reading self-selected books).</span></span></label>
                                      <textarea required name="structured_learning[]"></textarea>
                                  </div>
                                  <div class="form-group">
                                      <label for="resources_needed">Support/Resources Needed:</label>
                                      <textarea required name="resources_needed[]"></textarea>
                                  </div>
                              </div>
                              <div class="form-row">
                                  <div class="form-group">
                                      <label for="accomplishment_indicator">Accomplishment Indicators:</label>
                                      <textarea required name="accomplishment_indicator[]"></textarea>
                                  </div>
                                  <div class="form-group">
                                      <label for="fromdate">From Date:</label>
                                      <select name="fromdate[]" required>
                                          <option value="Q1">Q1</option>
                                          <option value="Q2">Q2</option>
                                          <option value="Q3">Q3</option>
                                          <option value="Q4">Q4</option>
                                      </select>
                                  </div>
                                  <div class="form-group">
                                      <label for="todate">To Date:</label>
                                      <select name="todate[]" required>
                                          <option value="Q1">Q1</option>
                                          <option value="Q2">Q2</option>
                                          <option value="Q3">Q3</option>
                                          <option value="Q4">Q4</option>
                                      </select>
                                  </div>
                                  <div class="form-group">
                                      <label for="estimated_budget">Estimated Budget:</label>
                                      <input type="text" required name="estimated_budget[]">
                                  </div>
                              </div>
                              <div class="button-container">
                                  <button type="button" class="delete-btn" onclick="deleteCompetencyForm(this)">Delete</button>
                              </div>
                          </div>
                      </div>
                      <div class="button-container">
                          <button type="button" class="add-btn" onclick="addCompetencyForm()">Add Another Competency</button>
                          <button type="submit" class="submit-btn">Submit</button>
                      </div>
                  </form>
              </div>
          </div>
      </main>
<script>
    function addCompetencyForm() {
        const formContainer = document.getElementById('competency-forms');
        const newForm = formContainer.firstElementChild.cloneNode(true);

        // Clear all input, textarea, and select values in the cloned form
        newForm.querySelectorAll('input, textarea, select').forEach(element => {
            if (element.tagName === 'SELECT') {
                element.selectedIndex = 0; // Reset select to the first option
            } else {
                element.value = ''; // Clear input and textarea values
            }
        });

        formContainer.appendChild(newForm);
    }
</script>
<script>
        $(document).on("click", ".delete-btn", function() {
            var formId = $(this).data("id");
            if (confirm("Are you sure you want to delete this form?")) {
                $.ajax({
                    url: "delete_form.php",
                    type: "POST",
                    data: { id: formId },
                    success: function(response) {
                        if (response === "success") {
                            $("#form-" + formId).remove();
                        } else {
                            alert("Error deleting form.");
                        }
                    }
                });
            }
        });
    </script>
<style>
    .delete-btn {
    display: inline-block; 
    padding: 10px 20px; 
    color: white;
    background-color: maroon; 
    border-radius: 4px; 
    border: none; 
    cursor: pointer; 
    text-align: center; 
    white-space: nowrap; 
    min-width: auto; 
    width: auto;

    }
    .delete-btn:hover {
        background-color: gold;
    }
    table {
    width: 100%;
    margin: auto;
    border-collapse: collapse;
    margin-top: 15px;
    margin-bottom: 15px;
    table-layout: auto;
}
h4 {
    font-size: 20px;
    font-weight: 500;
    color: gold;
    text-align: center;
    margin-top: 0px;
    shadow: 0.5px 0.5px maroon;
    text-shadow: 0.5px 0.5px maroon;
}
table th,
table td {
  border: 1px solid #ddd;
  padding: 10px;
  word-wrap: break-word;
  text-align: left;
  vertical-align: top;

}

table th {
    background-color: maroon;
    color: white;
    font-size: 14px;
}
.form-group {
    width: 100px;

}
table td {
    overflow-wrap: anywhere; /* Breaks long words to fit within the cell */
    word-break: break-word; /* Ensures text wraps */
}
.form-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #fff;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .form-group {
            flex: 1;
            min-width: 150px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            color:black;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 80%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group textarea {
            resize: none;
            height: 100px; /* Fixed height */
        }
        .submit-btn, .add-btn {
            background-color: maroon;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .form-group input {
            width:50%
        }
thead th {
  color: white;
}

.auto-expand {
    width: 100%; /* Full width */
    min-height: 40px; /* Minimum height to look like a single-line box initially */
    line-height: 1.5; /* Line height for readability */
    overflow-y: hidden; /* Prevent vertical scrollbar initially */
    display: block; /* Ensures it behaves as a block-level element */
    padding: 8px; /* Add padding for a better look */
    box-sizing: border-box; /* Include padding in width calculations */
    resize: none; /* Prevent manual resizing */
    white-space: pre-wrap; /* Preserve whitespace and wrap text */
    word-wrap: break-word; /* Allow words to break and wrap in narrow spaces */
}

select.formbold-form-input {
    width: auto;
    min-width: 150px; /* Minimum width for select input */
    max-width: 100%; /* Ensure it does not exceed container width */
    padding: 8px;
    font-size: 14px;
}

option {
    white-space: nowrap; /* Keep options on a single line */
    overflow: hidden; /* Hide overflowing text in options */
}

.objective-section {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    gap: 0px;
}
h2 {
    color: black;
}
.objective-label {
    font-size: 16px;
    font-weight: bold;
}

input[type="checkbox"] {
    margin-right: 10px;
}

button.formbold-btn, input.formbold-btn {
    background-color: maroon;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease, transform 0.2s;
    display: inline-block;
}

button.formbold-btn:hover, input.formbold-btn:hover {
    background-color: darkred;
}

button.formbold-btn.add-btn {
    background-color: maroon;
}

button.formbold-btn.add-btn:hover {
    background-color: gold;
}

button.formbold-btn.submit-btn {
    background-color: maroon;
}

button.formbold-btn.submit-btn:hover {
    background-color: gold;
}

.button-container {
    display: flex;
    gap: 10px;
    margin-top: 10px !important;
    justify-content: flex-end; /* Align buttons to the right */
} 
/* Adjust button styling */
td button.delete-btn {
    display: inline-block; /* Use inline-block to allow the button to adjust to content */
    padding: 5px 16px; /* Padding to ensure the button has some space around the text */
    background-color: maroon; /* Background color */
    color: white; /* Text color */
    border-radius: 4px; /* Rounded corners */
    border: none; /* No border */
    cursor: pointer; /* Pointer cursor to indicate interactivity */
    text-align: center; /* Ensure the text is centered */
    white-space: nowrap; /* Prevent the text from wrapping */
    min-width: auto; /* Allow the width to adjust automatically */
    width: auto; /* Let the width adjust based on the content */
}

/* Hover effect for delete button */
td button.delete-btn:hover {
    background-color: gold; /* Change background color on hover */
}
/* Responsive Design */
@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead tr {
        display: none;
    }

    tr {
        margin-bottom: 15px;
    }

    td {
        position: relative;
        padding-left: 50%;
    }

    td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: bold;
    }
    
}
</style>
<script>
    
    document.addEventListener('DOMContentLoaded', function () {
    // Function to attach auto-expand behavior to textareas
    function attachAutoExpandListeners() {
        document.querySelectorAll('.auto-expand').forEach(textarea => {
            textarea.removeEventListener('input', autoExpand); // Remove existing listener to prevent duplicates
            textarea.addEventListener('input', autoExpand);   // Attach new listener
        });
    }

    // Function to auto-expand textareas
    function autoExpand() {
        // Reset the textarea height to allow dynamic resizing
        this.style.height = 'auto'; // Reset height to recompute
        this.style.height = this.scrollHeight + 'px'; // Adjust height to fit content

        // Calculate the width based on content and add some padding for a better look
        this.style.width = 'auto'; // Reset height to recompute
        this.style.width = this.scrollWidth + 'px'; // Adjust width dynamically
    }

    // Bind listeners to existing textareas
    attachAutoExpandListeners();
});
</script>
</body>
</html>