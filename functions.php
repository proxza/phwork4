<?php

function dirList($dir){
    $data = opendir($dir);

    // Открываем и считываем директорию
    while($file = readdir($data)){
        if ($file != "." && $file != ".."){
            $files[] = $file;
        }
    }
    closedir($data);

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
            $ext = '<img src="images/folder.png" alt="pic" class="icon">';
        }



        echo "<tr>";
        echo "<td>" .$ext. " " .$items. "</td>";
        echo "</tr>";
    }
}

?>