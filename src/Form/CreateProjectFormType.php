<?php

namespace App\Form;

use App\Form\AbstractProjectFormType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateProjectFormType extends AbstractProjectFormType
{
    private static $actionText = 'Create project';

    public static function getActionText(): string
    {
        return self::$actionText;
    }

    protected function modifyForm(FormBuilderInterface $builder): FormBuilderInterface
    {
        return $builder;
    }

    protected function addOptions(): array
    {
        return [];
    }
}