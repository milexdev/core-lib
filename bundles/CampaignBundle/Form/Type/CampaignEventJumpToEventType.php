<?php

namespace Mautic\CampaignBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CampaignEventJumpToEventType.
 */
class CampaignEventJumpToEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jumpProps = $builder->getData();
        $selected  = isset($jumpProps['jumpToEvent']) ? $jumpProps['jumpToEvent'] : null;

        $builder->add(
            'jumpToEvent',
            ChoiceType::class,
            [
                'choices'    => [],
                'multiple'   => false,
                'label'      => 'milex.campaign.form.jump_to_event',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'                => 'form-control',
                    'data-onload-callback' => 'updateJumpToEventOptions',
                    'data-selected'        => $selected,
                ],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'milex.core.value.required',
                        ]
                    ),
                ],
            ]
        );

        // Allows additional values (new events) to be selected before persisting
        $builder->get('jumpToEvent')->resetViewTransformers();
    }

    public function getBlockPrefix()
    {
        return 'campaignevent_jump_to_event';
    }
}
