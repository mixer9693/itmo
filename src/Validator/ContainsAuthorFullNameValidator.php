<?php


namespace App\Validator;


use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use UnexpectedValueException;

class ContainsAuthorFullNameValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsAuthorFullName) {
            throw new UnexpectedTypeException($constraint, ContainsAuthorFullName::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Author) {
            throw new UnexpectedValueException($value, 'string');
        }

        /** @var Author $existingAuthor */
        $existingAuthor =$this->em->getRepository(Author::class)
            ->findOneBy([
                'name' => $value->getName(),
                'surname' => $value->getSurname(),
                'patronymic' => $value->getPatronymic()
            ]);
        if ($existingAuthor && $value->getId() !== $existingAuthor->getId()){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ authorId }}', $existingAuthor->getId())
                ->addViolation();
        }

    }
}