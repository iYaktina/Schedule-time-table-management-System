
<?php
include_once "include/dbh.inc.php";

$sql = "SELECT ID, Username FROM user";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['ID']}'>{$row['Username']}</option>";
}
?>