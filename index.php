<?php

session_start();

$rootDir = getcwd();
include "functions.php";


print_r($_SESSION['url']);

switch (true){
    case isset($_POST['create']):
        echo "CREATE NOT FOUND";
        break;
    case isset($_POST['delete']):
        deleteFiles($rootDir, $_POST['fls']);
        break;
    case isset($_POST['rename']);
        echo "RENAME";
        break;
    case isset($_POST['copy']);
        echo "COPY";
        break;
    case isset($_POST['sessionOff']);
        // Временное решение по очистке сессии
        session_destroy();
        unset($_SESSION['url']);
        break;
}
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
        <form action="index.php" method="post">
        <table id="file-list-table">
            <?php

            dirList($rootDir);

            ?>
            <tr>
                <td><br /><input type="submit" name="create" value="Создать папку"></td>
            </tr>
            <tr>
                <td><input type="submit" name="delete" value="Удалить"></td>
            </tr>
            <tr>
                <td><input type="submit" name="rename" value="Переименовать"></td>
            </tr>
            <tr>
                <td><input type="submit" name="copy" value="Копировать"></td>
            </tr>
            <tr>
                <td><input type="submit" name="sessionOff" value="KillSession"></td>
            </tr>
        </table>
        </form>
    </div>
    <div id="footer">
тут копирайт
    </div>
</div>

</body>
</html>