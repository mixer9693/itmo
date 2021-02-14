<?php


namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsAuthorFullName extends Constraint
{
    public $message = 'Автор с указаннми ФИО уже существует #{{ authorId }}';

    public function validatedBy(): string
    {
        return ContainsAuthorFullNameValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}