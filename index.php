<?php

include './db.php';
$pdo = getPDO();
$stmt = $pdo->prepare("TRUNCATE TABLE `films`");
$stmt->execute();

foreach (glob('films\\' . '*.html') as $htmlFile) {
    $html = basename($htmlFile);
    $subject = file_get_contents('films\\' . $html);

    $pattern = '/-(.*?)-\d{4}/';
    preg_match($pattern, $html, $matches);
    $filmname = $matches[1];
    
    $pattern = '#IMDb</a>: <span class="bold">([\d.]+)</span>.*?Кинопоиск</a>: <span class="bold">([\d.]+)</span>#';
    preg_match($pattern, $subject, $matches);
    $IMDb = $matches[1];
    $kinopoisk = $matches[2];

    $pattern = '#<td class="rd">(.*?)<\/td>#s';
    preg_match($pattern, $subject, $matches);
    $lists = strip_tags($matches[1]);

    $pattern = '#<td class="l"><h2>Слоган<\/h2>:</td>\s*<td>&laquo;(.*?)&raquo;<\/td>#us';
    preg_match($pattern, $subject, $matches);
    $tagline = $matches[1];

    $pattern = '#<td class="l"><h2>Дата выхода<\/h2>:</td>\s*<td>(\d{1,2} [а-я]+) <a[^>]*>(\d{4}) года<\/a><\/td>#u';
    preg_match($pattern, $subject, $matches);
    $day = $matches[1];
    $year = $matches[2];
    $release = $day . ' ' . $year;

    $pattern = '#<td class="l"><h2>Страна<\/h2>:<\/td>\s*<td><a[^>]*>(.*?)<\/a><\/td>#s';
    preg_match($pattern, $subject, $matches);
    $country = strip_tags($matches[1]);

    $pattern = '#<td class="l"><h2>Режиссер<\/h2>:<\/td>\s*<td>\s*<div class="persons-list-holder">\s*<span class="item"><span class="person-name-item".*?>(.*?)<\/span>#s';
    preg_match($pattern, $subject, $matches);
    $director = strip_tags($matches[1]);

    $pattern = '#<td class="l"><h2>Жанр<\/h2>:</td>\s*<td[^>]*>(.*?)<\/td>#us';
    preg_match($pattern, $subject, $matches);
    $genre = strip_tags($matches[1]);

    $pattern = '#<td class="l"><h2>В качестве<\/h2>:</td>\s*<td>(.*?)<\/td>#us';
    preg_match($pattern, $subject, $matches);
    $quality = $matches[1];

    $pattern = '#<td class="l"><h2>В переводе<\/h2>:</td>\s*<td>(.*?)<\/td>#us';
    preg_match($pattern, $subject, $matches);
    $translate = $matches[1];

    $pattern = '#<td class="l"><h2>Возраст<\/h2>:</td>\s*<td><span class="bold"[^>]*>(.*?)<\/span>\s*(.*?)<\/td>#s';
    preg_match($pattern, $subject, $matches);
    $age = $matches[1];

    $pattern = '#<td class="l"><h2>Время<\/h2>:</td>\s*<td[^>]*>(.*?)<\/td>#us';
    preg_match($pattern, $subject, $matches);
    $duration = $matches[1];

    $pattern = '#<td class="l"><h2>Из серии<\/h2>:</td>\s*<td>(.*?)<\/td>#us';
    preg_match($pattern, $subject, $matches);
    $collections = strip_tags($matches[1]);

    $pattern = '#<h2>В ролях актеры<\/h2>:(.*?)<\/div>#us';
    preg_match($pattern, $subject, $matches);
    $actors = strip_tags($matches[1]);


    $sql = "INSERT INTO `films` 
            (`filmname`, `IMDb`, `kinopoisk`, `lists`, `tagline`, `release`, `country`, `director`, `genre`, `quality`, `translate`, `age`, `duration`, `collections`, `actors`) 
            VALUES 
            (:filmname, :IMDb, :kinopoisk, :lists, :tagline, :release, :country, :director, :genre, :quality, :translate, :age, :duration, :collections, :actors)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':filmname' => $filmname ?? '',
        ':IMDb' => $IMDb ?? '',
        ':kinopoisk' => $kinopoisk ?? '',
        ':lists' => $lists ?? '',
        ':tagline' => $tagline ?? '',
        ':release' => $release ?? '',
        ':country' => $country ?? '',
        ':director' => $director ?? '',
        ':genre' => $genre ?? '',
        ':quality' => $quality ?? '',
        ':translate' => $translate ?? '',
        ':age' => $age ?? '',
        ':duration' => $duration ?? '',
        ':collections' => $collections ?? '',
        ':actors' => $actors ?? ''
    ]);


}

