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

    /**
     * @param array|null $fileExts
     */
    public function __construct(?array $fileExts = ['php'])
    {
        $this->fileExts = $fileExts;
    }

    /**
     * @param string $diff
     * @return CheckingFiles
     */
    public function parse(string $diff): CheckingFiles
    {
        $line = strtok($diff, PHP_EOL);
        do {
            $this->processLine($line);
        } while (($line = strtok(PHP_EOL)) !== false);

        return new CheckingFiles($this->result);
    }

    /**
     * @return array
     */
    public function files(): array
    {
        return array_keys($this->result);
    }

    /**
     * @param string $line
     * @return void
     */
    private function processLine(string $line)
    {
        $this->detectFile($line);
        $this->processLineData($line);
    }

    /**
     * @param $line
     * @return void
     */
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

    /**
     * @param $line
     * @return void
     */
    private function processLineData($line): void
    {
        if (
            $this->currentFile !== null
            && str_starts_with($line, self::CHANGES_LINE_PREFIX)
        ) {
            preg_match('/^@@ -(\d+),?(\d+)? \+(\d+),?(\d+)? @@/', $line, $matches);
            for ($i = $matches[3]; $i <= $matches[3] + ($matches[4] ?? $matches[3]); $i++) {
                $this->result[$this->currentFile][] = (int) $i;
            }
        }
    }
}
