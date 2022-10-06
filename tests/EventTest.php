<?php declare(strict_types=1);

use Otomaties\Events\FormField;
use PHPUnit\Framework\TestCase;
use Otomaties\WpModels\PostType;
use Otomaties\Events\Models\Event;
use Otomaties\Events\Models\TicketType;
use Otomaties\WpModels\Collection;
use Otomaties\WpModels\Exceptions\InvalidPostTypeException;

final class EventTest extends TestCase
{

    protected static $event;

    public static function setUpBeforeClass() : void
    {
        self::$event = new Event(420);
    }

    public function testCanBeCreatedFromId() : void
    {
        $event = new Event(420);
        $this->assertInstanceOf(
            Event::class,
            $event
        );
        $this->assertEquals(420, $event->getId());
    }

    public function testCanBeCreatedFromString() : void
    {
        $event = new Event('event_420');
        $this->assertInstanceOf(
            Event::class,
            $event
        );
        $this->assertEquals(420, $event->getId());
    }

    public function testCanBeCreatedFromWpPost() : void
    {
        $post = new WP_Post();
        $event = new Event($post);
        $this->assertInstanceOf(
            PostType::class,
            $event
        );
        $this->assertEquals(420, $event->getId());
    }

    public function testIfInvalidPostTypeExceptionIsThrown() : void
    {
        $this->expectException(InvalidPostTypeException::class);
        new Event(987);
    }

    public function testIfIdIsCorrect() : void
    {
        $this->assertEquals(420, self::$event->getId());
    }

    public function testIfToStringReturnsId() : void
    {
        $this->assertEquals(420, (string)self::$event);
    }

    public function testTitleIsCorrect() : void
    {
        $this->assertEquals('Title of post', self::$event->title());
    }

    public function testEventDateReturnsDatetime() : void
    {
        $this->assertInstanceOf(
            DateTime::class,
            self::$event->eventDate()
        );
    }

    public function testEventDateIsCorrect() : void
    {
        $this->assertEquals('12-01-2022 13:00', self::$event->eventDate()->format('d-m-Y H:i'));
        $this->assertEquals('13-01-2022 18:00', self::$event->eventDate('to')->format('d-m-Y H:i'));
    }

    public function testFormattedEventDateIsCorrect() : void
    {
        $this->assertEquals('12 January 2022 - 13 January 2022', self::$event->formattedEventDate());
        $this->assertEquals('12 January 2022 13:00 - 13 January 2022 18:00', self::$event->formattedEventDate(true));
        $this->assertEquals('12-01-2022 - 13-01-2022', self::$event->formattedEventDate(false, 'd-m-Y'));
        $this->assertEquals('12-01-2022 13 00 00 - 13-01-2022 18 00 00', self::$event->formattedEventDate(true, 'd-m-Y', 'H i s'));
    }

    public function testEventTimeIsCorrect() : void
    {
        $this->assertEquals('13:00', self::$event->eventTime());
        $this->assertEquals('18:00', self::$event->eventTime('to'));
    }

    public function testTicketTypesDefined() : void
    {
        $this->assertCount(2, self::$event->ticketTypes());
    }

    public function testTicketTypesInstancesOfTicketType() : void
    {
        $ticketTypes = self::$event->ticketTypes();
        $this->assertInstanceOf(
            TicketType::class,
            $ticketTypes[0]
        );
    }

    public function testIfTicketTypeCanBeFetchedBySlug() : void
    {
        $this->assertInstanceOf(
            TicketType::class,
            self::$event->ticketType('adult')
        );
        $this->assertNull(self::$event->ticketType('unexisting'));
    }

    public function testCountSoldTickets() : void
    {
        $this->assertEquals(
            999 * 4 + 999 * 1,
            self::$event->soldTickets()
        );
    }

    public function testIfEventHasExtraFormField() : void
    {
        $this->assertCount(1, self::$event->extraFormFields());
    }

    public function testExtraFormFieldsInstancesOfFormField() : void
    {
        $extraFormFields = self::$event->extraFormFields();
        $this->assertInstanceOf(
            FormField::class,
            $extraFormFields[0]
        );
    }

    public function testFormFieldCanBeFetchedBySlug() : void
    {
        $this->assertInstanceOf(
            FormField::class,
            self::$event->extraFormField('remark')
        );
        $this->assertNull(self::$event->extraFormField('unexisting'));
    }

    public function testRegistrationsCanBeFetched() : void
    {
        $this->assertCount(999, self::$event->registrations());
        $this->assertInstanceOf(
            Collection::class,
            self::$event->registrations()
        );
    }

    public function testIfRegistrationsAreOpen() : void
    {
        $this->assertTrue(self::$event->registrationsOpen());
    }

    public function testGetRegistrationLimit() : void
    {
        $this->assertEquals(500, self::$event->registrationLimit());
    }

    public function testFreeSpot() : void
    {
        $this->assertEquals(500 - (999 * 4) - (999 * 1), self::$event->freeSpots());
    }

    public function testPostTypeIsCorrect()
    {
        $this->assertEquals('event', Event::postType());
    }

    public function testMergeFormFieldsReturnsBoolean()
    {
        $this->assertIsBool(self::$event->mergeFormFields());
    }

    public function testMergeFormFieldsReturnsFalse()
    {
        $this->assertFalse(self::$event->mergeFormFields());
    }

    public function testHideTicketsTitleReturnsBoolean()
    {
        $this->assertIsBool(self::$event->hideTicketsTitle());
    }

    public function testHideTicketsTitleReturnsTrue()
    {
        $this->assertTrue(self::$event->hideTicketsTitle());
    }
}
