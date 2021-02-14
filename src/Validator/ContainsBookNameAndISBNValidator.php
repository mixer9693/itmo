<?php


namespace App\Validator;


use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use UnexpectedValueException;

class ContainsBookNameAndISBNValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsBookNameAndISBN) {
            throw new UnexpectedTypeException($constraint, ContainsBookNameAndISBN::class);
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
                'ISBN' => $value->getISBN()
            ]);

        if ($existingBook && $existingBook->getId() !== $value->getId()){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ bookId }}', $existingBook->getId())
                ->addViolation();
        }
    }
}