<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
                ->add('email', EmailType::class)
                ->add("submit", SubmitType::class);
    }

}