<?php

namespace Eprst\Eac\Command;

use Eprst\Eac\Command\Helper\CommonArgs;
use Eprst\Eac\Factory\JsPass;
use Eprst\Eac\Factory\PassFactory;
use Eprst\Eac\Factory\PassInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowSourcesCommand extends Command
{
    /**
     * @var CommonArgs
     */
    private $commonArgs;

    protected function configure()
    {
        $this->commonArgs = new CommonArgs();

        $this
            ->setName('eac:sources')
            ->setDescription('Show sources that will be covered by compiler')
           ;

        $this->commonArgs->addArguments($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceFiles = $this->commonArgs->getSources($input);
        $webroot     = $this->commonArgs->getWebroot($input);
        $modeAliases = $this->commonArgs->getModes($input);

        $output->writeln("Processing sources:\n\t<info>". implode("</info>\n\t<info>", $sourceFiles) . "</info>");

        $modes = PassFactory::createByAlias($modeAliases, array('webroot' => $webroot));

        if (empty($modes)) {
            throw new \RuntimeException("You must specify at least one mode.");
        }

        $output->writeln('');

        foreach ($modes as $mode) {
            $resolver = $mode->getAssetResolver();
            $chunks = $resolver->resolve($sourceFiles, $webroot);

            foreach ($chunks as $chunk) {
                $output->writeln("Chunk {$chunk->getName()}:");

                foreach ($chunk->assets as $f) {
                    $output->writeln("\t<info>{$f}</info>");
                }
            }
        }
    }
}