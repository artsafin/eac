<?php

namespace Eprst\Eac\Command\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * CommandArguments
 */
interface CommandArguments
{
    public function addArguments(Command $command);

    public function getArguments(InputInterface $input);
}
 