<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Book $data */
        $data = $builder->getData();
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название'
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Год издания',
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('ISBN', TextType::class, [
                'label' => 'ISBN'
            ])
            ->add('pagesNumber', IntegerType::class, [
                'label' => 'Количество страниц',
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('authors', EntityType::class, [
                'label' => 'Авторы',
                'class' => Author::class,
                'multiple' => true
            ])
            ->add('cover', ImageType::class, [
                'label' => 'Обложка',
                'data' => $data->getCover() ? $data->getCover(): null,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => [
                    'class' => 'btn btn-sm'
                ]
            ])
        ;
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($data){
            if ($data && $data->getCover()){
                $event->getForm()->remove('cover');
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }

}
