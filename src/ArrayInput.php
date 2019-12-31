<?php

/**
 * @see       https://github.com/laminas/laminas-inputfilter for the canonical source repository
 * @copyright https://github.com/laminas/laminas-inputfilter/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-inputfilter/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\InputFilter;

class ArrayInput extends Input
{
    /**
     * @var array
     */
    protected $value = array();

    /**
     * @param  array $value
     * @throws Exception\InvalidArgumentException
     * @return Input
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Value must be an array, %s given.', gettype($value))
            );
        }
        return parent::setValue($value);
    }

    /**
     * @return array
     */
    public function getValue()
    {
        $filter = $this->getFilterChain();
        $result = array();
        foreach ($this->value as $key => $value) {
            $result[$key] = $filter->filter($value);
        }
        return $result;
    }

    /**
     * @param  mixed $context Extra "context" to provide the validator
     * @return bool
     */
    public function isValid($context = null)
    {
        $this->injectNotEmptyValidator();
        $validator = $this->getValidatorChain();
        $values    = $this->getValue();
        $result    = true;
        foreach ($values as $value) {
            $result = $validator->isValid($value, $context);
            if (!$result) {
                if ($this->hasFallback()) {
                    $this->setValue($this->getFallbackValue());
                    $result = true;
                }
                break;
            }
        }

        return $result;
    }
}
