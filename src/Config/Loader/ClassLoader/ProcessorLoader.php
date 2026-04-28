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

declare(strict_types=1);

namespace Cascade\Config\Loader\ClassLoader;

use Cascade\Config\Loader\ClassLoader;

/**
 * Processor Loader. Loads the Processor options, validate them and instantiates
 * a Processor object (implementing Monolog\Processor\ProcessorInterface) with all
 * the corresponding options
 * @see ClassLoader
 *
 * @author Kate Burdon <kburdon@tableau.com>
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class ProcessorLoader extends ClassLoader
{
    /**
     * Constructor
     * @see ClassLoader::__construct
     * @see \Monolog\Handler classes for handler options
     *
     * @param array $processorOptions Processor options
     */
    public function __construct(array &$processorOptions)
    {
        parent::__construct($processorOptions);

        // @todo add additional options later?  Is the "tags" option needed in this implementation?
    }
}
