<?php

namespace Eprst\Eac\Service\Compiler;
use Symfony\Component\Process\Process;

/**
 * ExternalCommandCompiler
 */
class ExternalCommandCompiler implements AssetCompiler
{
    /**
     * command
     *
     * @var
     */
    private $command;

    /**
     * argumentsReducer
     *
     * @var null
     */
    private $filesReducer;

    /**
     * timeout
     *
     * @var
     */
    private $timeout;

    public function __construct($command, $timeout, $filesReducer = null)
    {
        $this->command = $command;
        $this->filesReducer = $filesReducer;

        if (!$this->filesReducer || !is_callable($this->filesReducer)) {
            $this->filesReducer = function(&$acc, $item){
                return ($acc ? " {$acc}" : '') . $item;
            };
        }
        $this->timeout = $timeout;
    }

    /**
     * Compile multiple files into one
     *
     * @param array $files Files to be compiled into single file
     *
     * @return string Full path to a single compiled file
     */
    public function compile($files)
    {
        $arguments = array_reduce($files, $this->filesReducer);

        $cmdLine = strtr($this->command, array(
            '%f' => $arguments
        ));

        $proc = new Process($cmdLine, null, null, null, $this->timeout);
        $proc->mustRun();
    }
}
 