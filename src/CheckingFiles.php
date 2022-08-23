<?php

namespace Shaggyrec\CodeStyleChecker;

/**
 * Class CheckingFiles
 *
 * @package Shaggyrec\CodeStyleChecker
 */
class CheckingFiles
{
    private string $rootPath = '';

    private bool $withLines;

    private array $lines;

    private array $files;

    /**
     * @param array $files
     */
    public function __construct(array $files)
    {
        $filenames = array_keys($files);
        $this->withLines = is_string($filenames[0]);
        $this->files = $this->withLines ? $filenames : $files;

        $this->lines = $this->withLines ? $files : [];
    }

    /**
     * @param string $path
     * @return $this
     */
    public function rootPath(string $path): self
    {
        $this->rootPath = $path . '/';

        return $this;
    }

    /**
     * @return string
     */
    public function filesToString(): string
    {
        return implode(
            PHP_EOL,
            array_map(
                function (string $file) {
                    return $this->rootPath . $file;
                },
                $this->files,
            ),
        );
    }

    /**
     * @param string $file
     * @return mixed
     */
    public function lines(string $file)
    {
        return $this->lines[$file] ?? $this->lines[str_replace($this->rootPath . '/', '', $file)];
    }

    /**
     * @return bool
     */
    public function withLines(): bool
    {
        return $this->withLines;
    }
}
