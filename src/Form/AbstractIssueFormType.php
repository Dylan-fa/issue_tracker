<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Priority;
use App\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base form for issue forms
 */
abstract class AbstractIssueFormType extends AbstractType

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
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => '7',
                ],
                
            ])
            ->add('assignee', AssigneeAutocompleteField::class)
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
            ])
            ->add('priority', EntityType::class, [
                'class' => Priority::class,
                'choice_label' => 'name',
            ])
            ->add('tags', ProjectAutocompleteField::class, [
            ])
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
            'data_class' => Issue::class,
        ], $this->addOptions());
        $resolver->setDefaults($options);
    }

}