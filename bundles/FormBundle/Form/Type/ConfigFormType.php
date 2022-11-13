<?php

namespace Milex\FormBundle\Form\Type;

use Milex\CoreBundle\Form\DataTransformer\ArrayLinebreakTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayLinebreakTransformer = new ArrayLinebreakTransformer();
        $builder->add(
            $builder->create(
                'do_not_submit_emails',
                TextareaType::class,
                [
                    'label'      => 'milex.form.config.form.do_not_submit_email',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'   => 'form-control',
                        'tooltip' => 'milex.form.config.form.do_not_submit_email.tooltip',
                        'rows'    => 8,
                    ],
                    'required' => false,
                ]
            )->addViewTransformer($arrayLinebreakTransformer)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'formconfig';
    }
}
