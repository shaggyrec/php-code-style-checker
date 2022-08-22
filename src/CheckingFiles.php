<?php

namespace Shaggyrec\CodeStyleChecker;

class CheckingFiles
{
    private string $rootPath = '';

    private bool $withLines;

    private array $lines;

    private array $files;

    public function __construct(array $files)
    {
        $filenames = array_keys($files);
        $this->withLines = is_string($filenames[0]);
        $this->files = $this->withLines ? $filenames : $files;

        $this->lines = $this->withLines ? $files : [];
    }

    public function rootPath(string $path): self
    {
        $this->rootPath = $path;

        return $this;
    }

    public function filesToString(): string
    {
        return implode(
            PHP_EOL,
            array_map(
                function (string $file) {
                    return $this->rootPath . '/' . $file;
                },
                $this->files,
            ),
        );
    }

    public function lines(string $file)
    {
        return $this->lines[$file] ?? $this->lines[str_replace($this->rootPath . '/', '', $file)];
    }

    public function withLines(): bool
    {
        return $this->withLines;
    }
}
