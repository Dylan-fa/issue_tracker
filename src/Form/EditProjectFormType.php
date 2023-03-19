<?php

namespace App\Form;

use App\Form\AbstractProjectFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EditProjectFormType extends AbstractProjectFormType
{
    private static $actionText = 'Edit project';

    public static function getActionText(): string
    {
        return self::$actionText;
    }

    protected function modifyForm(FormBuilderInterface $builder): FormBuilderInterface
    {
        $user = $this->security->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $builder
                ->add('delete', SubmitType::class, [
                    'attr' => ['class' => 'btn-danger']
                ])
            ;
        }
        return $builder;
    }

    protected function addOptions(): array
    {
        return [];
    }
}