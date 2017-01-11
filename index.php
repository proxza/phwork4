<html>
<head>
    <title>1FileExplorer</title>
    <style>
        body {
            background: #ffffff;
            font-family: Tahoma;
            font-size: 14px;
        }

        .icon {
            width: 18px;
            height: 18px;
            border: 0px;
            vertical-align: middle;
        }

        .inp {
            width: 300px;
        }

        a {
            color: #000000;
            text-decoration: none;
        }
    </style>
</head>
<body>

<form action="index.php" method="post" enctype="multipart/form-data">
    <table>
        <?php

        // Прописываем корневую директорию
        $baseDir = getcwd();

        // Временно
        echo 'Базовая директория: <strong>' . $baseDir . '</strong>';

        // Проверка на входящий GET-запрос
        if (!empty($_GET['folder'])) {
            $realDir = $_GET['folder'];
        }else{
            $realDir = '';
        }

        // Указываем абсолютный путь
        $realDir = realpath($realDir);

        // Условие, если переданный запрос является директорией то переходим в неё
        // Можно в будущем убрать лишний IF
        if ($realDir) {
            if(is_dir($realDir)) {
                chdir($realDir);
            } else {
                echo 'Указанный путь не является директорией ' . $realDir;
            }
            echo '<br>Текущая директория: <strong>' . $realDir . '</strong><br />';
        }

        // Сканируем/читаем директорию
        $data = scandir($realDir);

        // Сортировка
        natcasesort($data);

        // Загрузка файлов
        if (@is_uploaded_file($_FILES['uploadFile']['tmp_name'])) {
            $name = $_FILES['uploadFile']['name'];
            $uploadDir = $_POST['realDir'] . DIRECTORY_SEPARATOR . basename($_FILES['uploadFile']['name']);
            if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadDir)) {
                echo "Файл загружен!";
            }else{
                echo "OTAKE";
            }
        }

        // Обработка кнопок редактирования
        switch (true){
            // Кнопка "создать папку"
            case isset($_POST['create']):
                echo "<input type='text' name='newFolder' placeholder='Название папки'>";
                echo "<input type='hidden' name='realDir' value='".$_POST['realDir']."'>";
                echo "<input type='submit' name='addFolder' value='Создать папку'>";
                exit;
            case isset($_POST['createFile']):
                echo "<input type='text' name='fileName' placeholder='Название файла с расширением (name.txt, etc)' class='inp'> <br />";
                echo "<input type='text' name='fileContent' placeholder='Текст файла' class='inp'> <br />";
                echo "<input type='hidden' name='realDir' value='".$_POST['realDir']."'>";
                echo "<input type='submit' name='addFile' value='Создать файл'>";
                exit;
            case isset($_POST['addFile']):
                $fileOpen = fopen($_POST['realDir'].$_POST['fileName'], "w");
                fwrite($fileOpen, $_POST['fileContent']);
                fclose($fileOpen);
                break;
            case isset($_POST['addFolder']):
                @mkdir($_POST['realDir'].$_POST['newFolder'], 0777);
                break;

            // Кнопка "удалить"
            case isset($_POST['delete']):
                fileDelete($_POST['fls']);
                chdir($_POST['realDir']);
                break;

            // Кнопка "переименовать"
            case isset($_POST['rename']):
                echo "<input type='text' name='newname' value='".basename($_POST['fls'])."' placeholder='Новое название'>";
                echo "<input type='hidden' name='oldname' value='".basename($_POST['fls'])."'>";
                echo "<input type='hidden' name='realDir' value='".$_POST['realDir']."'>";
                echo "<input type='submit' name='edit' value='Save'>";
                exit;

            case isset($_POST['edit']):
                rename($_POST['realDir'].$_POST['oldname'], $_POST['realDir'].$_POST['newname']);
                // Остановка скрипта и обновление страницы
                exit("<meta http-equiv='refresh' content='0; url= $_SERVER[PHP_SELF]'>");
            case isset($_POST['copy']):
                echo "COPY";
                break;

            // Кнопка "загрузить"
            case isset($_POST['upload']):
                echo "<input type='file' name='uploadFile'>";
                echo "<input type='hidden' name='realDir' value='".$_POST['realDir']."'>";
                echo "<input type='submit' value='Загрузить'>";
                exit;

            // Кнопка "права доступа"
            case isset($_POST['chmode']):
                echo "<tr><td><input type='checkbox' name='ch1'></td><td> - Доступ и запись для владельца/Другим доступа нет (0600)</td></tr>";
                echo "<tr><td><input type='checkbox' name='ch2'></td><td> - Доступ и запись для владельца/Другим на чтение для других (0644)</td></tr>";
                echo "<tr><td><input type='checkbox' name='ch3'></td><td> - Полный доступ для владельца/Другим на чтение и выполнение для других (0755)</td></tr>";
                echo "<tr><td><input type='checkbox' name='ch4'></td><td> - god mode (0777)</td></tr>";
                echo "<input type='hidden' name='chfls' value='".$_POST['fls']."'>";
                echo "<tr><td colspan='2'><input type='submit' name='saveMode' value='Применить'></td></tr>";
                exit;
            case isset($_POST['saveMode']):
                if (isset($_POST['ch1'])) {
                    chmod($_POST['chfls'], 0600);
                }elseif (isset($_POST['ch2'])) {
                    chmod($_POST['chfls'], 0644);
                }elseif (isset($_POST['ch3'])) {
                    chmod($_POST['chfls'], 0755);
                }elseif (isset($_POST['ch4'])) {
                    chmod($_POST['chfls'], 0777);
                }
                break;

        }

        // Вывод
        foreach ($data as $key => $value) {
            if ($value != ".") {
                // Проверка является ли значение директорией
                if (is_dir($value) == true) {
                    echo "<tr><td width='300px'><input type='checkbox' name='fls' value=" . $realDir . DIRECTORY_SEPARATOR . $value . "><img src=images/folder.png alt=pic class=icon> <a href=?folder=" . $realDir . DIRECTORY_SEPARATOR . $value . ">" . $value . "</a></td>";
                    echo "<td></td></tr>";
                }

                // Проверка является ли значение файлом
                if (is_file($value) == true) {
                    // Отделяем расширение файлов для иконок
                    $ext = substr(strrchr($value, '.'), 1);
                    echo "<tr><td width='300px'><input type='checkbox' name='fls' value=" . $realDir . DIRECTORY_SEPARATOR . $value . ">" . fileExtentsion($ext) . " <a href=?view=" . $realDir . DIRECTORY_SEPARATOR . $value .">$value</a></td>";
                    echo "<td></td></tr>";
                }
            }
        }

        ?>
        <tr>
            <td><br /><input type="submit" name="create" value="Создать папку"> <input type="submit" name="createFile" value="Создать файл"><input type="hidden" name="realDir" value="<?=$realDir . DIRECTORY_SEPARATOR;?>"></td>
        </tr>
        <tr>
            <td><input type="submit" name="delete" value="Удалить"></td>
        </tr>
        <tr>
            <td><input type="submit" name="rename" value="Переименовать"></td>
        </tr>
        <tr>
            <td><input type="submit" name="copy" disabled value="Копировать"></td>
        </tr>
        <tr>
            <td><input type="submit" name="chmode" value="Права доступа"></td>
        </tr>
        <tr>
            <td><input type="submit" name="upload" value="Загрузить"></td>
        </tr>
    </table>

    <?php

    // Вывод редактирования файла
    @fileView($_GET['view']);

    ?>
</form>

<?php

/** Функция редактирования файлов
 * @param $view
 */
function fileView ($view) {

    if (isset($view)) {
        ?>

        <textarea name="content" rows="15" cols="70"><?=file_get_contents($view);?></textarea>
        <input type="hidden" name="value" value="<?=$view;?>">
        <br />
        <input type="submit" name="edit">

        <?php

    }

    if (isset($_POST['edit'])) {
        file_put_contents($_POST['value'], $_POST['content']);
    }
}


/** Функция разбивки расширений для файлов
 * @param $ext
 * @return string
 */
function fileExtentsion ($ext) {
    switch ($ext) {
        // Формат картинки если архив
        case "rar";
        case "zip":
            $ext = '<img src="images/zip.png" alt="pic" class="icon">';
            break;

        // Если документ
        case "txt";
        case "doc";
        case "xls";
        case "rtf";
        case "docx":
            $ext = '<img src="images/office.png" alt="pic" class="icon">';
            break;

        // Если html-php-css
        case "html";
        case "css";
        case "php":
            $ext = '<img src="images/php.png" alt="pic" class="icon">';
            break;

        // Если картинка
        case "png";
        case "jpg":
            $ext = '<img src="images/images.png" alt="pic" class="icon">';
            break;

        // По умолчанию
        default:
            $ext = '<img src="images/other.png" alt="pic" class="icon">';
    }
    return $ext;
}


/** Функция удаления
 * @param $file
 */
function fileDelete ($file){
    if (is_file($file)) {
        unlink($file);
    }

    if (is_dir($file)) {
        rmdir($file);
    }
    // Остановка скрипта и обновление страницы
    exit("<meta http-equiv='refresh' content='0; url= $_SERVER[PHP_SELF]'>");
}

?>

</body>
</html>
