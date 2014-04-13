<?php
/**
 *
 * User: davidlin
 * Date: 12/04/14
 * Time: 11:15 PM
 *
 */
namespace Dlin\Getopt\Tests;

use Dlin\Getopt\OptionDefinition;

class OptionDefinitionTest extends \PHPUnit_Framework_TestCase
{



    /**
     * Test the parse function
     */
    public function testMessages()
    {
        $option1 = array('arg'=>'test', 'pattern'=>'/pattern/', 'help'=>'test parameter');
        $option2 = array('arg'=>'test', 'promptMsg'=>'this is prompt', 'patternMsg'=>'this is pattern msg', 'requiredMsg'=>'this is require msg');

        $def = new OptionDefinition($option1);

        $this->assertEquals( 'Option -test must match pattern: /pattern/', $def->getPatternMsg());

        $this->assertEquals('Option -test is required.', $def->getRequiredMsg());

        $this->assertEquals('Please enter: test parameter', $def->getPromptMessage());


        $def = new OptionDefinition($option2);

        $this->assertEquals($option2['patternMsg'], $def->getPatternMsg());

        $this->assertEquals($option2['requiredMsg'], $def->getRequiredMsg());

        $this->assertEquals($option2['promptMsg'], $def->getPromptMessage());





    }
}