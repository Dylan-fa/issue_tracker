<?php

namespace App\Form;

use App\Entity\KanbanColumnIssue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddIssueToColumnFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('issue', IssueAutocompleteField::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Add issue to column',
            ])
            ->add('id', HiddenType::class, [
                'mapped' => false
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => KanbanColumnIssue::class,
        ]);
    }
}