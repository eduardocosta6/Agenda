<?php
function log_action($action, $details = '') {
    global $conn;
    $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'System';
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $timestamp = date('Y-m-d H:i:s');
    
    // Create log entry with more details
    $log_data = [
        'timestamp' => $timestamp,
        'user_id' => $user,
        'action' => $action,
        'details' => $details,
        'ip_address' => $ip,
        'user_agent' => $user_agent,
        'session_id' => session_id()
    ];
    
    // Convert to JSON for storage
    $log_json = json_encode($log_data);
    
    // Store in database
    $sql = "INSERT INTO logs (action, user, details, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $action, $user, $details, $ip, $user_agent);
    
    // Create logs directory if it doesn't exist
    $logs_dir = __DIR__ . '/../logs';
    if (!file_exists($logs_dir)) {
        mkdir($logs_dir, 0777, true);
    }
    
    // Write to file for backup
    $log_file = $logs_dir . '/system_' . date('Y-m-d') . '.log';
    $log_entry = "[{$timestamp}] {$action} - User: {$user} - {$details} - IP: {$ip}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    return $stmt->execute();
}


