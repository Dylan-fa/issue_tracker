<?php

namespace App\Form;

use App\Form\AbstractAuthFormType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginFormType extends AbstractAuthFormType
{
    private static $actionText = 'Log in';

    public static function getActionText(): string
    {
        return self::$actionText;
    }

    protected function modifyForm($builder): FormBuilderInterface
    {
        return $builder;
    }

    protected function addOptions(): array
    {
        return ['csrf_token_id' => 'authenticate'];
    }
}