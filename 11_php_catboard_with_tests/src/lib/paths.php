<?php
declare(strict_types=1);

const PUBLIC_DIR_NAME = 'public';
const UPLOADS_DIR_NAME = 'uploads';
const CONFIG_DIR_NAME = 'config';
const TEMPLATES_DIR_NAME = 'templates';

/**
 * Соединяет компоненты пути в один путь, используя разделитель '/' для Unix или '\' для Windows
 */
function joinPath(string ...$components): string
{
    return implode(DIRECTORY_SEPARATOR, array_filter($components));
}

/**
 * Возвращает путь к файлу конфигурации
 */
function getConfigPath(string $configFileName): string
{
    return joinPath(getProjectRootPath(), CONFIG_DIR_NAME, $configFileName);

}

/**
 * Возвращает путь к файлу PHP-шаблона
 */
function getTemplatePath(string $templateName): string
{
    return joinPath(getProjectRootPath(), TEMPLATES_DIR_NAME, $templateName);
}

/**
 * Возвращает путь к каталогу проекта
 */
function getProjectRootPath(): string
{
    return dirname(__DIR__, 2);
}

/**
 * @param string $relativeFilePath - относительный путь (или имя файла) в каталоге загружаемых файлов
 * @return string - абсолютный путь к каталогу загружаемых файлов
 */
function getUploadAbsolutePath(string $relativeFilePath): string
{
    return joinPath(getProjectRootPath(), PUBLIC_DIR_NAME, UPLOADS_DIR_NAME, $relativeFilePath);
}

/**
 * @param string $absoluteFilePath - абсолютный путь к каталогу загружаемых файлов
 * @return string - относительный путь в каталоге загружаемых файлов
 */
function getUploadRelativePath(string $absoluteFilePath): string
{
    $prefix = getUploadAbsolutePath('') . DIRECTORY_SEPARATOR;
    return str_starts_with($absoluteFilePath, $prefix)
        ? substr($absoluteFilePath, strlen($prefix))
        : $absoluteFilePath;
}

function getUploadUrlPath(string $relativeFilePath): string
{
    return sprintf('/%s/%s', UPLOADS_DIR_NAME, $relativeFilePath);
}
