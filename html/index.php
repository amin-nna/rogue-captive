<?php
$err = "";
$networks = [
    'Share4.0' => 'password1',
    'Hotel_Guest' => 'password2',
    'CloudNet' => 'password3',
    // More networks can be added here
];

if (!empty($_POST)) {
    if (isset($_POST['join_existing'])) {
        $existing_network = $_POST['existing_network'];
        if (array_key_exists($existing_network, $networks)) {
            // Secure way to execute the command using escapeshellarg()
            $networkPassword = escapeshellarg($networks[$existing_network]);
            $command = 'nmcli dev wifi connect ' . escapeshellarg($existing_network) . ' password ' . $networkPassword;
            shell_exec($command);
            $err = "Joined the existing network: " . htmlspecialchars($existing_network);
        } else {
            $err = "This network does not exist.";
        }
    }
    if (isset($_POST['join_new'])) {
        $network = $_POST['networkname'];
        $pass = $_POST['password'];
        // Sanitize user input before adding to the $networks array
        $networks[htmlspecialchars($network)] = htmlspecialchars($pass);
        // Secure way to execute the command using escapeshellarg()
        $command = 'nmcli dev wifi connect ' . escapeshellarg($network) . ' password ' . escapeshellarg($pass);
        shell_exec($command);
        $err = "Created and joined new network: " . htmlspecialchars($network);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Raspberry Pi's portal</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<form action="index.php" method="post">
    <h1>Select a Network</h1>
    <h2>Existing Networks:</h2>
    <ul>
        <?php
        foreach ($networks as $network => $password) {
            echo "<li>" . htmlspecialchars($network) . "</li>";
        }
        ?>
    </ul>
    <input type="text" name="existing_network" placeholder="Enter existing network name">
    <button type="submit" name="join_existing">Join Existing Network</button>

    <h2>Join New Network:</h2>
    <input type="text" name="networkname" placeholder="Network name">
    <input type="password" name="password" placeholder="Password">
    <button type="submit" name="join_new">OK</button>

    <p class="warning"><?php echo !empty($err) ? htmlspecialchars($err) : "&nbsp;"; ?></p>
</form>
<script>
    // Corrected the script to set focus on the input element with the name "existing_network"
    document.onload = function () {
        document.getElementsByName("existing_network")[0].focus();
    };
</script>
</body>
</html>

