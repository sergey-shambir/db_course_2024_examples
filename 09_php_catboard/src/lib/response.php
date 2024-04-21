<?php
declare(strict_types=1);

require_once __DIR__ . '/views.php';

const HTTP_STATUS_303_SEE_OTHER = 303;
const HTTP_STATUS_400_BAD_REQUEST = 400;
const HTTP_STATUS_404_NOT_FOUND = 404;
const HTTP_STATUS_500_INTERNAL_ERROR = 500;

/**
 * Выполняет Redirect со статусом "303 See Other",
 *  что дополнительно приводит к изменению на GET любого другого HTTP-метода.
 *
 * https://developer.mozilla.org/ru/docs/Web/HTTP/Status/303
 */
function writeRedirectSeeOther(string $url): void
{
    header('Location: ' . $url, true, HTTP_STATUS_303_SEE_OTHER);
}

function writeErrorBadRequest(): void
{
    writeErrorPageImpl([
        'code' => HTTP_STATUS_400_BAD_REQUEST,
        'text' => 'Неправильный формат запроса',
        'hint' => 'Попробуйте ещё раз позже'
    ]);
}

function writeErrorNotFound(): void
{
    writeErrorPageImpl([
        'code' => HTTP_STATUS_404_NOT_FOUND,
        'text' => 'Страница не найдена',
        'hint' => 'Вероятно, вы перешли по несуществующей ссылке'
    ]);
}

function writeInternalError(): void
{
    writeErrorPageImpl([
        'code' => HTTP_STATUS_500_INTERNAL_ERROR,
        'text' => 'Внутренняя ошибка сервера',
        'hint' => 'Попробуйте ещё раз позже'
    ]);
}

/**
 * @param array{code:int,text:string,hint:string} $error
 * @return void
 */
function writeErrorPageImpl(array $error): void
{
    echo renderView('error_page.php', $error);
    http_response_code($error['code']);
}
