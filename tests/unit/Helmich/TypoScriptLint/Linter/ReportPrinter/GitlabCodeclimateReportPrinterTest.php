<?php declare(strict_types=1);

namespace Helmich\TypoScriptLint\Tests\Unit\Helmich\TypoScriptLint\Linter\ReportPrinter;

use Helmich\TypoScriptLint\Linter\Report\File;
use Helmich\TypoScriptLint\Linter\Report\Issue;
use Helmich\TypoScriptLint\Linter\Report\Report;
use Helmich\TypoScriptLint\Linter\ReportPrinter\GitlabCodeclimateReportPrinter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

use function PHPUnit\Framework\assertEquals;

class GitlabCodeclimateReportPrinterTest extends TestCase
{
    public const EXPECTED_JSON_OUTPUT = '[' .
        '{' .
            '"categories":["Bug Risk"],' .
            '"check_name":"foobar",' .
            '"description":"Message #1",' .
            '"fingerprint":"80f7f899da537944e9a13ba46ada2a9989688769",' .
            '"location":{"path":"foobar.tys","lines":{"begin":123}},' .
            '"severity":"info",' .
            '"type":"issue"' .
        '},' .
        '{"' .
            'categories":["Bug Risk"],' .
            '"check_name":"foobar",' .
            '"description":"Message #2",' .
            '"fingerprint":"8729cd78fd7994196b405017f89868f0a7339069",' .
            '"location":{"path":"foobar.tys","lines":{"begin":124}},' .
            '"severity":"minor",' .
            '"type":"issue"' .
        '},' .
        '{' .
            '"categories":["Bug Risk"],' .
            '"check_name":"barbaz",' .
            '"description":"Message #3",' .
            '"fingerprint":"1ffc6e000887adfe3a6a81403cd6d9dd618fb5f5",' .
            '"location":{"path":"bar.txt","lines":{"begin":412}},' .
            '"severity":"major",' .
            '"type":"issue"' .
        '}' .
    ']';

    private BufferedOutput $output;

    private GitlabCodeclimateReportPrinter $printer;

    public function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->printer = new GitlabCodeclimateReportPrinter($this->output);
    }

    public function testGitlabReportIsCorrectlyGenerated(): void
    {
        $file1 = new File('foobar.tys');
        $file1->addIssue(new Issue(123, 12, 'Message #1', Issue::SEVERITY_INFO, 'foobar'));
        $file1->addIssue(new Issue(124, 0, 'Message #2', Issue::SEVERITY_WARNING, 'foobar'));

        $file2 = new File('bar.txt');
        $file2->addIssue(new Issue(412, 141, 'Message #3', Issue::SEVERITY_ERROR, 'barbaz'));

        $report = new Report();
        $report->addFile($file1);
        $report->addFile($file2);

        $this->printer->writeReport($report);

        assertEquals(self::EXPECTED_JSON_OUTPUT, $this->output->fetch());
    }
}
