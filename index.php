<?php

session_start();

$rootDir = getcwd();
include "functions.php";

?>

<html>
<head>
    <title>Explorer</title>
    <link rel="stylesheet" href="styles.css" type="text/css">
    <meta charset="utf-8">
</head>
<body>

<div id="container">
    <div id="header">
Logo
    </div>
    <div id="url">
<?=$rootDir;?>
    </div>
    <div id="content">
        <table id="file-list-table">
            <?php

            dirList($rootDir);

            ?>
        </table>
    </div>
    <div id="footer">
тут копирайт
    </div>
</div>

</body>
</html>