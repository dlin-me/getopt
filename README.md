# Dlin Getopt Option Parser


## 1. Overview

PHP Command Line Argument Parser.


When developing a executable command line script in PHP, we are most likely trying to achieve the following:

- Tell users what options are available
- Capture values user pass as command arguments
- Prompt user for input if a option is not provided
- Allow using short option names and long option names ( e.g. -h and -help )
- Validate option values, if invalid, show error message or ask user to enter again

Dlin\Getopt is designed to free you from writing all these repeated tasks.



## 2. Installation
#### With composer, add to your composer.json :

```
{
    require: {
        "dlin/getopt": "dev-master"
    }
}
```

#### For those who are not using composer:

- download the this package
- include the file **/src/Dlin/Getopt/getopt_inc.php**  into your script.



## 3. Basic Usage

Example used in the following sections assumes we are working on a command line PHP script named **myScript.php**





#### Constructor
The constructor of the Geocoder class takes an optional array of option configurations as the parameter. Please refer to the configuration section for option configuration settings.

```
//include composer autoload or getopt_inc.php
include './vendor/autoload.php';

$configs = array();
$configs[] = array('arg'=>'s'); //script accepts a 's' option i.e. -s
$configs[] = array('arg'=>'t'); //script accepts a 't' option, i.e. -t

$getOpt = new GetOpt(configs);

```
or you can set option configuration after creating an instance

```
$getOpt = new GetOpt();
$getOpt->setOption(array('arg'=>'s'));
$getOpt->setOption(array('arg'=>'t'));
```


#### Passing option parameters at command line

The option parameters provided at command line must either be in

- short format:  '-' followed by single letter. e.g. -h
- long format: '--' followed by more than one letter. e.g. --help

String with invalid option format will be considered as normal parameter

Option parameter values are  the first argument after the option parameter string. It does not matter if a value has leading white space or not. However, value can not contain any white space.

If a value is passed without a matching option parameter (e.g. -h or --help), it will be make available in the script via the '_' property. (see next section)



#### Getting option values in the script

##### - Parsable options
```
//php myScript.php -s 10 -t int foo bar zee

echo $getOpt->t; // output int
echo $getOpt->s; // output 10
echo $getOpt->foo; //null
echo $getOpt->nonexist; //null


```

##### - Unparsable options

The '_' (underscore) property contains an array of other unparsable parameters

```
//php myScript.php -s 10 -t int foo bar zee

echo $getOpt->_; //Array with values ['foo', 'bar', 'zee']
```

##### - Invalid option parameters
Invalid option parameters may lead to unexpected result:

```
//php myScript.php --s 10 -type int   (wrong)

echo $getOpt->s; // null
echo $getOpt->type; // null
echo $getOpt->_; //Array, ['--s', '10', '-type', int]

//php myScript.php -s 10 --type int   (the right way)

echo $getOpt->s; // 10
echo $getOpt->type; // int
echo $getOpt->_; //Array, [ ]


```





#####Alias

You can define an option to have alias. e.g. -t with alias -type, -s with alias -size.  the option and its alias can be used interchangeably. Alias name dose not need to be longer or shorter than the 'arg' name. And 'arg' name does not need to be of single character.

```
$getOpt = new GetOpt();

$getOpt->setOption(array('arg'=>'s', 'alias'=>'size')); //normal
$getOpt->setOption(array('arg'=>'type', 'alias'=>'t')); //this also work

//php myScript.php -s 10 -t int foo bar zee
//or
//php myScript.php --size 10 -t int foo bar zee
//or
//php myScript.php --size 10 --type int foo bar zee

echo $getOpt->t; // output int
echo $getOpt->type; // output int
echo $getOpt->s; // output 10
echo $getOpt->size; // output 10


```



## 4. Option configuration

An option config is simple an associate array ( object ).

###Fields:

- **arg**
[required] name of the option
- **alias**
[optional] alias name
- **default**
[optional] Default value when option is not passed as argument
- **help**
[optional] the help text for this option. e.g. Type of Item to generate
- **required**
[optional] Specify that this option is mandatory. Default is false
- **requiredMsg**
[optional] Message to show when this required option is not provided. Default looks like: 'Option -t is required.'
- **pattern**
[optional] Specify that value for this option must match given regular expression pattern. default '/\S+/'
- **patternMsg**
[Optional] Message to show when provided value does not match the required pattern. Default looks like: 'Option -t must match pattern /\d+/'
- **prompt**
[Optional] Ask for user input for an option if it is not passed as argument. Default false
- **promptMsg**
[Optional] Message to show when asking user for option value. Default looks like: 'Please enter: (Type of item to generate)' while text in the parentheses is the help text.

###Example:


```
$config = array(
	'arg'   => 's',
	'alias' => 'size',
	'help'	=> 'Size of generated array',
	'default' => 10,
	'required'		=>  true,
	'requiredMsg'	=>  'Size is missing',
	'pattern'		=>	'/\d+/',
	'patternMsg'	=>	'Size must be a integer',
	'prompt'		=>	true,
	'promptMsg'		=>  'Please enter size of the array to generate'
);


```

###Note:


* If '***required***', '***default***' will be ignored. '***required***' means the option MUST pass as argument.
* An option does not need to have a value to fullfil the '***required***' requirement. e.g. '*php myScript.php -s*'
* '***default***' only applies when option is NOT passed as arguement.  If a option is passed without a value, e.g. '*php myScript.php -s*', the value of 's' is boolean value true.
* If '***required***', '***prompt***' will be ignored. Again, '***required***' means the option MUST be passed as argument. And if it is passed, there's no point prompting for input.
* '**arg****' and '**alias****' are casesensitive.





## 5. Showing Usage

By default, when a parameter -h or --help is passed, GetOpt will terminate the current script, and outputs the usage message like the following:

```
Usage: php myScript.php

Options:
	-s,size	 	Size of generated array
	-t,type		Type of items in the generated array

```

The descriptive text on the right are option's '**help**' property. The Usage line can be customised:

```
$getopt->setUsage('/usr/bin/php $0 -s [num] -t [num]');

echo $getopt->getUsage();

//OUTPUT:  Usage: /usr/bin/php myScript.php -s [num] -t [num]

```



## 6. Advanced Configuration

You can further customize:

1. How messages are output
2. How script is terminated
3. How user input is captured


by providing your own functions to the constructor:

```

$reportFunc = function($msg){ echo touppercase($msg); };

$exitFunc = function(){}; //do not terminate

$inputFunc = function(){/* read and return a line from a file instead */ };

...
$getopt = new Getopt(null, $reportFunc , $existFunc, $inputFunc);
...

```

Here is the default implementation of the 3 functions if not provided:

```
$this->reportFunction =  function ($msg) {
    echo $msg;
    echo "\n";
};
$this->exitFunction =  function () {
    exit;
};
$this->inputFunction =   function () {
    return trim(fgets(STDIN));
};

```



## 7. License


This library is free. Please refer to the license file in the root directory for detail license info.

