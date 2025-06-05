<?php
if (isset($_GET['remarks'])) {
    $remarks = htmlspecialchars($_GET['remarks']);
    echo "<div style='padding: 20px; font-size: 18px;'>$remarks</div>";
} else {
    echo "<div style='padding: 20px; font-size: 18px; color: red;'>No remarks provided.</div>";
}
?>

<style>
/* Styling specific to the Remarks modal */
.modal-remarks-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    visibility: hidden;
    opacity: 0;
    transition: visibility 0s, opacity 0.3s ease-in-out;
}

.modal-remarks-content {
    background: #fff;
    color: #000;
    padding: 20px;
    width: 400px;
    border-radius: 10px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-size: 20px;


}
.modal-remarks-header {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 10px;
    color: maroon;
}

.modal-remarks-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 30px;
    cursor: pointer;
    color: #333;
}

.modal-remarks-close:hover {
    color: red;
}

.modal-remarks-show {
    visibility: visible;
    opacity: 1;
}
</style>