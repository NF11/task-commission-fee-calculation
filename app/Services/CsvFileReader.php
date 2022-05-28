<?php

declare(strict_types=1);

namespace App\Services;

use App\Helper\FileInfo;
use InvalidArgumentException;
use RuntimeException;

class CsvFileReader
{
    private const CSV_SEPARATOR = ',';
    private FileInfo $fileInfo;

    public function __construct(FileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }

    public function read(string $filePath): array
    {
        $fileInfo = $this->fileInfo->getFileInfo($filePath);

        if (!$fileInfo->isFile()) {
            throw new InvalidArgumentException("The file \"$filePath\" is not a valid file");
        }

        if (!$fileInfo->isReadable()) {
            throw new RuntimeException("The file \"$filePath\" cannot be read");
        }

        if ($fileInfo->getExtension() != 'csv') {
            throw new RuntimeException("The file extension \"$filePath\" is not valid");
        }

        $splFile = $fileInfo->openFile();

        $data = [];
        while (!$splFile->eof()) {
            $row = $splFile->fgetcsv(self::CSV_SEPARATOR);
            if (!empty(current($row))) {
                $data[] = $row;
            }
            $splFile->next();
        }

        return $data;
    }
}
