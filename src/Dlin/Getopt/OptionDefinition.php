<?php
/**
 *
 * User: davidlin
 * Date: 11/04/2014
 * Time: 11:02 PM
 *
 */

namespace Dlin\Getopt;


class OptionDefinition {


    protected $data = array();

    /**
     * Constructor
     *
     * @param $options array of configuration
     */
    public function __construct(array $options)
    {

        $args = array('arg', 'alias', 'required', 'default', 'help', 'prompt', 'pattern', 'promptMsg', 'patternMsg', 'requiredMsg');
        $result = array();
        foreach ($args as $arg) {
           $result[$arg] = array_key_exists($arg, $options) ?  $options[$arg] : null;
        }
        if (!$result['arg']) {
            throw new \Exception('Invalid option provided, "arg" key is required.');
        }
        $this->data = $result;

        return $this;

    }





    /**
     * Magic method for getting configuartion fields
     *
     * @param $name
     * @return mixed
     */
    public function __get($name){
        return $this->data[$name];
    }


    /**
     * Get the message to display when missing required option
     * @return string
     */
    public function getRequiredMsg(){
        return $this->data['requiredMsg'] ? $this->data['requiredMsg'] : 'Option -'.$this->data['arg'].' is required.';
    }

    /**
     * Get the message to display when option pattern matching fails
     * @return string
     */
    public function getPatternMsg(){
        return $this->data['patternMsg'] ? $this->data['patternMsg']: 'Option -'.$this->data['arg'].' must match pattern: '. $this->data['pattern']."\n";
    }

    /**
     * Get the message to show when user input is required
     *
     * @return string
     */
    public function getPromptMessage(){

        return $this->data['promptMsg']?$this->data['promptMsg']: 'Please enter ('. $this->data['help']."):";
    }

}