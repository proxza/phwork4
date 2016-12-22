<?php

CONST ROOT_DIR = "D:/Test/";

$files = scandir(ROOT_DIR);

for ($i = 1;$i <= count($files);$i++){
    echo $files[$i];
}

print_r($files);

?>

<html>
<head>
    <title>Explorer</title>
    <link rel="stylesheet" href="styles.css" type="text/css">
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
тут будет вывод всех файлов и работа с ними
    </div>
    <div id="footer">
тут копирайт
    </div>
</div>

</body>
</html>