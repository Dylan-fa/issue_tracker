<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueAtrributeConfigFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attributes', CollectionType::class,
                [
                    'entry_type' => EmbeddedIssueAttributeConfigField::class,
                    'entry_options' => [
                        'label' => false,
                        'error_bubbling' => false,
                        'data_class' => $options['attribute_class']
                    ],
                    'data' => $options['attribute_data'],
                    'allow_add' => true,
                    'allow_delete' => true,

                ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ])
            ->add('add', ButtonType::class, [
                'attr' => ['data-action' => 'click->issue-attribute-config#add'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attribute_class' => [],
            'attribute_data' => []
        ]);
    }
}