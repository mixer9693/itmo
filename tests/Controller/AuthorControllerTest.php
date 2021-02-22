<?php

namespace Controller;


use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorControllerTest extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function setUp()
    {
        self::bootKernel();
        $this->em = self::$container->get('doctrine.orm.entity_manager');
    }

    public function testIndexPage()
    {
        $indexUrl = $this->getUrls()['author_index'][0];

        $client = static::createClient();
        $crawler = $client->request('GET', $indexUrl);

        $this->assertSelectorExists('table');
        $this->assertSelectorExists('.add-author-btn');

        $link = $crawler->filter('.add-author-btn')->link();
        $newUrl = sprintf('http://%s%s',
            $client->getRequest()->getHost(),
            $indexUrl = $this->getUrls()['author_new'][0]
        );
        self::assertSame($newUrl, $link->getUri());

        $client->click($link);
        self::assertResponseIsSuccessful();
    }

    /**
     * @param Author $author
     * @dataProvider getAuthor
     */
    public function testNewPage(Author $author)
    {
        $newUrl = $this->getUrls()['author_new'][0];
        $client =  self::createClient();
        $crawler = $client->request('GET', $newUrl);

        $form = $crawler->filter('[type=submit]')->form();
        $form['author[name]'] = $author->getName();
        $form['author[surname]'] = $author->getSurname();
        $form['author[patronymic]'] = $author->getPatronymic();

        try{
            $client->submit($form);
            self::assertSame(302, $client->getResponse()->getStatusCode());

            $location = $client->getResponse()->headers->get('location');
            $client->request('GET', $location);
            self::assertResponseIsSuccessful();

        } finally {
            $author = $this->em->getRepository(Author::class)
                ->findOneBy([
                        'name' => $author->getName(),
                        'surname' => $author->getSurname(),
                        'patronymic' => $author->getPatronymic()
                ]);
            if ($author){
                $this->em->remove($author);
                $this->em->flush();
            }
        }

    }

    public function getUrls(): array
    {
        return [
            'author_index'  => ['/author'],
            'author_new'    => ['/author/new'],
            'author_show'   => ['/author/{id}'],
            'author_edit'   => ['/author/{id}/edit'],
            'author_delete' => ['/author/{id}']
        ];
    }

    public function getAuthor(): array
    {
        $author = new Author();
        $author->setName("Иван");
        $author->setSurname("Дуров");
        $author->setPatronymic("Евгеньевич");
        return [[$author]];
    }

}
