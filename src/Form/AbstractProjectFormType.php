<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base form for project forms
 */
abstract class AbstractProjectFormType extends AbstractType

{
    abstract protected static function getActionText(): string;
    abstract protected function modifyForm(FormBuilderInterface $builder): FormBuilderInterface;
    abstract protected function addOptions(): array;
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => '7',
                ],
                
            ])
            ->add('members', ProjectMembersAutocompleteField::class)
        ;
        $this->modifyForm($builder)
            ->add('submit', SubmitType::class, [
                'label' => static::getActionText(),
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {   
        $options = array_merge([
            'data_class' => Project::class,
        ], $this->addOptions());
        $resolver->setDefaults($options);
    }

}