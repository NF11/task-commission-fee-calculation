<?php

namespace App\Helper;

use SplFileInfo;

class FileInfo
{
    public function getFileInfo(string $filePath): SplFileInfo
    {
        return new SplFileInfo($filePath);
    }
}
