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

        if (trim($result)) {
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
        $result = phpcs($this->standard, $files->files(), $debug);

        if (!str_contains($result, self::RESULT_LINE_SUCCESS_DETECTION)) {
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
        $arrayErrorsLines = explode(PHP_EOL, $phpCsResult);

        $currentFile = null;
        $fileLinesMap = [];
        foreach ($arrayErrorsLines as $line) {
            $cleanLine = removeTerminalCodes($line);
            if (str_starts_with($cleanLine, self::RESULT_LINE_FILE_PREFIX)) {
                $currentFile = str_replace(self::RESULT_LINE_FILE_PREFIX, '', $cleanLine);
                $fileLinesMap[$currentFile][] = PHP_EOL . $line;
                continue;
            }

            if (
                $currentFile
                && preg_match('/^(\d+) \| /', trim($cleanLine), $matches)
                && in_array((int)$matches[1], $files->lines($currentFile))
            ) {
                $fileLinesMap[$currentFile][] = $line;
            }
        }

        return implode(
            PHP_EOL,
            array_merge(
                ...array_values(
                    array_filter(
                        $fileLinesMap,
                        function ($file) {
                            return count($file) > 1;
                        },
                    )
                )
            ),
        ) . PHP_EOL;
    }
}
