<?php
declare(strict_types=1);

require_once __DIR__ . '/paths.php';

/**
 * Выполняет рендеринг указанного PHP-шаблона с указанными переменными
 */
function renderView(string $templateName, array $vars = []): string
{
    $templatePath = getTemplatePath($templateName);

    if (!ob_start())
    {
        throw new \RuntimeException("Failed to render '$templateName': ob_start() failed");
    }
    try
    {
        renderPhpTemplateImpl($templatePath, $vars);
        $contents = ob_get_contents();
    }
    finally
    {
        ob_end_clean();
    }

    return $contents;
}

/**
 * Внутренняя фунция
 *  1. Выполняет extract() для установки PHP переменных
 *  2. Затем выполняет require() для подключения PHP-шаблона
 */
function renderPhpTemplateImpl(string $phpTemplatePath, array $phpTemplateVariables): void
{
    extract($phpTemplateVariables, EXTR_SKIP);
    require($phpTemplatePath);
}
