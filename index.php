<?php

declare(strict_types=1);

if(file_exists("./config.php")) {
    require_once "./config.php";
} else {
    echo "Please make config.php from config.example.php.";
    exit();
}

// Константы
const SHORT_URL_LENGTH = 3;

// Временно для вывода ошибок и сообщений
$messages = array();

// Данные для кодирования
$chars_str = "HhAzmcl9Dbd1nOeTwYyL47WQvUiZapJs3V5SgENCXtGfMFrBxuPkoRKq60j2I8";
$base_map = str_split($chars_str);
$base_size = count($base_map);

// Получение массива остатков от деления числа (number) на основание (base)
function GetDivisionRemainders(int $number, int $base): array {
    $result = array();
    if($base <= 1) { return $result; }

    do {
        $remainder = $number % $base;
        $number = intdiv($number, $base);
        $result[] = $remainder;
    } while(abs($number) > 0);

    return $result;
}

// Кодирование числа, используя карту соответствия остатка к одному из возможных значений
function Encode(int $number, int $base, array $map): string {
    $result = "";
    if($base > count($map)) { return $result; }

    $remainders = GetDivisionRemainders($number, $base);
    // !!! Не надо для функции общего назначения.
    // Проверка входного number на вхождение в [0; base_size ** SHORT_URL_LENGTH)
    // Происходит на более высоком уровне
    //if(count($remainders) > SHORT_URL_LENGTH) { return $result; }

    foreach($remainders as $rem) {
        $result .= $map[abs($rem)];
    }
    // !!! Не надо для функции общего назначения (Происходит на более высоком уровне)
    //$result = str_pad($result, SHORT_URL_LENGTH, $map[0]);

    return $result;
}

// Декодирование строки, используя карту соответствия остатка к одному из возможных значений
function Decode(string $encoded, int $base, array $map): int {
    $result = null;
    if($base > count($map)) { return $result; }

    $remainders = str_split($encoded);
    foreach($remainders as $key => $value) {
        $rem = array_search($value, $map);
        $result += $rem * pow($base, $key);
    }

    return $result;
}

$dsn = "mysql:dbname=" .$config["database"]["dbname"] .";host=" .$config["database"]["host"] .";charset=" .$config["database"]["charset"];
$db = new PDO($dsn, $config["database"]["user"], $config["database"]["pass"]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_POST["submit_url"])) {
    $full_url = filter_input(INPUT_POST, "full_url", FILTER_SANITIZE_URL) ?: "";
    $full_url = filter_var($full_url, FILTER_VALIDATE_URL) ?: "";
    if($full_url !== "") {
        // Ссылка прошла валидацию
        // Добавляем ссылку в базу данных
        $stmt = $db->prepare("INSERT INTO Urls (full_url) VALUES (:full_url)");
        $stmt->bindValue(":full_url", $full_url);
        $stmt->execute();

        // Получаем id добавленной записи
        $stmt = $db->query("SELECT LAST_INSERT_ID() as id");
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $id = $data["id"];

        // Кодируем id
        // Проверяем, что id не больше максимального значения для заданной длинны короткой ссылки
        if($id >= pow($base_size, SHORT_URL_LENGTH)) {
            exit("Maximum available short URLs reached.");
        }
        $short_url = Encode($id, $base_size, $base_map);
        // Дополняем ссылку символами до желаемой длинны
        $short_url = str_pad($short_url, SHORT_URL_LENGTH, $base_map[0]);

        // Выводим короткую ссылку
        echo "<form><p>Short URL:</p><input type='text' value=" .$config["urls"]["short_url"] .$short_url ." readonly /></form><br />";
        
    } else {
        $messages[] = "Not valid URL!";
    }
}

if($_SERVER["REQUEST_URI"] !== "/") {
    $recieved_url = trim($_SERVER["REQUEST_URI"], "/");
    $recieved_url = filter_var($recieved_url, FILTER_SANITIZE_ENCODED) ?: "";
    if($recieved_url !== "") {
        // Проверяем на необходимую длинну ссылки
        if(strlen($recieved_url) > SHORT_URL_LENGTH) {
            $messages[] = "Short URL is too long.";
            header("Location: /");
            exit();
        }

        // Декодируем короткую ссылку и получаем id записи
        $id = Decode($recieved_url, $base_size, $base_map);

        // Запрашиваем полную ссылку из базы данных по id
        $stmt = $db->prepare("SELECT full_url FROM Urls WHERE id = :id LIMIT 1");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        if($data === false) {
            $messages[] = "This short URL is not exist.";
            header("Location: /");
            exit();
        }
        $full_url = $data["full_url"];

        // Перенаправляем по полной ссылке
        header("Location: $full_url");
        exit();
    }
}

?>

<form method="POST" action="/">
    <input type="text" name="full_url" placeholder="Enter URL to get short version" value="" required />
    <button type="submit" name="submit_url" value="submit">Shorten</button>
</form>

<?php
foreach($messages as $msg) {
    echo "<p>$msg</p>";
}
?>