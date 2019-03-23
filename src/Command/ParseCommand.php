<?php declare(strict_types=1);
namespace Helmich\TypoScriptLint\Command;

use Helmich\TypoScriptParser\Parser\ParserInterface;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseCommand extends Command
{

    /**
     * @var ParserInterface
     */
    private $parser;

    public function injectParser(ParserInterface $parser): void
    {
        $this->parser = $parser;
    }

    protected function configure(): void
    {
        $this
            ->setName('parse')
            ->setDescription('Parse TypoScript file into syntax tree.')
            ->addArgument('filename', InputArgument::REQUIRED, 'Input filename');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $filename = $input->getArgument('filename');

        $printer    = new PrettyPrinter();
        $statements = $this->parser->parseStream($filename);

        $printer->printStatements($statements, $output);
    }
}
