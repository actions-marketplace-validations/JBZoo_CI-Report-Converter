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

namespace JBZoo\CIReportConverter\Formats\GitLabJson;

use function JBZoo\Data\json;

final class GitLabJson
{
    /** @var GitLabJsonCase[] */
    private array $testCases = [];

    public function __toString(): string
    {
        $result = [];

        foreach ($this->testCases as $testCase) {
            $result[] = $testCase->toArray();
        }

        return (string)json($result);
    }

    public function addCase(?string $name = null): GitLabJsonCase
    {
        $testSuite         = new GitLabJsonCase($name);
        $this->testCases[] = $testSuite;

        return $testSuite;
    }
}
