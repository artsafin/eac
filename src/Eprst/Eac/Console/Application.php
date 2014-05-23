<?php

namespace Eprst\Eac\Console;

use Eprst\Eac\Command\ShowSourcesCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    const VERSION = '1.0.0';

    public function __construct()
    {
        parent::__construct('EAC', self::VERSION);
    }

    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), array(
            new ShowSourcesCommand()
        ));
    }
}
