<?php

namespace App\Form;

use App\Form\AbstractIssueFormType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateIssueFormType extends AbstractIssueFormType
{
    private static $actionText = 'Create issue';

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