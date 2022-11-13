<?php

namespace Milex\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CampaignEventPageHitType.
 */
class CampaignEventPageHitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pages', PageListType::class, [
            'label'      => 'milex.page.campaign.event.form.pages',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class'   => 'form-control',
                'tooltip' => 'milex.page.campaign.event.form.pages.descr',
            ],
        ]);

        $builder->add('url', TextType::class, [
            'label'      => 'milex.page.campaign.event.form.url',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class'   => 'form-control',
                'tooltip' => 'milex.page.campaign.event.form.url.descr',
            ],
        ]);

        $builder->add('referer', TextType::class, [
            'label'      => 'milex.page.campaign.event.form.referer',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class'   => 'form-control',
                'tooltip' => 'milex.page.campaign.event.form.referer.descr',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'campaignevent_pagehit';
    }
}