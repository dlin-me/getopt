<?php
/**
 *
 * User: davidlin
 * Date: 12/04/14
 * Time: 11:15 PM
 *
 */
namespace Dlin\Getopt\Tests;

use Dlin\Getopt\Getopt;
use Dlin\Getopt\OptionDefinition;

class GetoptTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Assert that two arrays are equal. This helper method will sort the two arrays before comparing them if
     * necessary. This only works for one-dimensional arrays, if you need multi-dimension support, you will
     * have to iterate through the dimensions yourself.
     * @param array $expected the expected array
     * @param array $actual the actual array
     * @param bool $regard_order whether or not array elements may appear in any order, default is false
     * @param bool $check_keys whether or not to check the keys in an associative array
     */
    protected function assertArraysEqual(array $expected, array $actual, $regard_order = false, $check_keys = true) {
        // check length first
        $this->assertEquals(count($expected), count($actual), 'Failed to assert that two arrays have the same length.');

        // sort arrays if order is irrelevant
        if (!$regard_order) {
            if ($check_keys) {
                $this->assertTrue(ksort($expected), 'Failed to sort array.');
                $this->assertTrue(ksort($actual), 'Failed to sort array.');
            } else {
                $this->assertTrue(sort($expected), 'Failed to sort array.');
                $this->assertTrue(sort($actual), 'Failed to sort array.');
            }
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the parse function
     */
    public function testParse()
    {
        $args=array(
            'script_name.php',
            '-a',
            1,
            '-b',
            2,
            '--c',
            3,
            '--d',
            4,
            '--ee',
            6,
            '-ff',
            7,
            '-g',
            '-i',
            '8',
            'tow',
            'three'

        );

        $_SERVER['argv'] = $args;
        $go = new Getopt();
        $options = $go->parse()->getParsedOptions();

        $expected = array(
            '_'=>array('--c',3,'--d',4,'-ff',7, 'tow', 'three'),
            'a'=>1,
            'b'=>2,
            'ee'=>6,
            'g'=>1,
            'i'=>8

        );

        $this->assertArraysEqual($options, $expected);

    }


    /**
     * test options with definition
     */
    public function testDefinition(){

        $message = null;
        $go = new Getopt(null, function($msg)use(&$message){ $message = $msg;}, function(){});

        //test default
        $testOpt = array(
            'arg'=>'test1',
            'default'=>'this is my default'
        );
        $go->setOption($testOpt);
        $go->parse();
        $this->assertEquals($testOpt['default'], $go->test1);

        //test required
        $testOpt = array(
            'arg'=>'test2',
            'required'=>true,
            'requiredMsg'=>'hello required'
        );
        $go->clearOptions();
        $go->setOption($testOpt);
        $go->parse();
        $this->assertEquals($testOpt['requiredMsg'], $message);

        //test pattern
        $testOpt = array(
            'arg'=>'test3',
            'pattern'=>'/\d{2}/',
            'patternMsg'=>'Has to be two digit'
        );
        $go->clearOptions();
        $go->setOption($testOpt);
        $_SERVER['argv'] = array(
            'script_name.php',
            '--test3',
            'ok'
        );
        $go->parse();
        $this->assertEquals($testOpt['patternMsg'], $message);

        $message = null;
        $_SERVER['argv'] = array(
            'script_name.php',
            '--test3',
            '21'
        );
        $go->parse();
        $this->assertNull($message);


    }


    /**
     * Test Prompt and capturing input
     */
    public function testPrompt(){
        $message = null;
        $input = 'hello';
        $go = new Getopt(null, function($msg)use(&$message){ $message = $msg;}, function(){}, function()use(&$input){return $input;});
        //test default
        $testOpt = array(
            'arg'=>'test1',
            'prompt'=>true,

            'promptMsg'=>'please enter'
        );

        $go->setOption($testOpt);
        $go->parse();
        $this->assertEquals($testOpt['promptMsg'], $message);
        $this->assertEquals($input, $go->test1);

        $input = 'ok';
        $go->parse();
        $this->assertEquals($input, $go->test1);

        $input = ''; //empty input
        $go->parse();
        $this->assertNull($go->test1);


    }

    /**
     * Test alias
     */
    public function testAlias(){
        $message = null;

        $go = new Getopt(null, function($msg)use(&$message){ $message = $msg;}, function(){});
        //test set
        $testOpt = array(
            'arg'=>'t',
            'alias'=>'test'
        );

        $_SERVER['argv'] = array(
            'script_name.php',
            '-t',
            '21'
        );

        $go->setOption($testOpt);
        $go->parse();
        $this->assertEquals('21', $go->test);
        $this->assertEquals('21', $go->t);




        $_SERVER['argv'] = array(
            'script_name.php',
            '--test',
            '22'
        );

        $go->parse();
        $this->assertEquals('22', $go->test);
        $this->assertEquals('22', $go->t);




    }


    public function testUsage(){

        $message = null;
        $_SERVER['argv'] = array(
            'script_name.php',
            '--test',
            '22'
        );

        $go = new Getopt(null, function($msg)use(&$message){ $message = $msg;}, function(){});

        $this->assertEquals('Usage: php script_name.php', $go->getUsage());
        $go->setUsage('Usage: please use it like this $0 -x [num] -y [num]');

        $this->assertEquals('please use it like this script_name.php -x [num] -y [num]', $go->getUsage());
    }

    /**
     * Test showing help message
     */
    public function testHelp(){
        $message = null;

        $go = new Getopt(null, function($msg)use(&$message){ $message = $msg;}, function(){});
        //test default
        $testOpt = array(
            'arg'=>'test1',


            'help'=>'this is a test parameter',
            'promptMsg'=>'please enter'
        );
        $_SERVER['argv'] = array(
            'script_name.php',
            '-h'
        );


        $go->setOption($testOpt);

        $expect = "Usage: php script_name.php\nOptions:\n\t--test1\tthis is a test parameter";

        $this->assertEquals($expect, trim($go->getHelpMessage()));

        $go->parse();

        $this->assertEquals($expect, trim($message));



    }


}
