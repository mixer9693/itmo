<?php


namespace App\Validator;


use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use UnexpectedValueException;

class ContainsBookNameAndYearValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsBookNameAndYear) {
            throw new UnexpectedTypeException($constraint, ContainsBookNameAndYear::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Book) {
            throw new UnexpectedValueException($value, 'string');
        }

        /** @var Book $existingBook */
        $existingBook = $this->em->getRepository(Book::class)
            ->findOneBy([
                'name' => $value->getName(),
                'year' => $value->getYear()
            ]);

        if ($existingBook && $existingBook->getId() !== $value->getId()){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ bookId }}', $existingBook->getId())
                ->addViolation();
        }
    }
}