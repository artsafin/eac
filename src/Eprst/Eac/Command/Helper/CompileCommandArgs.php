<?php

namespace Eprst\Eac\Command\Helper;
use Eprst\Eac\Service\Path;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * CompileCommandArgs
 */
class CompileCommandArgs implements CommandArguments
{
    const OPTION_COMPILE_DIR = 'out';
    const OPTION_COMPILE_DIR_DEFAULT = '<webroot>/assets-min';

    const OPTION_PREFIX = 'prefix';
    const OPTION_PREFIX_DEFAULT = 'smart choice';

    const OPTION_WRITE_REPLACE = 'replace';

    const OPTION_DIR_MODE = 'dirmode';

    public function addArguments(Command $command)
    {
        $command->addOption(self::OPTION_COMPILE_DIR,
                            null,
                            InputOption::VALUE_REQUIRED,
                            'Directory to put compiled files in',
                            self::OPTION_COMPILE_DIR_DEFAULT)
                ->addOption(self::OPTION_PREFIX,
                            null,
                            InputOption::VALUE_REQUIRED,
                            'Web server prefix to put in src="" attribute for compiled assets',
                            self::OPTION_PREFIX_DEFAULT)
                ->addOption(self::OPTION_WRITE_REPLACE,
                            null,
                            InputOption::VALUE_NONE,
                            'Put modified content to source file instead of .eac file')
                ->addOption(self::OPTION_DIR_MODE,
                            null,
                            InputOption::VALUE_REQUIRED,
                            'Permission mode to create compile directory with',
                            '0750');
    }

    public function getCompileDir(InputInterface $input, $webroot, $cwd)
    {
        $compileDir = $input->getOption(self::OPTION_COMPILE_DIR);
        $compileDir = str_replace('<webroot>', $webroot, $compileDir);

        if (!is_dir($compileDir)) {
            $mode = $input->getOption(self::OPTION_DIR_MODE);
            mkdir($compileDir, octdec($mode), true);
        }

        if (!Path::isAbsolute($compileDir)) {
            $compileDir = Path::prepend($compileDir, $cwd);
        }

        return $compileDir;
    }

    /**
     * getPrefix
     *
     * @param InputInterface $input
     * @param string         $compileDir
     * @param string         $webroot
     *
     * @throws RuntimeException
     * @return string
     */
    public function getPrefix(InputInterface $input, $compileDir, $webroot)
    {
        $prefix = $input->getOption(self::OPTION_PREFIX);
        if ($prefix == self::OPTION_PREFIX_DEFAULT) {
            $compileDir = realpath($compileDir);
            $webroot    = realpath($webroot);

            if (strpos($webroot, $compileDir) === 0) {
                return str_replace($webroot, '', $compileDir);
            } else {
                throw new RuntimeException("Compile dir is not under web root, though you must specify "
                                            . "--" . self::OPTION_PREFIX
                                            . " option.");
            }
        }

        return $prefix;
    }

    public function isReplace(InputInterface $input)
    {
        return $input->getOption(self::OPTION_WRITE_REPLACE);
    }
}
 