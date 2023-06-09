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

namespace JBZoo\CIReportConverter\Formats\PlainText;

final class PlainText
{
    public const DEFAULT_NAME = 'Undefined Suite Name';

    /** @var PlainTextCase[] */
    private array $testCases = [];

    /** @var PlainTextSuite[] */
    private array $testSuites = [];

    public function __toString(): string
    {
        $tables = [];

        foreach ($this->testCases as $testCase) {
            if (!isset($tables[$testCase->name])) {
                $tables[$testCase->name] = new PlainTable(
                    $testCase->name !== '' ? $testCase->name : self::DEFAULT_NAME,
                );
            }

            $tables[$testCase->name]->appendRow([
                ($testCase->line ?? 1) . ($testCase->column > 0 ? ":{$testCase->column}" : ''),
                $testCase->level,
                $testCase->message,
            ]);
        }

        $result = \array_reduce($tables, static function (array $acc, PlainTable $table): array {
            $acc[] = $table->render();

            return $acc;
        }, []);

        foreach ($this->testSuites as $testSuite) {
            $result[] = (string)$testSuite;
        }

        return \trim(\implode("\n", $result)) . "\n";
    }

    public function addCase(?string $name = null): PlainTextCase
    {
        $testSuite         = new PlainTextCase($name);
        $this->testCases[] = $testSuite;

        return $testSuite;
    }

    public function addSuite(?string $name = null): PlainTextSuite
    {
        $testSuite          = new PlainTextSuite($name ?? self::DEFAULT_NAME);
        $this->testSuites[] = $testSuite;

        return $testSuite;
    }
}
