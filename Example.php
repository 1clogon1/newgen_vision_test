<?php

require 'YandexMusicParser.php';

$db = 'mysql:host=localhost;dbname=YandexMusic;charset=utf8mb4';
$user = 'username';
$password = 'password';

$url = 'https://music.yandex.ru/artist/36800/tracks';

try {

    $PDO = new PDO($db, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $parser = new YandexMusicParser($PDO);
    $data = $parser->parser($url);

    echo "Исполнитель: " . $data['artist'] . PHP_EOL;
    echo "Слушателей за месяц: " . $data['listeners'] . PHP_EOL;
    echo "Треки:" . PHP_EOL;
    foreach ($data['musics'] as $track) {
        echo " - $track" . PHP_EOL;
    }

} catch (Exception $e) {
    echo "Ошибка парсинга: " . $e->getMessage();
}