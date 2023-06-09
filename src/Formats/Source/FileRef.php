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

namespace JBZoo\CIReportConverter\Formats\Source;

use JBZoo\CIReportConverter\Formats\AbstractNode;

final class FileRef extends AbstractNode
{
    public string  $name;
    public ?string $fullpath = null;
    public ?int    $line     = null;
    public ?int    $column   = null;

    public function getFullName(): string
    {
        $result = (string)$this->fullpath;
        if ($this->line > 0) {
            $result .= ":{$this->line}";
        }

        return $result;
    }

    public function getShortName(): string
    {
        $result = $this->name;
        if ($this->line > 0) {
            $result .= " on line {$this->line}";
        }

        return $result;
    }
}
