<?php
declare(strict_types=1);

require_once __DIR__ . '/paths.php';

/**
 * @param array{name:string,tmp_name:string,error:int,size:int} $fileInfo
 * @return array{path:string,width:int,height:int,mime_type:string}
 * @throws InvalidArgumentException
 */
function uploadImageFile(array $fileInfo): array
{
    $imageName = $fileInfo['name'];
    validateImageUploadError($imageName, $fileInfo['error']);
    validateImageUploadBytesSize($imageName, $fileInfo['size']);

    $imageInfo = getImageFileInfo($fileInfo);
    $fileExtension = getImageFileExtension($imageName, $imageInfo['mime_type']);

    $uploadAbsolutePath = moveFileToUploadsDir($fileInfo['tmp_name'], 'img', $fileExtension);
    $imageInfo['path'] = getUploadRelativePath($uploadAbsolutePath);

    return $imageInfo;
}

function moveFileToUploadsDir(string $fileTempPath, string $namePrefix, string $extension): string
{
    $uploadPath = generateNewUploadPath($namePrefix, $extension);
    if (!move_uploaded_file($fileTempPath, $uploadPath))
    {
        throw new RuntimeException("Failed to move file '$fileTempPath' to the uploads directory");
    }

    return $uploadPath;
}

/**
 * @throws InvalidArgumentException
 */
function getImageFileExtension(string $imageName, string $mimeType): string
{
    return match ($mimeType)
    {
        'image/jpeg' => '.jpg',
        'image/webp' => '.webp',
        default => throw new InvalidArgumentException("Изображение '$imageName' имеет недопустимый тип '$mimeType' . Разрешены только JPEG и WEBP . ")
    };
}

function generateNewUploadPath(string $prefix, string $extension): string
{
    for ($attempt = 0; $attempt < 5; ++$attempt)
    {
        $fileName = uniqid($prefix, true) . $extension;
        $filePath = getUploadAbsolutePath($fileName);
        if (!file_exists($filePath))
        {
            if (!touch($filePath))
            {
                throw new RuntimeException("Failed to create file '$filePath' on the uploads directory");
            }
            return $filePath;
        }
    }
    throw new RuntimeException("Failed to generate file path for the new upload");
}

/**
 * @param array{name:string,tmp_name:string,error:int,size:int} $fileInfo
 * @return array{width:int,height:int,mime_type:string}
 * @throws InvalidArgumentException
 */
function getImageFileInfo(array $fileInfo): array
{
    $imageSizeInfo = getimagesize($fileInfo['tmp_name']);
    if (!$imageSizeInfo)
    {
        $imageName = $fileInfo['name'];
        throw new InvalidArgumentException("Файл '$imageName' не является изображением");
    }

    return [
        'mime_type' => $imageSizeInfo['mime'],
        'width' => $imageSizeInfo[0],
        'height' => $imageSizeInfo[1],
    ];
}

/**
 * @throws InvalidArgumentException
 */
function validateImageUploadError(string $imageName, int $uploadErrorCode): void
{
    if ($uploadErrorCode === UPLOAD_ERR_OK)
    {
        return;
    }

    $errorMessage = match ($uploadErrorCode)
    {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => "файл слишком большой",
        UPLOAD_ERR_PARTIAL => 'файл частично потерян',
        UPLOAD_ERR_NO_FILE => 'файл не был загружен',
        default => "неизвестная ошибка загрузки(код ошибки $uploadErrorCode)"
    };
    throw new InvalidArgumentException("Не удалось загрузить изображение '$imageName': $errorMessage");
}

/**
 * @throws InvalidArgumentException
 */
function validateImageUploadBytesSize(string $imageName, int $byteCount): void
{
    $maxByteCount = 2 * 1024 ** 2; // 2 мегабайта
    $maxByteCountStr = '2 МБ';
    if ($byteCount > $maxByteCount)
    {
        throw new InvalidArgumentException("Файл изображения '$imageName' слишком большой . Размер не должен превышать $maxByteCountStr . ");
    }
}
