<?php

namespace Eprst\Eac\Command;

use Eprst\Eac\Command\Helper\CommonArgsHelper;
use Eprst\Eac\Service\Extractor\SgmlCommentChunk;
use Eprst\Eac\Service\Extractor\XPathTagExtractor;
use Eprst\Eac\Service\SgmlTagAssetResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowSourcesCommand extends Command
{
    /**
     * @var CommonArgsHelper
     */
    private $argsHelper;

    protected function configure()
    {
        $this->argsHelper = new CommonArgsHelper();

        $this
            ->setName('eac:sources')
            ->setDescription('Show sources that will be covered by compiler')
           ;

        $this->argsHelper->addArguments($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceFiles = $this->argsHelper->getSources($input);
        $webroot = $this->argsHelper->getWebroot($input);

        $output->writeln("Processing sources:\n\t<info>". implode("</info>\n\t<info>", $sourceFiles) . "</info>");

        $resolver = new SgmlTagAssetResolver(new SgmlCommentChunk(), new XPathTagExtractor('//script'), 'src');
        $files = $resolver->resolveAssets($sourceFiles, $webroot);

        $output->writeln('');
        foreach ($files as $source => $sourceFiles) {
            $output->writeln("Chunk {$source}:");

            foreach ($sourceFiles as $f) {
                $output->writeln("\t<info>{$f}</info>");
            }
        }
    }
}