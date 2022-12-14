<?php

namespace Milex\SmsBundle\Tests\EventListener;

use Milex\CoreBundle\Event\TokenReplacementEvent;
use Milex\CoreBundle\Model\AuditLogModel;
use Milex\LeadBundle\Entity\Lead;
use Milex\PageBundle\Entity\Trackable;
use Milex\PageBundle\Helper\TokenHelper;
use Milex\PageBundle\Model\TrackableModel;
use Milex\SmsBundle\EventListener\SmsSubscriber;
use Milex\SmsBundle\Helper\SmsHelper;
use PHPUnit\Framework\TestCase;

class SmsSubscriberTest extends TestCase
{
    private $messageText = 'custom http://milex.com text';

    private $messageUrl = 'http://milex.com';

    public function testOnTokenReplacementWithTrackableUrls()
    {
        $mockAuditLogModel = $this->createMock(AuditLogModel::class);

        $mockTrackableModel = $this->createMock(TrackableModel::class);
        $mockTrackableModel->expects($this->any())->method('parseContentForTrackables')->willReturn([
            $this->messageUrl,
            new Trackable(),
        ]);
        $mockTrackableModel->expects($this->any())->method('generateTrackableUrl')->willReturn('custom');

        $mockPageTokenHelper = $this->createMock(TokenHelper::class);
        $mockPageTokenHelper->expects($this->any())->method('findPageTokens')->willReturn([]);

        $mockAssetTokenHelper = $this->createMock(\Milex\AssetBundle\Helper\TokenHelper::class);
        $mockAssetTokenHelper->expects($this->any())->method('findAssetTokens')->willReturn([]);

        $mockSmsHelper = $this->createMock(SmsHelper::class);
        $mockSmsHelper->expects($this->any())->method('getDisableTrackableUrls')->willReturn(false);

        $lead                  = new Lead();
        $tokenReplacementEvent = new TokenReplacementEvent($this->messageText, $lead, ['channel' => [1 => 'sms']]);
        $subscriber            = new SmsSubscriber(
            $mockAuditLogModel,
            $mockTrackableModel,
            $mockPageTokenHelper,
            $mockAssetTokenHelper,
            $mockSmsHelper
        );
        $subscriber->onTokenReplacement($tokenReplacementEvent);
        $this->assertNotSame($this->messageText, $tokenReplacementEvent->getContent());
    }

    public function testOnTokenReplacementWithDisableTrackableUrls()
    {
        $mockAuditLogModel = $this->createMock(AuditLogModel::class);

        $mockTrackableModel = $this->createMock(TrackableModel::class);
        $mockTrackableModel->expects($this->any())->method('parseContentForTrackables')->willReturn([
            $this->messageUrl,
            new Trackable(),
        ]);
        $mockTrackableModel->expects($this->any())->method('generateTrackableUrl')->willReturn('custom');

        $mockPageTokenHelper = $this->createMock(TokenHelper::class);
        $mockPageTokenHelper->expects($this->any())->method('findPageTokens')->willReturn([]);

        $mockAssetTokenHelper = $this->createMock(\Milex\AssetBundle\Helper\TokenHelper::class);
        $mockAssetTokenHelper->expects($this->any())->method('findAssetTokens')->willReturn([]);

        $mockSmsHelper = $this->createMock(SmsHelper::class);
        $mockSmsHelper->expects($this->any())->method('getDisableTrackableUrls')->willReturn(true);

        $lead                  = new Lead();
        $tokenReplacementEvent = new TokenReplacementEvent($this->messageText, $lead, ['channel' => ['sms', 1]]);
        $subscriber            = new SmsSubscriber(
            $mockAuditLogModel,
            $mockTrackableModel,
            $mockPageTokenHelper,
            $mockAssetTokenHelper,
            $mockSmsHelper
        );
        $subscriber->onTokenReplacement($tokenReplacementEvent);
        $this->assertSame($this->messageText, $tokenReplacementEvent->getContent());
    }
}
