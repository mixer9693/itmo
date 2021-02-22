<?php

namespace Validator;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContainsAuthorFullNameValidatorTest extends WebTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::$container->get('validator');
        $this->em = self::$container->get('doctrine.orm.entity_manager');
    }

    /**
     * @dataProvider getIdenticalAuthors
     */
    public function testIdenticalFullNames(Author $a1, Author $a2)
    {
        $errors = $this->validator->validate($a1);
        self::assertSame(0, count($errors));

        $this->em->beginTransaction();
        try {
            $this->em->persist($a1);
            $this->em->flush();

            $errors = $this->validator->validate($a2);
            self::assertGreaterThan(0, count($errors));

            $this->em->remove($a1);
            $this->em->flush();

        } finally {
            $this->em->rollback();
        }
    }

    /**
     * @dataProvider getDifferentAuthors
     */
    public function testDifferentFullNames(Author $a1, Author $a2)
    {
        $errors = $this->validator->validate($a1);
        self::assertSame(0, count($errors));

        $this->em->beginTransaction();
        try {
            $this->em->persist($a1);
            $this->em->flush();

            $errors = $this->validator->validate($a2);
            self::assertSame(0, count($errors));

            $this->em->remove($a1);
            $this->em->flush();

        } finally {
            $this->em->rollback();
        }
    }

    public function getIdenticalAuthors(): array
    {
        $data = $this->getSameFullNameList();
        return $this->getAuthorList($data);
    }

    public function getDifferentAuthors(): array
    {
        $data = $this->getDifferentFullNameList();
        return $this->getAuthorList($data);
    }

    private function getSameFullNameList(): array
    {
        $arr = [
            [['Альберт', 'Володин', 'Сергеевич'], ['Альберт', 'Володин', 'Сергеевич']],
            [['Альберт', 'Володин', ''],          ['Альберт', 'Володин', '']],
            [['нижний', 'регистр', 'сергеевич'],  ['Нижний', 'регистр', 'Сергеевич']],
            [['_', 'One Two', 'третий лишний '],  ['_', 'One Two', 'третий лишний ']],
        ];
        return $arr;
    }

    private function getDifferentFullNameList(): array
    {
        return [
            [['Альберт', 'Володин', 'Сергеевич'], ['Альберт', 'Володин', '']],
            [['Coaxm', '_', '_ -'],               ['Соахм', '_', '_ -']],
            [['Пробел', 'Володин', 'Сергеевич'],  ['Пробел ', ' Володин', 'Сергеевич']]
        ];
    }

    private function getAuthorList(array $data): array
    {
        $result = [];
        foreach ($data as $arr){
            $a1Data = $arr[0];
            $a1 = new Author();
            $a1->setName($a1Data[0]);
            $a1->setSurname($a1Data[1]);
            $a1->setPatronymic($a1Data[2]);

            $a2Data = $arr[1];
            $a2 = new Author();
            $a2->setName($a2Data[0]);
            $a2->setSurname($a2Data[1]);
            $a2->setPatronymic($a2Data[2]);

            $result[] = [$a1, $a2];
        }
        return $result;
    }

}
