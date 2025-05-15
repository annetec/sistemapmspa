<?php  
$sName = "localhost";
$uName = "alice";
$pass  = "cpd@sorento";
$db_name = "task_management_db"; // Nome correto do banco

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
