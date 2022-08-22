<?php

namespace Shaggyrec\CodeStyleChecker\Tests\unit;

use Shaggyrec\CodeStyleChecker\GitDiffParser;
use PHPUnit\Framework\TestCase;

class GitDiffParserTest extends TestCase
{
    public function testParsesGitDiff()
    {
        $diff = file_get_contents(__DIR__ . '/data/diff.txt');

        $p = (new GitDiffParser())->parse($diff);

        $this->assertSame(
            'application/api/controllers/PortmoneController.php',
            $p->filesToString(),
        );

        $this->assertSame(
            [136, 137, 138, 139, 140, 141, 142, 154, 155, 156],
            $p->lines('application/api/controllers/PortmoneController.php')
        );
    }
}
