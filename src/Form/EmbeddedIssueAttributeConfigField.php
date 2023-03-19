<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use InvalidArgumentException;

class EmbeddedIssueAttributeConfigField extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if(!is_a($options['data_class'], 'App\Entity\IssueAttributeInterface', true)) {
            throw new InvalidArgumentException('Entity must implement IssueAttributeInterface');
        }

        $builder
            ->add('name', TextType::class, [
            ])
            ->add('color', ColorType::class, [
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('data_class')
        ;
    }
}