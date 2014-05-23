<?php

namespace Eprst\Eac\Command;

use Eprst\Eac\Service\ScriptTagCompiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ShowSourcesCommand extends Command
{
    const ARG_FILES = 'files';

    protected function configure()
    {
        $this
            ->setName('eac:sources')
            ->setDescription('Show sources that will be covered by compiler')
            ->addArgument(
                self::ARG_FILES,
                InputArgument::REQUIRED,
                'HTML file pattern to search sources in'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pattern = $input->getArgument(self::ARG_FILES);

        $output->writeln("Processing pattern {$pattern}");

        $compiler = new ScriptTagCompiler();
        $compiler->compile($pattern);
    }
}