<?php
include('connection.php');
header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("
        SELECT 
            t.trainingdate AS start,
            t.trainingdate_end AS end,
            t.trainingtitle AS title,
            t.venue,
            u.empname AS employee
        FROM training t
        JOIN users u ON t.userID = u.userID
        WHERE t.status = 'Approved'
    ");
    $stmt->execute();
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = array_map(function($training) {
        $start = new DateTime($training['start']);
        $end = new DateTime($training['end']);
        $end->modify('+1 day'); // required for full-day events to span correctly

        return [
            'title' => $training['title'],
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'), // return plain date (not ISO) for allDay
            'allDay' => true,
            'extendedProps' => [
                'employeeName' => $training['employee'],
                'venue' => $training['venue']
            ]
        ];
    }, $trainings);

    echo json_encode($events);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => 'An unexpected error occurred.']);
}
?>
