<?php

namespace Milex\LeadBundle\Tests\Entity;

use Milex\LeadBundle\Entity\Import;
use Milex\LeadBundle\Tests\StandardImportTestHelper;

class ImportTest extends StandardImportTestHelper
{
    public function testSetPath()
    {
        $import = $this->initImportEntity();

        $this->assertSame(self::$csvPath, $import->getFilePath());
    }

    public function testCanProceed()
    {
        $import = $this->initImportEntity();

        $this->assertTrue($import->canProceed());
    }

    public function testIsBackgroundProcess()
    {
        $import = $this->initImportEntity();

        $this->assertTrue($import->isBackgroundProcess());

        $import->setStatus(Import::MANUAL);

        $this->assertFalse($import->isBackgroundProcess());
    }

    public function testIncreaseInsertedCount()
    {
        $count  = 4;
        $import = $this->initImportEntity();
        $import->setInsertedCount($count);

        $this->assertSame($count, $import->getInsertedCount());

        $import->increaseInsertedCount();

        $this->assertSame(5, $import->getInsertedCount());
    }

    public function testIncreaseUpdatedCount()
    {
        $count  = 4;
        $import = $this->initImportEntity();
        $import->setUpdatedCount($count);

        $this->assertSame($count, $import->getUpdatedCount());

        $import->increaseUpdatedCount();

        $this->assertSame(5, $import->getUpdatedCount());
    }

    public function testIncreaseIgnoredCount()
    {
        $count  = 4;
        $import = $this->initImportEntity();
        $import->setIgnoredCount($count);

        $this->assertSame($count, $import->getIgnoredCount());

        $import->increaseIgnoredCount();

        $this->assertSame(5, $import->getIgnoredCount());
    }

    public function testGetProcessedRows()
    {
        $count  = 4;
        $import = $this->initImportEntity();

        $this->assertSame(0, $import->getProcessedRows());

        $import->setIgnoredCount($count);
        $import->setUpdatedCount($count);
        $import->setInsertedCount($count);

        $this->assertSame(3 * $count, $import->getProcessedRows());

        $import->increaseIgnoredCount();
        $import->increaseIgnoredCount();

        $this->assertSame(3 * $count + 2, $import->getProcessedRows());
    }

    public function testGetProgressPercentage()
    {
        $import = $this->initImportEntity()
            ->setLineCount(100);

        $this->assertSame(0, $import->getProgressPercentage());

        $import->setIgnoredCount(3);

        $this->assertEquals(3, $import->getProgressPercentage());

        $import->increaseIgnoredCount()
            ->increaseIgnoredCount();

        $this->assertEquals(5, $import->getProgressPercentage());
    }

    public function testStart()
    {
        $import = $this->initImportEntity();

        $this->assertSame(Import::QUEUED, $import->getStatus());
        $this->assertNull($import->getDateStarted());

        // Date started will be set when the start is called for the first time.
        $import->start();

        $startDate = $import->getDateStarted();

        $this->assertSame(Import::IN_PROGRESS, $import->getStatus());
        $this->assertTrue($startDate instanceof \DateTime);

        // But the date started will not change when started for the second time.
        $import->end(false);
        $import->start();

        $this->assertSame($startDate, $import->getDateStarted());
    }

    public function testEnd()
    {
        $import = $this->initImportEntity();

        $this->assertSame(Import::QUEUED, $import->getStatus());
        $this->assertNull($import->getDateEnded());

        $import->start()->end(false);

        $this->assertSame(Import::IMPORTED, $import->getStatus());
        $this->assertTrue($import->getDateEnded() instanceof \DateTime);
    }

    public function testGetRunTime()
    {
        $import = $this->initImportEntity()->start();

        $this->assertNull($import->getRunTime());

        $import->end(false);

        $this->fakeImportStartDate($import, (10 * 60));

        $this->assertTrue($import->getRunTime() instanceof \DateInterval);
        $this->assertSame(10, $import->getRunTime()->i);
    }

    public function testGetRunTimeSeconds()
    {
        $import = $this->initImportEntity()->start();

        $this->assertSame(0, $import->getRunTimeSeconds());

        $import->end(false);

        $this->fakeImportStartDate($import, 600);

        $this->assertSame(600, $import->getRunTimeSeconds());
    }

    public function testGetSpeed()
    {
        $import = $this->initImportEntity()->start();

        $this->assertSame(0, $import->getSpeed());

        $import->setInsertedCount(900);
        $import->end(false);

        $this->fakeImportStartDate($import, 600);

        $this->assertSame(1.5, $import->getSpeed());
    }

    public function testGetSpeedWhenRunTimeIsUnderOneSecond()
    {
        $import = $this->initImportEntity()->start();

        $this->assertSame(0, $import->getSpeed());

        $import->setInsertedCount(3);
        $import->end(false);

        $this->assertSame(3, $import->getSpeed());
    }

    /**
     * Fake the start date to the past to emulate that the import runs for a while.
     *
     * @param int $runtime in seconds
     */
    protected function fakeImportStartDate(Import $import, $runtime = 600)
    {
        $dateEnded   = $import->getDateEnded();
        $dateStarted = new \DateTime($dateEnded->format('Y-m-d H:i:s.u'), $dateEnded->getTimezone());
        $dateStarted->modify('-'.$runtime.' seconds');
        $import->setDateStarted($dateStarted);
    }
}
