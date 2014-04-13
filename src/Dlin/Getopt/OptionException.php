<?php
/**
 * 
 * User: davidlin
 * Date: 12/04/2014
 * Time: 9:14 AM
 * 
 */

namespace Dlin\Getopt;


class OptionException extends \Exception{

    /**
     * Option definition
     * @var
     */
    protected $definition;



    /**
     *
     * New constructor
     * @param string $message
     * @param int $def OptionDefinition
     * @param \Exception $option
     */
    public function __construct($message, OptionDefinition $def) {
        $this->definition = $def;

        parent::__construct($message);
    }

    // custom string representation of object
    public function __toString() {
        return $this->getMessage();
    }

    //Getter for definition
    public function getDefinition() {
        return $this->definition;
    }



}