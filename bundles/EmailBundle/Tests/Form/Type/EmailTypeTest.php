<?php

declare(strict_types=1);

namespace Milex\EmailBundle\Tests\Form\Type;

use Doctrine\ORM\EntityManager;
use Milex\CoreBundle\Helper\CoreParametersHelper;
use Milex\CoreBundle\Helper\ThemeHelperInterface;
use Milex\EmailBundle\Entity\Email;
use Milex\EmailBundle\Form\Type\EmailType;
use Milex\StageBundle\Model\StageModel;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EmailTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var MockObject|EntityManager
     */
    private $entityManager;

    /**
     * @var MockObject|StageModel
     */
    private $stageModel;

    /**
     * @var MockObject|FormBuilderInterface
     */
    private $formBuilder;

    /**
     * @var EmailType
     */
    private $form;

    /**
     * @var CoreParametersHelper|MockObject
     */
    private $coreParametersHelper;

    /**
     * @var ThemeHelperInterface|MockObject
     */
    private $themeHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator           = $this->createMock(TranslatorInterface::class);
        $this->entityManager        = $this->createMock(EntityManager::class);
        $this->stageModel           = $this->createMock(StageModel::class);
        $this->formBuilder          = $this->createMock(FormBuilderInterface::class);
        $this->coreParametersHelper = $this->createMock(CoreParametersHelper::class);
        $this->themeHelper          = $this->createMock(ThemeHelperInterface::class);
        $this->form                 = new EmailType(
            $this->translator,
            $this->entityManager,
            $this->stageModel,
            $this->coreParametersHelper,
            $this->themeHelper
        );

        $this->formBuilder->method('create')->willReturnSelf();
    }

    public function testBuildForm(): void
    {
        $options = ['data' => new Email()];
        $names   = [];
        $this->themeHelper
            ->expects($this->once())
            ->method('getCurrentTheme')
            ->with('blank', 'email')
            ->willReturn('blank');

        $this->formBuilder->method('add')
            ->with(
                $this->callback(
                    function ($name) use (&$names) {
                        $names[] = $name;

                        return true;
                    }
                )
            );

        $this->form->buildForm($this->formBuilder, $options);

        Assert::assertContains('buttons', $names);
    }
}
