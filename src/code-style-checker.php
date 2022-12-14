<?php

namespace Shaggyrec\CodeStyleChecker;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Shaggyrec\CodeStyleChecker\Exception\CodeStyle;
use Shaggyrec\CodeStyleChecker\Exception\MandatoryOption;
use Shaggyrec\CodeStyleChecker\Exception\NoGitDiff;

require __DIR__ . '/../vendor/autoload.php';

$options = CliOptions::fromEnv();
$logger = new Logger('code-style-checker');

$logger->pushHandler(
    new StreamHandler(
        'php://stderr',
        $options->isVerbose() ? Level::Debug : Level::Warning,
        !$options->isVerbose(),
    )
);

if(count($argv) === 1 || $options->isHelp()) {
    echo helpMessage(basename($argv[0]));
    exit(1);
}

try {
    $options->assertMandatoryOptions();
} catch (MandatoryOption $e) {
    $logger->critical($e->getMessage());
    exit(1);
}

if ($options->logTo()) {
    $logger->pushHandler(new StreamHandler($options->logTo()));
}

set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($logger) {
    if (error_reporting()) {
        $logger->critical($errstr . ' in ' . $errfile . ':' . $errline);
        exit(1);
    }
}, E_ALL);

register_shutdown_function(function () use ($logger) {
    if (null !== ($e = error_get_last())) {
        $logger->critical($e['message'] . ' in ' . $e['file'] . ': ' . $e['line']);
        exit(1);
    }
});

try {
    (new Checker($options->standard()))
        ->check(
            (
            $options->hasOption(CliOptions::OPTION_DIFF)
                ? (new GitDiffParser())->parse(gitDiff($options->diff()))
                : new CheckingFiles(explode(PHP_EOL, $options->src()))
            )->rootPath(getRootPath()),
            $options->isDebug(),
            $options->isColors(),
        );
    exit(0);
} catch (NoGitDiff $exception) {
    $logger->info($exception->getMessage());
    exit(0);
} catch (CodeStyle $exception) {
    echo $exception->getMessage();
    exit(1);
} catch (\Exception $exception) {
    $logger->critical($exception->getMessage());
    exit(1);
}
