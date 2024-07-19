<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_attr' => 'id',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_attr' => 'category_name',
            ])
            ->add('parameterValues', CollectionType::class, [
                'entry_type' => PostParameterValueFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
