<?php

if (isset($_POST['check_updates'])) {
    // Путь к вашему package.json файлу на сервере
    $localPackageJsonPath = __DIR__ . '/package.json';

    // GitHub репозиторий
    $githubUsername = 'alimdzhatdoev';
    $githubRepo = 'versiontest';
    $githubPath = 'package.json';

    // GitHub API URL
    $githubUrl = "https://api.github.com/repos/$githubUsername/$githubRepo/contents/$githubPath";

    // GitHub API заголовки
    $githubHeaders = [
        'User-Agent: Your-App-Name', // Замените на имя вашего приложения
        // Если используете токен, добавьте заголовок авторизации
        // 'Authorization: Bearer ' . $token,
    ];

    // Отправляем запрос к GitHub API
    $githubOptions = [
        'http' => [
            'header' => implode("\r\n", $githubHeaders),
            'method' => 'GET',
        ],
    ];

    $githubContext = stream_context_create($githubOptions);
    $githubResponse = file_get_contents($githubUrl, false, $githubContext);

    // Парсим JSON-ответ
    $githubData = json_decode($githubResponse, true);

    // Извлекаем версию из package.json на GitHub
    if ($githubData && isset($githubData['content'])) {
        $githubContent = base64_decode($githubData['content']);
        $githubPackageJson = json_decode($githubContent, true);

        // Извлекаем версию из package.json на сервере
        $localContent = file_get_contents($localPackageJsonPath);
        $localPackageJson = json_decode($localContent, true);

        // Сравниваем версии
        if ($githubPackageJson && $localPackageJson && isset($githubPackageJson['version']) && isset($localPackageJson['version'])) {
            $githubVersion = $githubPackageJson['version'];
            $localVersion = $localPackageJson['version'];

            if (version_compare($githubVersion, $localVersion, '>')) {
                echo 'Есть обновление! Текущая версия: ' . $localVersion . ', Новая версия: ' . $githubVersion;
                // Добавьте здесь код для уведомления пользователя о необходимости обновления
            } else {
                echo 'У вас актуальная версия: ' . $localVersion;
            }
        } else {
            echo 'Не удалось извлечь версию из package.json';
        }
    } else {
        echo 'Не удалось получить содержимое файла package.json с GitHub';
    }
} else {
    echo 'Неверный запрос';
}
