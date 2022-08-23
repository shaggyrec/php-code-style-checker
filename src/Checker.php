<?php

namespace Shaggyrec\CodeStyleChecker;

use Shaggyrec\CodeStyleChecker\Exception\CodeStyle;

class Checker
{
    private ?string $standard;

    public function __construct(?string $standard)
    {
        $this->standard = $standard;
    }

    public function check(CheckingFiles $files, bool $debug = false): void
    {
        $result = $this->runPhpCS($files, $debug);
        if ($files->withLines()) {
           $result = self::filterRows($result, $files);
        }

        if ($result) {
            throw new CodeStyle($result);
        }
    }

    private function runPhpCS(CheckingFiles $files, bool $debug = false): bool|string|null
    {
        return shell_exec(
            sprintf(
                '%s/../vendor/bin/phpcs %s --standard=%s -n%s',
                __DIR__,
                escapeshellarg($files->filesToString()),
                $this->standard,
                $debug ? 's' : '',
            ),
        );
    }

    private static function filterRows(string $phpCsResult, CheckingFiles $files): string
    {
        $res = '';
        $arrayErrorsLines = explode(PHP_EOL, $phpCsResult);

        $currentFile = null;
        foreach ($arrayErrorsLines as $line) {
            if (str_starts_with($line, 'FILE: ')) {
                $currentFile = str_replace('FILE: ', '', $line);
                continue;
            }

            if (
                $currentFile
                && preg_match('/^(\d+) \| /', trim($line), $matches)
                && in_array((int)$matches[1], $files->lines($currentFile))
            ) {
                $res .= $line . PHP_EOL;
            }
        }

        return $res;
    }
}
