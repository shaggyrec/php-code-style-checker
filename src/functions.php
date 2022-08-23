<?php

namespace Shaggyrec\CodeStyleChecker;

/**
 * @return string
 */
function getRootPath(): string
{
    exec('git rev-parse --show-toplevel', $r, $resultCode);

    return $resultCode ? getcwd() : $r[0];
}

/**
 * @return string
 */
function gitDiff(): string
{
    return shell_exec('git diff --merge-base develop -U0');
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
--diff                Check git diff of current brunch and merge-base

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
