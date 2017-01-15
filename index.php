<html>
<head>
    <title>1FileExplorer</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
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
            width: 449px;
            margin: 3px 3px;
        }

        a {
            color: #000000;
            text-decoration: none;
        }

        button {
            margin: 5px 3px;
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
            // Обработчик кнопки "Создать файл"
            case isset($_POST['addFile']):
                $fileOpen = fopen($_POST['realDir'].$_POST['fileName'], "w");
                fwrite($fileOpen, $_POST['fileContent']);
                fclose($fileOpen);
                break;

            // Обработчик кнопки "Создать папку"
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

            case isset($_POST['rename']):
                rename($_POST['realDir'].$_POST['oldName'], $_POST['realDir'].$_POST['newName']);
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
                    echo "<tr><td width='300px'><input type='checkbox' name='fls' value=" . $realDir . DIRECTORY_SEPARATOR . $value . "><img src=images/folder.png alt=pic class=icon> <a href=?folder=" . $realDir . DIRECTORY_SEPARATOR . $value . ">" . $value . "</a><button type=\"button\" data-toggle=\"modal\" data-target=\"#myModal_3\" name='test1' value='$value'> X </button></td>";
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
            <td><br /><button type="button" data-toggle="modal" data-target="#myModal_1">Создать папку</button><button type="button" data-toggle="modal" data-target="#myModal_2">Создать файл</button><input type="hidden" name="realDir" value="<?=$realDir . DIRECTORY_SEPARATOR;?>"></td>
        </tr>
        <tr>
            <td><button type="submit" name="delete">Удалить</button></td>
        </tr>
        <tr>
            <td><button type="button" data-toggle="modal" data-target="#myModal_3">Переименовать</button></td>
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

    <!-- Модальное окно создания папки -->
    <div id="myModal_1" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Создание папки</h4>
                </div>
                <div class="modal-body">Название папки: <input type="text" name="newFolder" placeholder="Введите название папки">
                    <input type="hidden" name="realDir" value="<?=$realDir . DIRECTORY_SEPARATOR;?>"></div>
                <div class="modal-footer"><input class="btn btn-default" type="submit" name="addFolder" value="Создать папку"></div>
            </div>
        </div>
    </div>

    <!-- Модальное окно создания файла -->
    <div id="myModal_2" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Создание файла</h4>
                </div>
                <div class="modal-body">Название файла: <input type="text" name="fileName" class="inp" placeholder="Название файла с расширением (name.txt, etc)">
                    <textarea rows="15" cols="78" name="fileContent" placeholder="Текст внутри файла"></textarea>
                    <input type="hidden" name="realDir" value="<?=$realDir . DIRECTORY_SEPARATOR;?>"></div>
                <div class="modal-footer"><input class="btn btn-default" type="submit" name="addFile" value="Создать файл"></div>
            </div>
        </div>
    </div>

    <!-- Модальное окно переименования файлов и папок -->
    <div id="myModal_3" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Переименование</h4>
                </div>
                <div class="modal-body">Новое название: <input type="text" name="fileName" class="inp" placeholder="Название файла с расширением (name.txt, etc)">
                    <input type="text" name="newName" value="<?=basename($_POST['fls']);?>">
                    <input type="hidden" name="oldName" value="<?=basename($_POST['fls']);?>">
                    <input type="hidden" name="realDir" value="<?=$realDir . DIRECTORY_SEPARATOR;?>"></div>
                <div class="modal-footer"><input class="btn btn-default" type="submit" name="rename" value="Переименовать"></div>
            </div>
        </div>
    </div>



    <?php

    // Вывод редактирования файла
    @fileView($_GET['view']);

    ?>
</form>

<?php

function test($value) {
    echo <<<END<div id='myModal_3' class='modal fade'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'><button class='close' type='button' data-dismiss='modal'>×</button>
                    <h4 class='modal-title'>Переименование</h4>
                </div>
                <div class='modal-body\">Новое название:
                    <input type='text\" name=\"newName\" value='$value'>
                    <input type='hidden' name='oldName'>
                    <input type='hidden' name='realDir'></div>
                <div class='modal-footer'><input class='btn btn-default' type='submit' name='rename' value='Переименовать'></div>
            </div>
        </div>
    </div>"
    END;
}

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
