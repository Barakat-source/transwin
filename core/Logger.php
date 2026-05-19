<?php
class Logger {
    public static function log($db, $action, $details = "") {
        if (!isset($_SESSION['user_id'])) return;
        
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $action, $details]);
    }
}
