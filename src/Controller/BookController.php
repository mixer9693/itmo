<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Image;
use App\Form\BookType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(): Response
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('book_show', [
                'id' => $book->getId()
            ]);
        }

        return $this->render('book/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{book_id}/image/{image_id}/delete", name="book_cover_delete", methods={"DELETE"})
     */
    public function deleteCover(int $book_id, int $image_id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $book = $entityManager->getRepository(Book::class)->find($book_id);
        $image = $entityManager->getRepository(Image::class)->find($image_id);

        if (!$book || !$image){
            throw new NotFoundHttpException();
        }

        $book->setCover(null);
        $entityManager->persist($book);
        $entityManager->remove($image);
        $entityManager->flush();

        return $this->redirectToRoute('book_show', [
            'id' => $book->getId()
        ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('book_show', [
                'id' => $book->getId()
            ]);
        }

        return $this->render('book/form.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
            'form_label' => 'Редактирование книги'
        ]);
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Book $book): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_index');
    }

}
