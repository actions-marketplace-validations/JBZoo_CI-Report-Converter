<?php

/**
 * JBZoo Toolbox - CI-Report-Converter.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/CI-Report-Converter
 */

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\CIReportConverter\Converters\CheckStyleConverter;
use JBZoo\CIReportConverter\Converters\GithubCliConverter;
use JBZoo\CIReportConverter\Converters\JUnitConverter;
use JBZoo\CIReportConverter\Formats\GithubActions\GithubActions;
use JBZoo\CIReportConverter\Formats\GithubActions\GithubCase;

final class ConverterGithubTest extends PHPUnit
{
    public function testJUnitSimple(): void
    {
        $pathPrefix = '/Users/smetdenis/Work/projects/jbzoo-ci-report-converter';

        $sourceReport = (new JUnitConverter())
            ->setRootPath($pathPrefix)
            ->toInternal(\file_get_contents(Fixtures::PHPUNIT_JUNIT_SIMPLE));
        $targetReport = (new GithubCliConverter())
            ->setRootPath($pathPrefix)
            ->fromInternal($sourceReport);

        isSame(Fixtures::getExpectedFileContent('txt'), $targetReport);
    }

    public function testJUnitNested(): void
    {
        $pathPrefix = '/Users/smetdenis/Work/projects/jbzoo-ci-report-converter';

        $sourceReport = (new JUnitConverter())
            ->setRootPath($pathPrefix)
            ->toInternal(\file_get_contents(Fixtures::PHPUNIT_JUNIT_NESTED));
        $targetReport = (new GithubCliConverter())
            ->setRootPath($pathPrefix)
            ->fromInternal($sourceReport);

        isSame(Fixtures::getExpectedFileContent('txt'), $targetReport);
    }

    public function testCodeStyle(): void
    {
        $pathPrefix = '/Users/smetdenis/Work/projects/jbzoo-ci-report-converter';

        $sourceReport = (new CheckStyleConverter())
            ->setRootPath($pathPrefix)
            ->toInternal(\file_get_contents(Fixtures::PHPCS_CODESTYLE));
        $targetReport = (new GithubCliConverter())
            ->setRootPath($pathPrefix)
            ->setRootSuiteName('Tests')
            ->fromInternal($sourceReport);

        isSame(Fixtures::getExpectedFileContent('txt'), $targetReport);
    }

    public function testGithubActionsPrinter(): void
    {
        $ghActions      = new GithubActions();
        $case0          = $ghActions->addCase('src/Root.php');
        $case0->line    = 789;
        $case0->column  = null;
        $case0->message = 'Something went wrong #0';

        $suite1         = $ghActions->addSuite('src/File.php');
        $case1          = $suite1->addCase('src/Class.php');
        $case1->line    = 123;
        $case1->column  = 4;
        $case1->message = 'Something went wrong #1';

        $suite2         = $ghActions->addSuite();
        $case2          = $suite2->addCase('src/AnotherFile.php');
        $case2->line    = 456;
        $case2->column  = 0;
        $case2->level   = GithubCase::LEVEL_WARNING;
        $case2->message = "Something\nwent\nwrong\n\n#2\n";

        $case3          = $suite2->addCase('src/SomeFiles.php');
        $case3->level   = GithubCase::LEVEL_DEBUG;
        $case3->message = \implode("\n", [
            'Failed asserting that two arrays are identical.',
            '--- Expected',
            '+++ Actual',
            '@@ @@',
            ' Array &0 (',
            "-    0 => 'asd'",
            '+    0 => 123',
            '+    1 => 123123',
            ' )',
        ]);

        $suite2->addCase();

        isSame(Fixtures::getExpectedFileContent('txt'), (string)$ghActions);
    }
}
