<?php

namespace Milex\LeadBundle\Form\Type;

use Milex\CoreBundle\Form\Type\FormButtonsType;
use Milex\LeadBundle\Entity\LeadDevice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('device', TextType::class);
        $builder->add('deviceOsName', TextType::class);
        $builder->add('deviceOsShortName', TextType::class);
        $builder->add('deviceOsVersion', TextType::class);
        $builder->add('deviceOsPlatform', TextType::class);
        $builder->add('deviceModel', TextType::class);
        $builder->add('deviceBrand', TextType::class);

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'apply_text' => false,
                'save_text'  => 'milex.core.form.save',
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => LeadDevice::class,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leaddevice';
    }
}
