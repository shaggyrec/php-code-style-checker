<?php

namespace Shaggyrec\CodeStyleChecker;

class GitDiffParser
{
    const FILE_LINE_PREFIX = '+++ b/';
    const CHANGES_LINE_PREFIX = '@@ ';

    /**
     * @var string[]
     */
    private array $fileExts;

    private ?string $currentFile = null;

    private array $result = [];

    public function __construct(?array $fileExts = ['php'])
    {
        $this->fileExts = $fileExts;
    }

    public function parse(string $diff)
    {
        $line = strtok($diff, PHP_EOL);
        do {
            $this->processLine($line);
        } while (($line = strtok(PHP_EOL)) !== false);

        return new CheckingFiles($this->result);
    }

    public function files(): array
    {
        return array_keys($this->result);
    }

    private function processLine(string $line)
    {
        $this->detectFile($line);
        $this->processLineData($line);
    }

    private function detectFile($line)
    {
        if (str_starts_with($line, self::FILE_LINE_PREFIX)) {
            $filePath = str_replace(self::FILE_LINE_PREFIX, '', $line);
            if (in_array(pathinfo($filePath, PATHINFO_EXTENSION), $this->fileExts)) {
                $this->currentFile = $filePath;
            } else {
                $this->currentFile = null;
            }
        }
    }

    private function processLineData($line)
    {
        if (
            $this->currentFile !== null
            && str_starts_with($line, self::CHANGES_LINE_PREFIX)
        ) {
            preg_match('/^@@ -(\d+),(\d+) \+(\d+),(\d+) @@/', $line, $matches);
            for ($i = $matches[3]; $i < $matches[3] + $matches[4]; $i++) {
                $this->result[$this->currentFile][] = (int) $i;
            }
        }
    }
}
