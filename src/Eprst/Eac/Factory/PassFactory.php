<?php

namespace Eprst\Eac\Factory;
use Eprst\Eac\Command\Helper\CommandArguments;
use Eprst\Eac\Command\Helper\ExternalCommandArguments;
use Eprst\Eac\Service\Compiler\ExternalCommandCompiler;

/**
 * PassFactory
 */
class PassFactory
{
    /**
     * createByAlias
     *
     * @param array|string $aliases
     * @param array $params
     *
     * @return PassInterface[]
     */
    public static function createByAlias($aliases, $params)
    {
        $aliases = (array) $aliases;

        /** @var PassInterface[] $modes */
        $modes = array();
        foreach ($aliases as $pass) {
            switch ($pass) {
                case 'js':
                    $modes[] = self::createJsExternalPass();
                    break;
                default:
                    throw new \RuntimeException("Unsupported pass {$pass}");
            }
        }

        return $modes;
    }

    private static function

    private static function createJsExternalPass()
    {
        $compiler = new ExternalCommandCompiler();

        return new JsPass($compiler);
    }

    /**
     * getPassArguments
     *
     * @return CommandArguments[]
     */
    public static function getPassArguments()
    {
        return array(
            new ExternalCommandArguments()
        );
    }
}
 