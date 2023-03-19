<?php

namespace App\Form;

use App\Form\AbstractIssueFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EditIssueFormType extends AbstractIssueFormType
{
    private static $actionText = 'Edit issue';

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