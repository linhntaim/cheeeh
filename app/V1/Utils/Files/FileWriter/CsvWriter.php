<?php

namespace App\V1\Utils\Files\FileWriter;

class CsvWriter extends FileWriter
{
    public function __construct($name, $stored = false, $toDirectory = '', $isRelative = false)
    {
        if (is_array($name)) {
            $name['extension'] = 'csv';
        } else {
            $name = [
                'name' => $name,
                'extension' => 'csv',
            ];
        }

        parent::__construct($name, $stored, $toDirectory, $isRelative);
    }

    public function write($anything)
    {
        fputcsv($this->handler, $anything);
        return $this;
    }

    public function writeMany($anything)
    {
        foreach ($anything as $item) {
            $this->write($item);
        }
        return $this;
    }
}
