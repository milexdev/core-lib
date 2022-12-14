<?php

namespace Milex\LeadBundle\Form\Type;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'company_unique_identifiers_operator',
            ChoiceType::class,
            [
                'choices'           => [
                    'milex.core.config.contact_unique_identifiers_operator.or'    => CompositeExpression::TYPE_OR,
                    'milex.core.config.contact_unique_identifiers_operator.and'   => CompositeExpression::TYPE_AND,
                ],
                'label'             => 'milex.core.config.unique_identifiers_operator',
                'required'          => false,
                'attr'              => [
                    'class'   => 'form-control',
                    'tooltip' => 'milex.core.config.unique_identifiers_operator.tooltip',
                ],
                'placeholder'       => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'companyconfig';
    }
}
