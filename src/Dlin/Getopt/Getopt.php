<?php
/**
 *
 * User: davidlin
 * Date: 11/04/14
 * Time: 11:40 PM
 *
 */

namespace Dlin\Getopt;


/**
 * Class Getopt
 *
 * This class handles the command line argument parsing and specification
 *
 * @package Dlin\Getopt
 *
 */
class Getopt
{

    /**
     * Array of argument definitions
     */
    protected  $argDefinitions;

    /**
     * Constructor
     *
     * @param $options array of configuration
     */
    public function __construct($options)
    {
        foreach($options as $option){
            $this->setOptions($option);
        }

    }



    public function setOptions($options){
        $args = array('arg', 'alias', 'required', 'default', 'help');
        $args = array_map(function($key) use ($options) {return array_key_exists($key, $options) ? $options[$key] : null; }, $args);
        call_user_func_array(array($this, "option"), $args);
    }

    public function option($key, $alias, $required, $default, $help)
    {
        $option = array();
        $option['arg'] = $key;
        $option['alias'] = $alias;
        $option['required'] = $required;
        $option['default'] = $default;
        $option['help'] = $help;
        $this->argDefinitions = $option;
    }



}
