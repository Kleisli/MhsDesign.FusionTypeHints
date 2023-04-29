<?php
namespace MhsDesign\FusionTypeHints\Validation\Validator;

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Validation\Validator\AbstractValidator;

/**
 * Validator for not empty values.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class NeosValidator extends AbstractValidator
{
    /**
     * This validator always needs to be executed even if the given value is empty.
     * See AbstractValidator::validate()
     *
     * @var boolean
     */
    protected $acceptsEmptyValues = false;

    /**
     * Checks if the given value is not empty (NULL, empty string, empty array
     * or empty object that implements the Countable interface).
     *
     * @param mixed $value The value that should be validated
     * @return void
     * @api
     */
    protected function isValid($value)
    {
        if ($value !== 'NEOS') {
            $this->addError('This property needs to be NEOS, found "'.$value.'"', 1682770100);
        }
    }
}
