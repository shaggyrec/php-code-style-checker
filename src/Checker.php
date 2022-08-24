<?php

namespace Shaggyrec\CodeStyleChecker;

use Shaggyrec\CodeStyleChecker\Exception\CodeStyle;

class Checker
{
    private const RESULT_LINE_FILE_PREFIX = 'FILE: ';
    private const RESULT_LINE_SUCCESS_DETECTION = 'FOUND';

    private ?string $standard;

    public function __construct(?string $standard)
    {
        $this->standard = $standard;
    }

    /**
     * @param CheckingFiles $files
     * @param bool $debug
     * @throws CodeStyle
     * @throws Exception\Checker
     * @return void
     */
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

    /**
     * @param CheckingFiles $files
     * @param bool $debug
     * @throws Exception\Checker
     * @return bool|string|null
     */
    private function runPhpCS(CheckingFiles $files, bool $debug = false): bool|string|null
    {
        exec(
            sprintf(
                '%s/../vendor/bin/phpcs %s --standard=%s -n%s',
                pathinfo(__FILE__, PATHINFO_DIRNAME),
                $files->filesToString(),
                $this->standard,
                $debug ? 's' : '',
            ),
            $r,
            $code
        );

        $result = implode(PHP_EOL, $r);

        if (
            $code !== 0
            && !str_contains($result, self::RESULT_LINE_SUCCESS_DETECTION)
        ) {
            throw new \Shaggyrec\CodeStyleChecker\Exception\Checker($result);
        }

        return $result;
    }

    /**
     * @param string $phpCsResult
     * @param CheckingFiles $files
     * @return string
     */
    private static function filterRows(string $phpCsResult, CheckingFiles $files): string
    {
        $res = '';
        $arrayErrorsLines = explode(PHP_EOL, $phpCsResult);

        $currentFile = null;
        foreach ($arrayErrorsLines as $line) {
            $cleanLine = removeTerminalCodes($line);
            if (str_starts_with($cleanLine, self::RESULT_LINE_FILE_PREFIX)) {
                $currentFile = str_replace(self::RESULT_LINE_FILE_PREFIX, '', $cleanLine);
                $res .=  PHP_EOL . $line . PHP_EOL;
                continue;
            }

            if (
                $currentFile
                && preg_match('/^(\d+) \| /', trim($cleanLine), $matches)
                && in_array((int)$matches[1], $files->lines($currentFile))
            ) {
                $res .= $line . PHP_EOL;
            }
        }

        return $res;
    }
}
