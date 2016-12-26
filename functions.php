<?php

function dirList($dir){

    // Фильтрация
    $fld = strip_tags($_GET['fld']);
    $fld = htmlspecialchars($fld);
    $fld = rtrim($fld, ".");
    $fld = rtrim($fld, "./");
    $fld = urlencode($fld);

    // Принимает GET запрос по ссылке и переходим в папку
    if (isset($fld) && is_dir($dir.$fld)){
        $dir = $dir . $fld;
        chdir($dir);
    }
    //
    $data = opendir($dir);

    // Открываем и считываем директорию
    while($file = readdir($data)){
        if ($file != "." && $file != "..") {
            $files[] = $file;
        }
    }
    closedir($data);

    // Проверка на пустой массив files (если папка пустая)
    if (empty($files)){
        echo "<tr>";
        echo "<td><a href='index.php?fld='>Вернуться</a></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>Пустая папка...</td>";
        echo "</tr>";
        return;
    }else{
        echo "<tr>";
        echo "<td><a href='index.php?fld='>Вернуться</a></td>";
        echo "</tr>";
    }

    // Сортируем полученный массив по алфавиту
    asort($files);

    foreach ($files as $items){
        // Определяем расширение файла
        $extension = pathinfo($items);

        // Если расширение есть (не папка)
        if(isset($extension['extension'])){
            switch ($extension['extension']){
                case "odt":
                    $ext = '<img src="images/office.png" alt="pic" class="icon">';
                    break;
                case "zip";
                case "rar":
                    $ext = '<img src="images/zip.png" alt="pic" class="icon">';
                    break;
                default:
                    $ext = '<img src="images/folder.png" alt="pic" class="icon">';
            }
        }else{
            $items = '<a href="index.php?fld='.$items.'">'.$items.'</a>';
            $ext = '<img src="images/folder.png" alt="pic" class="icon">';
        }

        echo "<tr>";
        echo "<td>" .$ext. " " .$items. "</td>";
        echo "</tr>";
    }
}

?>