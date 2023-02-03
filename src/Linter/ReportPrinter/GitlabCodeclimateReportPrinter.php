<?php declare(strict_types=1);

namespace Helmich\TypoScriptLint\Linter\ReportPrinter;

use Helmich\TypoScriptLint\Linter\Report\Issue;
use Helmich\TypoScriptLint\Linter\Report\Report;
use Symfony\Component\Console\Output\OutputInterface;

use function json_encode;
use function sha1;

class GitlabCodeclimateReportPrinter implements Printer
{
    private OutputInterface $output;

    private static array $severityMap = [
        Issue::SEVERITY_INFO => 'info',
        Issue::SEVERITY_WARNING => 'minor',
        Issue::SEVERITY_ERROR => 'major',
    ];

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function writeReport(Report $report): void
    {
        $issues = [];
        foreach ($report->getFiles() as $file) {
            foreach ($file->getIssues() as $issue) {
                $issues[] = [
                    'categories'  => ['Bug Risk'],
                    'check_name' => $issue->getSource(),
                    'description' => $issue->getMessage(),
                    'fingerprint' => sha1("{$file->getFilename()}:{$issue->getLine()}:{$issue->getColumn()}"),
                    'location' => [
                        'path' => $file->getFilename(),
                        'lines' => ['begin' => $issue->getLine()]
                    ],
                    'severity' => self::$severityMap[$issue->getSeverity()] ?? self::$severityMap['major'],
                    'type' => 'issue'
                ];
            }
        }
        $this->output->write(json_encode($issues));
    }
}
