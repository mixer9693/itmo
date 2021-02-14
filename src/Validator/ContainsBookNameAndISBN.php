<?php


namespace App\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsBookNameAndISBN extends Constraint
{
    public $message = 'Книга с заданным названием и ISBN уже существует #{{ bookId }}';

    public function validatedBy(): string
    {
        return ContainsBookNameAndISBNValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}