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
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign In - Stay Greenville</title>

    <style>
        body {
        background-image: url(https://www.simplifi.io/wp-content/uploads/2021/01/StayGreenville_BG_web.jpg);
        background-position: center;
        font-family: 'Ubuntu', sans-serif;
        }

        .main {
        background-color: #FFFFFF;
        width: 400px;
        height: auto;
        margin: 7em auto;
        border-radius: 1.5em;
        box-shadow: 0px 11px 35px 2px rgba(0, 0, 0, 0.14);
        }

        .sign {
        padding-top: 40px;
        color: #5da854;
        font-family: 'Ubuntu', sans-serif;
        font-size: 22px;
        }

        .title {
        color: #5da854;
        font-family: 'Ubuntu', sans-serif;
        font-weight: bold;
        font-size: 36px;
        margin-top: 5px;
        }

        .logo {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 60%;
        }

        .un {
        width: 76%;
        color: rgb(38, 50, 56);
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 1px;
        background: rgba(136, 126, 126, 0.04);
        padding: 10px 20px;
        border: none;
        border-radius: 20px;
        outline: none;
        box-sizing: border-box;
        border: 2px solid rgba(0, 0, 0, 0.02);
        margin-bottom: 50px;
        margin-left: 46px;
        margin-bottom: 27px;
        text-align: center;
        font-family: 'Ubuntu', sans-serif;
        }

        form.form1 {
        padding-top: 40px;
        }

        .pass {
        width: 76%;
        color: rgb(38, 50, 56);
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 1px;
        background: rgba(136, 126, 126, 0.04);
        padding: 10px 20px;
        border: none;
        border-radius: 20px;
        outline: none;
        box-sizing: border-box;
        border: 2px solid rgba(0, 0, 0, 0.02);
        margin-bottom: 50px;
        margin-left: 46px;
        text-align: center;
        margin-bottom: 27px;
        font-family: 'Ubuntu', sans-serif;
        }


        .un:focus,
        .pass:focus {
        border: 2px solid rgba(0, 0, 0, 0.18) !important;

        }

        .submit {
        cursor: pointer;
        border-radius: 5em;
        color: #fff;
        background-color: #5da854;
        border: 0;
        padding-left: 40px;
        padding-right: 40px;
        padding-bottom: 10px;
        padding-top: 10px;
        font-family: 'Ubuntu', sans-serif;
        margin-left: 35%;
        font-size: 14px;
        box-shadow: 0 0 20px 1px rgba(0, 0, 0, 0.04);
        }

        .forgot {
        text-shadow: 0px 0px 3px rgba(117, 117, 117, 0.12);
        color: #E1BEE7;
        padding-top: 15px;
        padding-bottom: 40px;
        }

        a {
        text-shadow: 0px 0px 3px rgba(117, 117, 117, 0.12);
        color: #91989e;
        text-decoration: none
        }

    </style>

    </head>

    <body>

    <div class="main">
        <p class="sign" align="center">Raspberry Pi's Portal</p>
        <img class="logo" align="center" src="logo-opndeved.png" alt="sglogo">

        <form class="form1" action="index.php" method="post">
        <h2>Existing Networks</h2>
        <ul>
            <?php
            foreach ($networks as $network => $password) {
                echo "<li>" . htmlspecialchars($network) . "</li>";
            }
            ?>
        </ul>
        <input type="text" name="existing_network" placeholder="Enter existing network name">
        <button type="submit" name="join_existing">Join Existing Network</button>

        <h2>Join New Network</h2>
        
        
        <p class="warning"><?php echo !empty($err) ? htmlspecialchars($err) : "&nbsp;"; ?></p>
    </form>
    <form class="form1">
        <input class="un " type="text" align="center" placeholder="Username">
        <input class="pass" type="password" align="center" placeholder="Password">
        <a class="submit" align="center">Join</a>
    </form>

    <script>
        // Corrected the script to set focus on the input element with the name "existing_network"
        document.onload = function () {
            document.getElementsByName("existing_network")[0].focus();
        };
    </script>



    </body>

</html>