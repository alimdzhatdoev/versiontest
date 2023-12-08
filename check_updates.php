<?php

if (isset($_POST['check_updates'])) {
    $username = 'alimdzhatdoev';
    $repo = 'versiontest';
    $path = 'package.json';
    
    // Сформируйте URL для запроса к GitHub API
    $url = "https://api.github.com/repos/$username/$repo/contents/$path";
    
    // Если ваш репозиторий закрыт, убедитесь, что у вас есть токен авторизации
    // $token = 'ваш-токен';
    
    // Формируем заголовки для запроса
    $headers = [
        'User-Agent: Your-App-Name', // Замените на имя вашего приложения
        // Если используете токен, добавьте заголовок авторизации
        // 'Authorization: Bearer ' . $token,
    ];
    
    // Отправляем запрос и получаем результат
    $options = [
        'http' => [
            'header' => implode("\r\n", $headers),
            'method' => 'GET',
        ],
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    // Парсим JSON-ответ
    $data = json_decode($response, true);
    
    // Извлекаем версию из package.json
    if ($data && isset($data['content'])) {
        $content = base64_decode($data['content']);
        $packageJson = json_decode($content, true);
    
        if ($packageJson && isset($packageJson['version'])) {
            $version = $packageJson['version'];
            echo 'Версия проекта: ' . $version;
        } else {
            echo 'Не удалось извлечь версию из package.json';
        }
    } else {
        echo 'Не удалось получить содержимое файла package.json';
    }
    

    // Остальной код для обработки запроса или вывода результатов
    // ...

} else {
    echo 'Неверный запрос';
}
