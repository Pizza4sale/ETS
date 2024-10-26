<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<div class="mobile-menu-overlay"></div>

<div class="main-container">
    <div class="pd-ltr-20">
        <div class="card-box pd-20 height-100-p mb-30">
            <h4 class="text-center">Calendar</h4>
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- FullCalendar JS initialization -->
<script>
$(document).ready(function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: 'events.php', // Your endpoint for fetching events
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Events data:', data); // Debugging
                    successCallback(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching events:', error); // Debugging
                    failureCallback(error);
                }
            });
        }
    });

    calendar.render();
});
</script>
