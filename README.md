# Laravel Crud Generator

Проект создан быстрой генерации файлов для админки сайтов.
создаются файлы: Model, Repository, Service, Controller, view на основе указанной таблицы

Примеры кода будут выложены позже

Базовое использование:
php artisan gen:all tableName

Artisan::call('email:send', [
'table' => 'event',
'--enum' => 'type-sport,home,work;status-active,inactive,deleted'
]);


Общие настройки конфигурации по желанию в файле .env

GENERATOR_BASE_NS= - базовый namespace, где хранятся Entities, Enum, Repository, Service

GENERATOR_HTTP_NS= - базовый namespace, где хранятся Controller

GENERATOR_MODEL_FOLDER_NAME=

GENERATOR_REPOSITORY_FOLDER_NAME=

GENERATOR_ENUM_FOLDER_NAME=

GENERATOR_SERVICE_FOLDER_NAME=

GENERATOR_REPOSITORY_SUFFIX=

GENERATOR_CONTROLLER_SUFFIX=

GENERATOR_SERVICE_SUFFIX=

GENERATOR_ENUM_STATUS_SUFFIX=

GENERATOR_VIEW_FILE_SUFFIX=
