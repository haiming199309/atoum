<?php

namespace mageekguy\atoum\report\fields\runner\errors;

use mageekguy\atoum\cli\colorizer;
use mageekguy\atoum\cli\prompt;
use mageekguy\atoum\report;

class cli extends report\fields\runner\errors
{
    protected $titlePrompt = null;
    protected $titleColorizer = null;
    protected $methodPrompt = null;
    protected $methodColorizer = null;
    protected $errorPrompt = null;
    protected $errorColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setTitlePrompt()
            ->setTitleColorizer()
            ->setMethodPrompt()
            ->setMethodColorizer()
            ->setErrorPrompt()
            ->setErrorColorizer()
        ;
    }

    public function __toString()
    {
        $string = '';

        if ($this->runner !== null) {
            $errors = $this->runner->getScore()->getErrors();

            $sizeOfErrors = count($errors);

            if ($sizeOfErrors > 0) {
                $string .=
                    $this->titlePrompt .
                    sprintf(
                        $this->locale->_('%s:'),
                        $this->titleColorizer->colorize(sprintf($this->locale->__('There is %d error', 'There are %d errors', $sizeOfErrors), $sizeOfErrors))
                    ) .
                    PHP_EOL
                ;

                $class = null;
                $method = null;

                foreach ($errors as $error) {
                    if ($error['class'] !== $class || $error['method'] !== $method) {
                        $string .=
                            $this->methodPrompt .
                            sprintf(
                                $this->locale->_('%s:'),
                                $this->methodColorizer->colorize($error['class'] . '::' . $error['method'] . '()')
                            ) .
                            PHP_EOL
                        ;

                        $class = $error['class'];
                        $method = $error['method'];
                    }

                    $string .= $this->errorPrompt;

                    $type = static::getType($error['type']);
                    $case = $error['case'] === null ? '' : sprintf(' in case \'%s\'', $error['case']);

                    switch (true) {
                        case $error['file'] === null:
                            switch (true) {
                                case $error['errorFile'] === null:
                                    $errorMessage = $this->locale->_('Error %s in unknown file on unknown line%s, generated by unknown file', $type, $case);
                                    break;

                                case $error['errorLine'] === null:
                                    $errorMessage = $this->locale->_('Error %s in unknown file on unknown line, generated by file %s%s', $type, $error['errorFile'], $case);
                                    break;

                                case $error['errorLine'] !== null:
                                    $errorMessage = $this->locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d%s', $type, $error['errorFile'], $error['errorLine'], $case);
                                    break;
                            }
                            break;

                        case $error['line'] === null:
                            switch (true) {
                                case $error['errorFile'] === null:
                                    $errorMessage = $this->locale->_('Error %s in %s on unknown line, generated by unknown file%s', $type, $error['file'], $case);
                                    break;

                                case $error['errorLine'] === null:
                                    $errorMessage = $this->locale->_('Error %s in %s on unknown line, generated by file %s%s', $type, $error['file'], $error['errorFile'], $case);
                                    break;

                                case $error['errorLine'] !== null:
                                    $errorMessage = $this->locale->_('Error %s in %s on unknown line, generated by file %s on line %d%s', $type, $error['file'], $error['errorFile'], $error['errorLine'], $case);
                                    break;
                            }
                            break;

                        default:
                            switch (true) {
                                case $error['errorFile'] === null:
                                    $errorMessage = $this->locale->_('Error %s in %s on line %d, generated by unknown file%s', $type, $error['file'], $error['line'], $case);
                                    break;

                                case $error['errorLine'] === null:
                                    $errorMessage = $this->locale->_('Error %s in %s on line %d, generated by file %s%s', $type, $error['file'], $error['line'], $error['errorFile'], $case);
                                    break;

                                case $error['errorLine'] !== null:
                                    $errorMessage = $this->locale->_('Error %s in %s on line %d, generated by file %s on line %d%s', $type, $error['file'], $error['line'], $error['errorFile'], $error['errorLine'], $case);
                                    break;
                            }
                            break;
                    }

                    $string .= sprintf(
                            $this->locale->_('%s:'),
                            $this->errorColorizer->colorize(($errorMessage))
                        ) .
                        PHP_EOL
                    ;

                    foreach (explode(PHP_EOL, $error['message']) as $line) {
                        $string .= $line . PHP_EOL;
                    }
                }
            }
        }

        return $string;
    }

    public function setTitlePrompt(prompt $prompt = null)
    {
        $this->titlePrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getTitlePrompt()
    {
        return $this->titlePrompt;
    }

    public function setTitleColorizer(colorizer $colorizer = null)
    {
        $this->titleColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getTitleColorizer()
    {
        return $this->titleColorizer;
    }

    public function setMethodPrompt(prompt $prompt = null)
    {
        $this->methodPrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getMethodPrompt()
    {
        return $this->methodPrompt;
    }

    public function setMethodColorizer(colorizer $colorizer = null)
    {
        $this->methodColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getMethodColorizer()
    {
        return $this->methodColorizer;
    }

    public function setErrorPrompt(prompt $prompt = null)
    {
        $this->errorPrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getErrorPrompt()
    {
        return $this->errorPrompt;
    }

    public function setErrorColorizer(colorizer $colorizer = null)
    {
        $this->errorColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getErrorColorizer()
    {
        return $this->errorColorizer;
    }
}
