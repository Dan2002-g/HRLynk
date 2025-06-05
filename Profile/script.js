// Toggle sidebar visibility on page load
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.add("visible");
});

// Preview profile image on upload
function previewImage(event) {
    const profileImage = document.getElementById("profileImage");
    profileImage.src = URL.createObjectURL(event.target.files[0]);
}

// Add an event listener to trigger the file input when the profile picture is clicked
document.getElementById("profileImage").addEventListener("click", function() {
    document.getElementById("uploadProfilePicture").click();
});