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

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\BufferedOutput;

final class PlainTable
{
    /** @var array[] */
    private array          $rows = [];
    private BufferedOutput $buffer;
    private Table          $table;
    private string         $testCaseName;

    public function __construct(string $testCaseName)
    {
        $this->buffer = new BufferedOutput();
        $this->buffer->getFormatter()->setDecorated(false);

        $this->table = new Table($this->buffer);
        $this->table->setHeaders(['Line:Column', 'Severity', 'Message']);
        $this->table->setColumnWidth(2, 80);
        $this->table->setColumnMaxWidth(2, 80);

        $this->testCaseName = $testCaseName;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function appendRow(array $row): self
    {
        $this->rows[] = $row;

        return $this;
    }

    public function render(): string
    {
        $rowsCount = \count($this->rows);

        foreach ($this->rows as $index => $row) {
            $this->table->addRow($row);

            if ($rowsCount > (int)$index + 1) {
                $this->table->addRow(new TableSeparator());
            }
        }

        if ($this->testCaseName !== '') {
            $this->table
                ->setHeaderTitle($this->testCaseName)
                ->setFooterTitle($this->testCaseName);

            $this->buffer->writeln("## Test Case: {$this->testCaseName}");
        }

        $this->table->render();

        return $this->buffer->fetch();
    }
}
