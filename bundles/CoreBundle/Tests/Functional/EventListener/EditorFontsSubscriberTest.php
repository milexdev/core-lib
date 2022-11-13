<?php

declare(strict_types=1);

namespace Milex\CoreBundle\Tests\Functional\EventListener;

use Milex\CoreBundle\Test\MilexMysqlTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;

class EditorFontsSubscriberTest extends MilexMysqlTestCase
{
    protected function setUp(): void
    {
        $this->configParams['editor_fonts'] = [
            [
                'name' => 'Arial',
                'font' => 'Arial, Helvetica, sans-serif',
                'url'  => 'https://custom-font.test/arial.css',
            ],
            [
                'name' => 'Courier New',
                'font' => 'Courier New, Courier, monospace',
                'url'  => 'https://custom-font.test/courier.css',
            ],
        ];

        parent::setUp();
    }

    public function testEditorFontsAreLoadedWithDefinedConfigValues(): void
    {
        $crawler  = $this->client->request(Request::METHOD_GET, '/');
        $response = $crawler->html();

        Assert::assertTrue($this->client->getResponse()->isOk());

        Assert::assertStringContainsString(
            'https://custom-font.test/arial.css',
            $response
        );

        Assert::assertStringContainsString(
            'https://custom-font.test/courier.css',
            $response
        );
    }
}
