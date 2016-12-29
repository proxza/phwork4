<?php

function dirList($dir){

    //unset($_SESSION['url']);
   // Принимает GET запрос по ссылке и переходим в папку + Фильтрация
    if (isset($_GET['fld'])){
        $fld = strip_tags($_GET['fld']);
        $fld = htmlspecialchars($fld);
        $fld = rtrim($fld, ".");
        $fld = rtrim($fld, "./");
        $fld = urlencode($fld);

        // Костяль в виде многоуровнего вхождения в папку и обратно
        if (empty($_SESSION['url'])){
            $_SESSION['url'] = $fld;
            $dir = $dir."/".$_SESSION['url'];
        }else{
            if ($fld == "back"){
                $back = substr($_SESSION['url'], 0, strrpos($_SESSION['url'], "/"));
                $_SESSION['url'] = $back;
                $dir = $dir."/".$_SESSION['url'];
            }else{
                $dir = $dir."/".$_SESSION['url']."/".$fld;
                $_SESSION['url'] .= "/".$fld;
            }
        }

        // Меняем директорию
        if (is_dir($dir)){
            chdir($dir);
        }
    }

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
        echo "<td><a href='index.php?fld=back'>Вернуться</a></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>Пустая папка...</td>";
        echo "</tr>";
        return;
    }elseif (!empty($_SESSION['url'])){
        echo "<tr>";
        echo "<td><a href='index.php?fld=back'>Вернуться</a></td>";
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
        echo "<td><input type='checkbox' name='fls' value='$items'>" .$ext. " " .$items. "</td>";
        echo "</tr>";
    }
}


function deleteFiles($dir, $data){
    $pattern = "|<a[^>]+>(.+?)</a>|";
    preg_match_all($pattern, $data, $out);
    unset($out[0]);


    echo $out[1];
    if (is_dir($dir."/".$data)){
        rmdir($data);
        print_r($dir."/".$data);
    }

    if (is_file($dir."/".$data)){
        unlink($data);
    }
    //echo "Файл удален";

}

?>