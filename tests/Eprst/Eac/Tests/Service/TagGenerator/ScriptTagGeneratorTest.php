<?php


namespace Eprst\Eac\Tests\Service\TagGenerator;


use Eprst\Eac\Service\TagGenerator\ScriptTagGenerator;

class ScriptTagGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $obj = new ScriptTagGenerator();

        $result = $obj->generate('arbitrary input file which is not \'checked\' in any way');

        self::assertEquals('<script type=\'text/javascript\' src=\'arbitrary input file which is not \'checked\' in any way\'></script>', $result);
    }
}
 