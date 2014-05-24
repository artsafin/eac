<?php

namespace Eprst\Eac\Command;

use Eprst\Eac\Service\Extractor\ScriptTagExtractor;
use Eprst\Eac\Service\Path;
use Eprst\Eac\Service\ScriptTagCompiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ShowSourcesCommand extends Command
{
    const ARG_SOURCE = 'source';

    const ARG_WEBROOT = 'webroot';

    protected function configure()
    {
        $this
            ->setName('sources')
            ->setDescription('Show sources that will be covered by compiler')
            ->addArgument(
                self::ARG_WEBROOT,
                InputArgument::REQUIRED,
                'Relative path'
            )
            ->addArgument(
                self::ARG_SOURCE,
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'HTML file pattern to search sources in'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument(self::ARG_SOURCE);
        $webroot = $input->getArgument(self::ARG_WEBROOT);

        $sourceFiles = $this->expandPaths(getcwd(), $source);

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

    private function expandPaths($sourceFilesRoot, $paths)
    {
        // Not all shells expand path wildcards
        $result = array_reduce($paths, function(&$acc, $item){
            if (strpos($item, '*') !== false || strpos($item, '?') !== false) {
                $acc = array_merge($acc, glob($item, GLOB_BRACE | GLOB_NOCHECK));
            } else {
                $acc[] = $item;
            }
            return $acc;
        }, array());

        $result = array_map(function($item) use($sourceFilesRoot){
            $path = new Path($item);
            return $path->prepend($sourceFilesRoot);
        }, $result);

        return $result;
    }
}