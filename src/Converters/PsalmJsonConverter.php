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

namespace JBZoo\CIReportConverter\Converters;

use JBZoo\CIReportConverter\Formats\Source\SourceCaseOutput;
use JBZoo\CIReportConverter\Formats\Source\SourceSuite;
use JBZoo\CIReportConverter\Helper;
use JBZoo\Data\Data;

use function JBZoo\Data\data;
use function JBZoo\Data\json;

final class PsalmJsonConverter extends AbstractConverter
{
    public const TYPE = 'psalm-json';
    public const NAME = 'Psalm.json';

    public function toInternal(string $source): SourceSuite
    {
        $sourceSuite = new SourceSuite(
            $this->rootSuiteName !== '' && $this->rootSuiteName !== null ? $this->rootSuiteName : 'Psalm',
        );

        $sourceCases = json($source)->getArrayCopy();

        foreach ($sourceCases as $sourceCase) {
            $sourceCase = data($sourceCase);

            /** @var string $fileName */
            $fileName = $sourceCase['file_name'] ?? 'Undefined';
            $fileLine = $sourceCase['line_from'] ?? null;

            $suite       = $sourceSuite->addSuite($fileName);
            $suite->file = $sourceCase['file_path'];

            $case = $suite->addTestCase($fileLine > 0 ? "{$fileName} line {$sourceCase['line_from']}" : $fileName);

            $case->file      = $sourceCase['file_path'];
            $case->line      = $sourceCase['line_from'];
            $case->class     = $sourceCase['type'];
            $case->classname = $sourceCase['type'];

            $caseOutput = new SourceCaseOutput(
                $sourceCase['type'],
                $sourceCase['message'],
                self::getDetails($sourceCase),
            );

            if ($sourceCase['error_level'] <= 0) {
                $case->warning = $caseOutput;
            } else {
                $case->failure = $caseOutput;
            }
        }

        return $sourceSuite;
    }

    private static function getDetails(Data $data): ?string
    {
        $snippet = \trim((string)$data->get('snippet'));

        return Helper::descAsList([
            ''            => \htmlspecialchars_decode((string)$data->get('message')),
            'Rule'        => $data->get('type'),
            'File Path'   => self::getFilePoint($data->get('file_path'), $data->get('line_from')),
            'Snippet'     => $snippet !== '' ? "`{$snippet}`" : null,
            'Taint Trace' => $data->get('taint_trace'),
            'Docs'        => $data->get('link'),
            'Severity'    => $data->get('severity'),
            'Error Level' => $data->get('error_level'),
        ]);
    }
}
