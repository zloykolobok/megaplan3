<?php
/**
 *  Конфигурация подключения к мегаплану
 *
 *  url - адрес мегаплана
 *  header - массив заголовков
 *  username - логин
 *  password - пароль
 *  grant_type -
 *  timeout - установка таймаута в секундах
 *  session - true - запись токена в файл
 */

return [
    'url'        => 'http://demo.megaplan.ru',
    'header'     => [
        'content-type' => 'multipart/form-data',
    ],
    'username'   => 'dev-null@megoplan.ru',
    'password'   => '123456',
    'grant_type' => 'password',
    'timeout'    => 60,
    'session'    => true,
];