<?php

CONST ROOT_DIR = "D:/Test/";
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
<?=ROOT_DIR;?>
    </div>
    <div id="content">
        <table id="file-list-table">
            <?php

            dirList(ROOT_DIR);

            ?>
        </table>
    </div>
    <div id="footer">
тут копирайт
    </div>
</div>

</body>
</html>