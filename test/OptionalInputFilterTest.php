<?php

namespace LaminasTest\InputFilter;

use ArrayIterator;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\OptionalInputFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\InputFilter\OptionalInputFilter
 */
class OptionalInputFilterTest extends TestCase
{
    public function testValidatesSuccessfullyWhenSetDataIsNeverCalled()
    {
        $this->assertTrue($this->getNestedCarInputFilter()->get('car')->isValid());
    }

    public function testValidatesSuccessfullyWhenValidNonEmptyDataSetProvided()
    {
        $data = [
            'car' => [
                'brand' => 'Volkswagen',
                'model' => 'Golf',
            ]
        ];

        $inputFilter = $this->getNestedCarInputFilter();
        $inputFilter->setData($data);

        $this->assertTrue($inputFilter->isValid());
        $this->assertEquals($data, $inputFilter->getValues());
    }

    public function testValidatesSuccessfullyWhenEmptyDataSetProvided()
    {
        $data = [
            'car' => null,
        ];

        $inputFilter = $this->getNestedCarInputFilter();
        $inputFilter->setData($data);

        $this->assertTrue($inputFilter->isValid());
        $this->assertEquals($data, $inputFilter->getValues());
    }

    public function testValidatesSuccessfullyWhenNoDataProvided()
    {
        $data = [];

        $inputFilter = $this->getNestedCarInputFilter();
        $inputFilter->setData($data);

        $this->assertTrue($inputFilter->isValid());
        $this->assertEquals(['car' => null], $inputFilter->getValues());
    }

    public function testValidationFailureWhenInvalidDataSetIsProvided()
    {
        $inputFilter = $this->getNestedCarInputFilter();
        $inputFilter->setData([
            'car' => [
                'brand' => 'Volkswagen',
            ]
        ]);

        $this->assertFalse($inputFilter->isValid());
        $this->assertGetValuesThrows($inputFilter);
    }

    public function testStateIsClearedBetweenValidationAttempts()
    {
        $data = [
            'car' => null,
        ];

        $inputFilter = $this->getNestedCarInputFilter();
        $inputFilter->setData($data);

        $this->assertTrue($inputFilter->isValid());
        $this->assertEquals($data, $inputFilter->getValues());
    }

    /**
     * We are doing some boolean shenanigans in the implementation
     * we want to check that Iterator objects work the same as arrays
     */
    public function testIteratorBehavesTheSameAsArray()
    {
        $optionalInputFilter = new OptionalInputFilter;
        $optionalInputFilter->add(new Input('brand'));

        $optionalInputFilter->setData(['model' => 'Golf']);
        $this->assertFalse($optionalInputFilter->isValid());

        $optionalInputFilter->setData(new ArrayIterator([]));
        $this->assertTrue($optionalInputFilter->isValid());

        $optionalInputFilter->setData([]);
        $this->assertTrue($optionalInputFilter->isValid());
    }

    protected function assertGetValuesThrows(InputFilterInterface $inputFilter)
    {
        try {
            $inputFilter->getValues();
            $this->assertTrue(false);
        // TODO: issue #143 narrow which exception should be thrown
        } catch (\Exception $exception) {
            $this->assertTrue(true);
        }
    }

    private $nestedCarInputFilter;

    protected function getNestedCarInputFilter()
    {
        if (! $this->nestedCarInputFilter) {
            $optionalInputFilter = new OptionalInputFilter;
            $optionalInputFilter->add(new Input('brand'));
            $optionalInputFilter->add(new Input('model'));

            $this->nestedCarInputFilter = new InputFilter;
            $this->nestedCarInputFilter->add($optionalInputFilter, 'car');
        }

        return $this->nestedCarInputFilter;
    }
}
