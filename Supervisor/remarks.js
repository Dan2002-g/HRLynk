document.addEventListener('DOMContentLoaded', () => {
    // Add event listeners to dynamically created "Remarks" buttons
    document.querySelector('#hrdplan').addEventListener('click', (event) => {
        if (event.target && event.target.classList.contains('btn-outline-primary')) {
            const remarksModal = new bootstrap.Modal(document.getElementById('remarksModal'));
            const remarksInput = document.getElementById('remarksInput');
            const saveButton = document.getElementById('saveRemarksButton');

            // Get the competency ID and existing remarks from the button's dataset
            const competencyId = event.target.dataset.competencyId;
            const existingRemarks = event.target.dataset.remarks || '';

            // Populate the modal with existing remarks
            remarksInput.value = existingRemarks;

            // Show the modal
            remarksModal.show();

            // Save remarks on button click
            saveButton.onclick = () => {
                const remarks = remarksInput.value;

                // Save the remark via AJAX
                fetch('save_hrd_remark.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: competencyId, remarks })
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            // Update the button style to indicate success
                            event.target.classList.remove('btn-outline-primary');
                            event.target.classList.add('btn-success');
                            event.target.dataset.remarks = remarks; // Update the dataset with the new remarks
                            remarksModal.hide();
                        } else {
                            alert('Failed to save remark. Please try again.');
                        }
                    })
                    .catch(error => console.error('Error saving remark:', error));
            };
        }
    });
});