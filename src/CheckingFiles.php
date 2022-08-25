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
            ' ',
            array_map(
                function (string $file) {
                    return $this->rootPath . $file;
                },
                $this->files,
            ),
        );
    }

    /**
     * @return array
     */
    public function files(): array
    {
        return array_map(
            function (string $file) {
                return $this->rootPath . $file;
            },
            $this->files,
        );
    }

    /**
     * @param string $file
     * @return array
     */
    public function lines(string $file): array
    {
        return $this->lines[$file]
            ?? $this->lines[str_replace($this->rootPath, '', $file)]
            ?? $this->lines[
                array_values(
                    array_filter(
                        $this->files,
                        function (string $filename) use ($file) {
                            return str_ends_with($file, $filename);
                        },
                    )
                )[0]
            ] ?? [];
    }

    /**
     * @return bool
     */
    public function withLines(): bool
    {
        return $this->withLines;
    }
}
