<?php
declare(strict_types=1);

const HTTP_METHOD_GET = 'GET';
const HTTP_METHOD_POST = 'POST';

function isRequestHttpMethod(string $method): bool
{
    return $_SERVER['REQUEST_METHOD'] === $method;
}
