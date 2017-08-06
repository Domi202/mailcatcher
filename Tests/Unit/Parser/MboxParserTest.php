<?php
namespace Domi202\Mailcatcher\Tests\Unit\Parser\MboxParser;

use Domi202\Mailcatcher\Object\Mail;
use Domi202\Mailcatcher\Parser\MboxParser;
use Domi202\Mailcatcher\Tests\Unit\UnitTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * @coversDefaultClass \Domi202\Mailcatcher\Parser\MboxParser
 * @covers ::<!public>
 */
class MboxParserTest extends UnitTestCase
{
    /**
     * @var string
     */
    protected $testPath;

    /**
     * @var vfsStreamFile
     */
    protected $testFile;

    /**
     * @var MboxParser
     */
    protected $subject;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockFileSystem();

        $this->subject = new MboxParser($this->testPath);
    }

    /**
     * @return void
     */
    protected function mockFileSystem()
    {
        $root = vfsStream::setup();

        $this->testFile = new vfsStreamFile('exampleFile.mbox');
        $root->addChild($this->testFile);
        $this->testPath = $root->url() . DIRECTORY_SEPARATOR . 'exampleFile.mbox';
    }

    /**
     * @test
     * @covers ::__construct
     * @return void
     */
    public function throwsExceptionIfFileDoesNotExists()
    {
        $this->expectException(\RuntimeException::class);

        $this->subject = new MboxParser('file/does/not/exist.mbox');
    }

    /**
     * @test
     * @covers ::__construct
     * @return void
     */
    public function canBeConstructed()
    {
        $this->subject = new MboxParser($this->testPath);

        $this->assertAttributeInternalType(
            'string',
            'filePath',
            $this->subject
        );
        $this->assertAttributeInternalType(
            'resource',
            'file',
            $this->subject
        );
    }

    /**
     * @test
     * @covers ::getMails
     * @return void
     */
    public function canReturnMessages()
    {
        $mailDummy1 = $this->createMock(Mail::class);
        $mailDummy2 = $this->createMock(Mail::class);
        $mailArray = [
            $mailDummy1,
            $mailDummy2
        ];
        ObjectAccess::setProperty(
            $this->subject,
            'mails',
            $mailArray,
            true
        );

        $result = $this->subject->getMails();

        $this->assertEquals(
            $mailArray,
            $result
        );
    }
}
