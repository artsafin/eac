<?php

namespace Eprst\Eac\Command;

use Eprst\Eac\Command\Helper\CommonInputDefinitionHelper;
use Eprst\Eac\Service\Extractor\ScriptTagExtractor;
use Eprst\Eac\Service\Path;
use Eprst\Eac\Service\ScriptTagCompiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CompileCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Compile assets of specified source files');

        $this->getHelper('cmd_args')->addArguments($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var CommonInputDefinitionHelper $eacArgs */
        $eacArgs = $this->getHelper('cmd_args');

        $sourceFiles = $eacArgs->getSources($input);
        $webroot     = $eacArgs->getWebroot($input);

        $output->writeln("<info>Processing sources:</info>\n\t". implode("\n\t", $sourceFiles));

        $compiler = new ScriptTagCompiler(new ScriptTagExtractor());
        $files = $compiler->getCompileFileNames($sourceFiles, $webroot);

        $output->writeln('');
        foreach ($files as $source => $sourceFiles) {
            $output->writeln("<info>Source {$source}:</info>");

            foreach ($sourceFiles as $f) {
                $output->writeln("\t{$f}");
            }
        }
    }
}