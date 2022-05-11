<?php

namespace Xinger\Tap;

class NdjsonTap
{
    private $filename;
    private $bookmark;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->bookmark = 0;
    }

    public function open()
    {
        $this->handle = fopen($this->filename, "r");
    }

    public function getRecord(): ?array
    {
        $line = fgets($this->handle);
        if (!$line) {
            return null;
        }
        $data = json_decode($line, true);
        return $data;
    }

    public function close()
    {
        fclose($this->handle);
    }
}
