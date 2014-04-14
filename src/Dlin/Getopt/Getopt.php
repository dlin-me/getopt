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
    protected $optionDefinitions;

    /**
     * $usage message
     * the $0 will be replaced with the running script
     */
    protected $usage;

    /**
     * This is the parsed arguments
     * @var array
     */
    protected $parsedOptions;

    /**
     * function to report message, default is error
     * @var null
     */
    protected $reportFunction;

    /**
     * function to terminate
     * @var
     */
    protected $exitFunction;

    /**
     * function to get user input
     * @var
     */
    protected $inputFunction;


    /**
     * Constructor
     *
     * @param $options array of configuration
     */
    function  __construct($options = null, $reportFunction = null, $exitFunction = null, $inputFunction = null)
    {
        $this->reportFunction = $reportFunction ? $reportFunction : function ($msg) {
            echo $this->getUsage();
            echo "\n";
            echo $msg;
            echo "\n";
        };
        $this->exitFunction = $exitFunction ? $exitFunction : function () {
            exit;
        };
        $this->inputFunction = $inputFunction ? $inputFunction : function () {
            return trim(fgets(STDIN));
        };
        //Ini
        $this->parsedOptions = array();
        $this->optionDefinitions = array();
        $this->usage = '';
        //set definition
        if ($options) {
            foreach ($options as $option) {
                $this->setOption($option);
            }
        }

        return $this;
    }


    /**
     * Sets option for a given field
     *
     * @param $options array of option configurations
     * @return $this
     * @throws \Exception
     */
    public function setOption($options)
    {
        $def = new OptionDefinition($options);

        $this->optionDefinitions[$def->arg] = $def;

        return $this;
    }

    /**
     * Clear the definitions ( so that you can call setOption again )
     * This is mainly for testing
     */
    public function clearOptions()
    {
        $this->optionDefinitions = array();

        return $this;
    }


    /**
     * Set usage message.
     *
     * the $0 will be replaced with the running script
     *
     *
     * @param $u
     */
    public function setUsage($u)
    {
        $this->usage = str_replace('$0',   $_SERVER['argv'][0], $u);
        return $this;
    }

    /**
     * Get usage message.
     * Provide default if not set
     * @return string
     */
    public function getUsage()
    {
        if ($this->usage) {
            return 'Usage: '.$this->usage;
        }
        return 'Usage: php ' . $_SERVER['argv'][0];
    }

    /**
     * Getter
     * @return array
     */
    public function getParsedOptions()
    {
        return $this->parsedOptions;
    }


    /**
     * Parse arguments
     *
     * @return $this
     */
    public function parse()
    {
        $option = null;
        $result = array();
        $result['_'] = array();
        for ($i = 1; $i < count($_SERVER['argv']); $i++) {
            $parameter = $_SERVER['argv'][$i];
            if (!$option && (preg_match('/^-(\S)$/', $parameter, $matches) || preg_match('/^--(\S\S+)$/', $parameter, $matches))) {
                $option = $matches[1];
            } else if ($option && (preg_match('/^-(\S)$/', $parameter, $matches) || preg_match('/^--(\S\S+)$/', $parameter, $matches))) {
                $result[$option] = true; //no value, but given
                $option = $matches[1];
            } else if ($option) {
                $result[$option] = $parameter;
                $option = null;
            } else {
                array_push($result['_'], $parameter);
            }
        }
        //In case of last option
        if ($option) {
            $result[$option] = null;
        }
        //store parsed options
        $this->parsedOptions = $result;

        //check if -h or --help presents
        if (array_key_exists('h', $this->parsedOptions) || array_key_exists('help', $this->parsedOptions)) {
            //output help text
            call_user_func($this->reportFunction, $this->getHelpMessage());
            call_user_func($this->exitFunction);

        } else {
            foreach ($this->optionDefinitions as $def) {
                try {
                    /**
                     *
                     * loop through the definition to validate the given $options
                     * @var $argOpt \Dlin\Getopt\OptionDefinition
                     */
                    $this->processDefinition($def);

                } catch (OptionException $e) {
                    $valid = $this->handleException($e);
                    if(!$valid){
                        call_user_func($this->exitFunction);
                    }
                }
            }
        }


        return $this;
    }

    /**
     * Generate a help message
     * @return string
     */
    public function getHelpMessage()
    {
        $msg = array();
        $msg[] = "\n";
        $msg[] = $this->getUsage();
        $msg[] = "\n";
        $msg[] = "Options:\n";
        //loop through options

        foreach ($this->optionDefinitions as $def) {

            $msg[] = "\t";
            if (strlen($def->arg) > 1) {
                $msg[] = "--";
            } else {
                $msg[] = "-";
            }
            $msg[] = $def->arg;

            if ($def->alias) {
                $msg[] = ", ";
                if (strlen($def->alias) > 1) {
                    $msg[] = "--";
                } else {
                    $msg[] = "-";
                }
                $msg[] = $def->alias;
            }

            $msg[] = "\t";

            if ($def->required) {
                $msg[] = '[Required] ';
            }

            $msg[] = $def->help;
            $msg[] = "\n";


        }
        return implode($msg, '');

    }


    /**
     * this will validate parsedOptions against given definition
     * @param $definition OptionDefinition
     */
    protected function processDefinition(OptionDefinition $definition)
    {

        $exist = false;
        foreach ($this->parsedOptions as $key => $value) {
            if ($key == '_') {
                continue;
            }

            //match existence
            if ($definition->arg == $key || $definition->alias == $key) {
                $exist = true;

                //match pattern
                if ($definition->pattern && !@preg_match($definition->pattern, $value)) {
                    throw new OptionException($definition->getPatternMsg(), $definition);
                }
            }
        }
        if (($definition->required || $definition->prompt) && !$exist) { //required not found
            throw new OptionException($definition->getRequiredMsg(), $definition);
        }

        return $this;


    }

    /**
     * Handles exception
     * @param OptionException $exception
     */
    protected function handleException(OptionException $exception)
    {


        $definition = $exception->getDefinition();

        //Echo message
        call_user_func($this->reportFunction, $exception->getMessage());


        //depends on the exception, requires input
        $passIn = false;

        foreach ($this->parsedOptions as $key => $value) {
            if ($definition->arg == $key || $definition->alias == $key) {
                $passIn = true;
            }
        }
        if (!$passIn && $definition->prompt) { //show error

            call_user_func($this->reportFunction, $definition->getPromptMessage());

            $valid = false;
            do {
                $input = call_user_func($this->inputFunction);

                if ($input != '' && $definition->pattern && !preg_match($definition->pattern, $input)) {
                    call_user_func($this->reportFunction, $definition->getPatternMsg());
                } else if ($input != '') {
                    $this->parsedOptions[$definition->arg] = $input;
                    $valid = true;
                } else if (!$definition->required) {
                    $valid = true;
                }

            } while (!$valid);


        } else {
            return false;
        }

        return true;
    }

    /**
     * This is the magic method for getting user entered option values
     *
     * @param $opt
     * @return null
     */
    public function __get($opt)
    {
        //if passed
        if (array_key_exists($opt, $this->parsedOptions)) {
            return $this->parsedOptions[$opt];
        }

        //look at the alias
        $definitions = $this->optionDefinitions;
        foreach ($definitions as $def) {
            if ($def->alias == $opt || $def->arg == $opt) {
                if (array_key_exists($def->arg, $this->parsedOptions)) {
                    return $this->parsedOptions[$def->arg];
                }
                if (array_key_exists($def->alias, $this->parsedOptions)) {
                    return $this->parsedOptions[$def->alias];

                } else if ($def->default) {
                    return $def->default;
                }
            }
        }
        return null;

    }

}
