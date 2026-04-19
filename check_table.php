<?php
$con = mysqli_connect("127.0.0.1", "root", "", "web_hotel_mngt", 3307);
if (!$con) {
    die("Connection failed");
}

$result = mysqli_query($con, "DESCRIBE hotels_guests");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
mysqli_close($con);
?>
