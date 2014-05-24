<?php


namespace Eprst\Eac\Command\Helper;


use Eprst\Eac\Service\Path;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class CommonInputDefinitionHelper implements HelperInterface
{
    const ARG_SOURCE = 'source';

    const ARG_WEBROOT = 'webroot';

    /**
     * @var HelperSet
     */
    private $helperSet;

    /**
     * Sets the helper set associated with this helper.
     *
     * @param HelperSet $helperSet A HelperSet instance
     *
     * @api
     */
    public function setHelperSet(HelperSet $helperSet = null)
    {
        $this->helperSet = $helperSet;
    }

    /**
     * Gets the helper set associated with this helper.
     *
     * @return HelperSet A HelperSet instance
     *
     * @api
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'cmd_args';
    }

    public function addArguments(Command $command)
    {
        $command
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

    /**
     * @param InputInterface $input
     *
     * @return array
     */
    public function getSources(InputInterface $input)
    {
        $source  = $input->getArgument(self::ARG_SOURCE);

        $sourceFiles = $this->expandPaths(getcwd(), $source);

        return $sourceFiles;
    }

    private function expandPaths($sourceFilesRoot, $paths)
    {
        // Not all shells expand path wildcards
        $result = array_reduce($paths,
        function (&$acc, $item) {
            if (strpos($item, '*') !== false || strpos($item, '?') !== false) {
                $acc = array_merge($acc, glob($item, GLOB_BRACE | GLOB_NOCHECK));
            } else {
                $acc[] = $item;
            }

            return $acc;
        }, array());

        $result = array_map(function ($item) use ($sourceFilesRoot) {
            $path = new Path($item);

            return $path->prepend($sourceFilesRoot);
        }, $result);

        return $result;
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    public function getWebroot(InputInterface $input)
    {
        $webroot = $input->getArgument(self::ARG_WEBROOT);

        return $webroot;
    }
}