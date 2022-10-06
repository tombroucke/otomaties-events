<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Otomaties\Events\Models\Location;

final class LocationTest extends TestCase
{

    protected static $location;

    public static function setUpBeforeClass() : void
    {
        self::$location = new Location(42);
    }

    public function testPostTypeIsCorrect()
    {
        $this->assertEquals('location', Location::postType());
    }

    public function testInformationIsCorrect()
    {
        $this->assertEquals('Location info', self::$location->information());
    }
}
