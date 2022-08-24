<?php

namespace Shaggyrec\CodeStyleChecker;

use Shaggyrec\CodeStyleChecker\Exception\GitDiff;

/**
 * @return string
 */
function getRootPath(): string
{
    exec('git rev-parse --show-toplevel', $r, $resultCode);

    return $resultCode ? getcwd() : $r[0];
}

/**
 * @param string $branch
 * @throws GitDiff
 * @return string
 */
function gitDiff(string $branch): string
{
    $d = shell_exec('git diff --merge-base ' . $branch . ' -U0');

    if ($d === null) {
        throw new GitDiff('Can not get git diff');
    }

    return $d;
}

/**
 * @param string $scriptName
 * @return string
 */
function helpMessage(string $scriptName): string
{
    return <<<TEXT
PHP Code-style checker
Checks PHP code style according to a standard.

Example: $scriptName --src=src --standard=PSR12

Options:

Mandatory: One and only one of --src or --diff must be specified.

--src                 Path to a file or a directory with files to check
--diff                Branch for git diff with merge-base

Optional:
--standard            Standard to check (PSR12, PSR2, Squiz, etc.) OR path to a xml-file with a standard
--log-to              Path to a file to log to
--debug               Show internal names of standards rules


TEXT;

}

/**
 * @param string $string
 * @return string
 */
function removeTerminalCodes(string $string): string
{
    return preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $string);
}

/**
 * @param string $standard
 * @param array $files
 * @param bool $debug
 * @return bool|string
 */
function phpcs(string $standard, array $files, bool $debug): bool|string
{
    ini_set('output_buffering', 4096);
    ob_start();
    $_SERVER['argv'] = [
        '', // first argument is a script name
        '--standard=' . $standard,
        '-n' . (
            $debug
                ? 's'
                : ''
        ),
        '--colors',
        ...$files,
    ];
    include __DIR__ . '/../vendor/bin/phpcs';

    return ob_get_clean();
}
