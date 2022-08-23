<?php

namespace unit;

use Shaggyrec\CodeStyleChecker\CliOptions;
use PHPUnit\Framework\TestCase;
use Shaggyrec\CodeStyleChecker\Exception\ConflictOptions;
use Shaggyrec\CodeStyleChecker\Exception\MandatoryOption;

class CliOptionsTest extends TestCase
{
    public function testHasOption(): void
    {
        $options = [
            'help' => true,
            'src' => 'src',
            'diff' => true,
            'verbose' => true,
            'log' => 'log',
            'standard' => 'standard',
        ];
        $cliOptions = new CliOptions($options);
        $this->assertTrue($cliOptions->hasOption('help'));
        $this->assertTrue($cliOptions->hasOption('src'));
        $this->assertTrue($cliOptions->hasOption('diff'));
        $this->assertTrue($cliOptions->hasOption('verbose'));
        $this->assertTrue($cliOptions->hasOption('log'));
        $this->assertTrue($cliOptions->hasOption('standard'));
        $this->assertFalse($cliOptions->hasOption('unknown'));
    }

    public function testAssertMandatoryOption(): void
    {
        $this->expectException(MandatoryOption::class);
        (new CliOptions([]))->assertMandatoryOptions();
    }

    public function testThrowsOnConflictOption(): void
    {
        $this->expectException(ConflictOptions::class);
        (new CliOptions(['src' => 'src', 'diff' => true]))->assertMandatoryOptions();
    }
}
