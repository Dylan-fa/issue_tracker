<?php

namespace App\Form;

use App\Form\AbstractAuthFormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SignupFormType extends AbstractAuthFormType
{
    private static $actionText = 'Sign up';

    public static function getActionText(): string
    {
        return self::$actionText;
    }

    protected function modifyForm($builder): FormBuilderInterface
    {
        $builder
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new Callback([$this, 'validatePasswordConfirmedCallback']),
                ],
            ])
        ;
        return $builder;
    }

    protected function addOptions(): array
    {
        return [];
    }

    public function validatePasswordConfirmedCallback($value, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot();
        if($form->get('password')->getData() != $value) {
            $context
                ->buildViolation('The confirm password is not the same as the input password')
                ->addViolation();
        }
    }

}
