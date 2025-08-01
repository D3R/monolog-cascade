<?php

/**
 * This file is part of the Monolog Cascade package.
 *
 * (c) Raphael Antonmattei <rantonmattei@theorchard.com>
 * (c) The Orchard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cascade\Tests\Fixtures;

/**
 * Class SampleClass
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class SampleClass
{
    /**
     * Optional member A
     * @var mixed
     */
    public $optionalB;

    /**
     * Optional member Y
     * @var mixed
     */
    public $optionalY;

    /**
     * Constructor
     *
     * @param mixed $mandatory Some mandatory param
     */
    public function __construct(
        $mandatory
    ) {
        $this->setMandatory($mandatory);
    }

    /**
     * Set the mandatory property
     *
     * @param mixed $mandatory Some value
     */
    public function setMandatory($mandatory)
    {
    }

    /**
     * Function that sets the optionalA member
     *
     * @param  mixed $value Some value
     */
    public function optionalA($value)
    {
    }

    /**
     * Function that sets the optionalX member
     *
     * @param  mixed $value Some value
     */
    public function optionalX($value)
    {
    }

    /**
     * Function that sets the hello member
     *
     * @param  mixed $value Some value
     */
    public function setHello($value)
    {
    }

    /**
     * Function that sets the there member
     *
     * @param  mixed $value Some value
     */
    public function setThere($value)
    {
    }
}
