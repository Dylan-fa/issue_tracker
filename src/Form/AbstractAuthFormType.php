<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Base form for authentication forms
 */
abstract class AbstractAuthFormType extends AbstractType

{
    abstract protected static function getActionText(): string;
    abstract protected function modifyForm($builder): FormBuilderInterface;
    abstract protected function addOptions(): array;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a username',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                ]
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
            'data_class' => User::class,
        ], $this->addOptions());
        $resolver->setDefaults($options);
    }

}
