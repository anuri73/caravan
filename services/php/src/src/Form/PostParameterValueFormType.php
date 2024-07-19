<?php

namespace App\Form;

use App\DataTransformer\ParameterSelectTransformer;
use App\Entity\PostParameterValue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostParameterValueFormType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $transformer = new ParameterSelectTransformer($this->entityManager);

        $builder
            ->add('parameter', PostParameterSelectFormType::class)
            ->add('value', TextType::class);

        $builder->get('parameter')->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostParameterValue::class,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}