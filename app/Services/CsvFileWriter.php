<?php

declare(strict_types=1);

namespace App\Services;

class CsvFileWriter
{
    private const CSV_SEPARATOR = ',';
    private string $path;

    public function __construct()
    {
        $this->path = config('params.outputCsv');
    }

    public function write($data)
    {
        $fp = fopen($this->path . '/result.csv', 'w');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }
}
