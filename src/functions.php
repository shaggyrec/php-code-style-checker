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
