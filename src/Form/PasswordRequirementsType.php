<?php

namespace App\Form;

use App\Model\PasswordRequirements;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordRequirementsType extends AbstractType
{
    private Request $request;

    public function __construct(
        private readonly ParameterBagInterface $params,
        RequestStack $requestStack,
    )
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('length', ChoiceType::class, [
                'choices' => range(
                    $this->params->get('app.password_min_length'),
                    $this->params->get('app.password_max_length')
                ),
                'choice_label' => fn ($value) => $value
            ])
            ->add('uppercase_letters', CheckboxType::class, [
                'label' => 'Uppercase Letters',
                'required' => false
            ])
            ->add('digits', CheckboxType::class, [
                'required' => false
            ])
            ->add('special_characters', CheckboxType::class, [
                'label' => 'Special Characters',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $passwordRequirements = new PasswordRequirements();

        $passwordRequirements->setLength($this->request->cookies->getInt(
            'app_length', $this->params->get('app.password_default_length')
        ));
        $passwordRequirements->setUppercaseLetters(
            $this->request->cookies->getBoolean('app_uppercase_letters', false)
        );
        $passwordRequirements->setDigits(
            $this->request->cookies->getBoolean('app_digits', false)
        );
        $passwordRequirements->setSpecialCharacters(
            $this->request->cookies->getBoolean('app_special_characters', false)
        );

        $resolver->setDefaults([
            'data_class' => PasswordRequirements::class,
            'data' => $passwordRequirements
        ]);
    }
}
