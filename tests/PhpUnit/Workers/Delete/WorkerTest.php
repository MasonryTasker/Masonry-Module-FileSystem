<?php
/**
 * Worker.php
 * PHP version 5.4
 * 2015-10-01
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Builder\Tests\PhpUnit\Workers\FileSystem\Delete;

use Foundry\Masonry\Module\FileSystem\FileSystem;
use Foundry\Masonry\Module\FileSystem\Tests\PhpUnit\FileSystemTestTrait;
use Foundry\Masonry\Module\FileSystem\Workers\Delete\Worker;
use Foundry\Masonry\Module\FileSystem\Workers\Delete\Description;
use Foundry\Masonry\Core\Task;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractWorkerTest;
use Foundry\Masonry\Tests\PhpUnit\DeferredWrapper;

/**
 * Class WorkerTest
 * @coversDefaultClass Foundry\Masonry\Module\FileSystem\Workers\Delete\Worker
 * @package Foundry\Masonry-Website-Builder
 */
class WorkerTest extends AbstractWorkerTest
{

    use FileSystemTestTrait;

    /**
     * @return Worker
     */
    protected function getTestSubject()
    {
        return new Worker();
    }

    /**
     * @return string[]
     */
    protected function getDescriptionTypes()
    {
        return [
            Description::class
        ];
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Delete\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredSuccess()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testFile = 'schema://root/test';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('isFile')
            ->with($testFile)
            ->will($this->returnValue(false));
        $fileSystem
            ->expects($this->once())
            ->method('isDirectory')
            ->with($testFile)
            ->will($this->returnValue(true));
        $fileSystem
            ->expects($this->once())
            ->method('delete')
            ->with($testFile)
            ->will($this->returnValue(true));

        $description = new Description($testFile);
        $task = new Task($description);
        $worker = $this->getTestSubject();
        $worker->setFileSystem($fileSystem);

        $processDeferred = $this->getObjectMethod($worker, 'processDeferred');

        /** @var \Generator $generator */
        $generator = $processDeferred($deferredWrapper->getDeferred(), $task);
        while ($generator->valid()) {
            $generator->next();
        }

        // Test messages
        $this->assertSame(
            "Deleted file or directory '{$testFile}'",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Deleting file or directory '{$testFile}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Delete\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredFailure()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testFile = 'schema://root/test';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('isFile')
            ->with($testFile)
            ->will($this->returnValue(true));
        $fileSystem
            ->expects($this->once())
            ->method('delete')
            ->with($testFile)
            ->will($this->returnValue(false));

        $description = new Description($testFile);
        $task = new Task($description);
        $worker = $this->getTestSubject();
        $worker->setFileSystem($fileSystem);

        $processDeferred = $this->getObjectMethod($worker, 'processDeferred');

        /** @var \Generator $generator */
        $generator = $processDeferred($deferredWrapper->getDeferred(), $task);
        while ($generator->valid()) {
            $generator->next();
        }

        // Test messages
        $this->assertSame(
            "",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "File or directory '{$testFile}' could not be deleted",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Deleting file or directory '{$testFile}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Delete\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredSkip()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testFile = 'schema://root/test';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('isFile')
            ->with($testFile)
            ->will($this->returnValue(false));
        $fileSystem
            ->expects($this->once())
            ->method('isDirectory')
            ->with($testFile)
            ->will($this->returnValue(false));

        $description = new Description($testFile);
        $task = new Task($description);
        $worker = $this->getTestSubject();
        $worker->setFileSystem($fileSystem);

        $processDeferred = $this->getObjectMethod($worker, 'processDeferred');

        /** @var \Generator $generator */
        $generator = $processDeferred($deferredWrapper->getDeferred(), $task);
        while ($generator->valid()) {
            $generator->next();
        }

        // Test messages
        $this->assertSame(
            "File or directory '{$testFile}' does not exist",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Deleting file or directory '{$testFile}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }
}