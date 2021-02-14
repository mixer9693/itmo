<?php


namespace App\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsBookNameAndYear extends Constraint
{
    public $message = 'Книга с указаннм названием и годом выпуска уже существует #{{ bookId }}';

    public function validatedBy(): string
    {
        return ContainsBookNameAndYearValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}