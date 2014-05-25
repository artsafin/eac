<?php


namespace Eprst\Eac\Command\Helper;


use Eprst\Eac\Service\Path;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class CommonArgsHelper implements HelperInterface
{
    const ARG_SOURCE = 'source';

    const ARG_WEBROOT = 'webroot';

    const OPTION_DEPTH = 'depth';

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
            )
            ->addOption(self::OPTION_DEPTH,
                        strtolower(substr(self::OPTION_DEPTH, 0, 1)),
                        InputOption::VALUE_REQUIRED,
                        'Recurse through directories specified in ' . self::ARG_SOURCE,
                        1);
    }

    /**
     * @param InputInterface $input
     *
     * @return array
     */
    public function getSources(InputInterface $input)
    {
        $source  = $input->getArgument(self::ARG_SOURCE);
        $depth = max($input->getOption(self::OPTION_DEPTH), 0);

        $sourceFiles = $this->expandPaths($source, getcwd(), $depth);

        return $sourceFiles;
    }

    private function expandPaths($paths, $relativePathRoot = null, $depth = 0)
    {
        // Not all shells expand path wildcards
        $result = array_reduce($paths,
        function (&$acc, $item) {
            if (strpos($item, '*') !== false || strpos($item, '?') !== false) {
                $glob = glob($item, GLOB_BRACE);
                if ($glob !== false) {
                    $acc = array_merge($acc, $glob);
                }
            } else {
                $acc[] = $item;
            }

            return $acc;
        }, array());

        if ($relativePathRoot) {
            $result = array_map(function ($item) use ($relativePathRoot) {
                if (Path::isAbsolute($item)) {
                    return $item;
                } else {
                    $path = new Path($item);
                    return $path->prepend($relativePathRoot);
                }
            },
            $result);
        }

        if ($depth > 0) {
            $dirEntries = array_filter($result, 'is_dir');
            $globDirEntries = array_map(function ($item) {
                return $item . DIRECTORY_SEPARATOR . "*";
            }, $dirEntries);

            $recurseResult = $this->expandPaths($globDirEntries, null, $depth - 1);
            $result = array_diff($result, $dirEntries);
            $result = array_merge($result, $recurseResult);
        }

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