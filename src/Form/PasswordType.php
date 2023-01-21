<?php

namespace App\Form;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordType extends AbstractType
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $passwordLengthRange = range(
        //     $this->parameterBag->get('app.password_min_length'),
        //     $this->parameterBag->get('app.password_max_length')
        // );

        $builder
            ->add('length', ChoiceType::class, [
                // 'choices' => array_combine($passwordLengthRange, $passwordLengthRange),
                'choices' => range(
                    $this->parameterBag->get('app.password_min_length'),
                    $this->parameterBag->get('app.password_max_length')
                ),
                'choice_label' => function ($value) {
                    return $value;
                },
            ])
            ->add('uppercaseLetters', CheckboxType::class, [
                'label' => 'Uppercase Letters'
            ])
            ->add('digits', CheckboxType::class)
            ->add('specialCharacters', CheckboxType::class, [
                'label' => 'Special Characters'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
