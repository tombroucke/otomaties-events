<?php

namespace Otomaties\Events;

use Otomaties\Events\Models\Event;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class RegistrationExport
{
    /**
     * The event object
     *
     * @var Event
     */
    protected Event $event;

    /**
     * Constructor, save event in property
     *
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Download xlsx
     *
     * @return void
     */
    public function execute() : void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $this->data(),
                null,
                'A1'
            );

        $this->printHeaders();

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * Export content
     *
     * @return array<array<string>>
     */
    private function data() : array
    {
        $data = [];

        // Headers
        $headers = [
            __('First name', 'otomaties-events'),
            __('Last name', 'otomaties-events'),
            __('Phone', 'otomaties-events'),
            __('Email', 'otomaties-events')
        ];
        foreach ($this->event->extraFormFields() as $formField) {
            $headers[] = $formField->label();
        }
        foreach ($this->event->ticketTypes() as $ticketType) {
            $headers[] = $ticketType->title();
        }
        $data[] = $headers;

        // Body
        foreach ($this->event->registrations() as $registration) {
            // Default fields
            $entry = [
                esc_html($registration->meta()->get('first_name')),
                esc_html($registration->meta()->get('last_name')),
                esc_html($registration->meta()->get('phone')),
                esc_html($registration->meta()->get('email'))
            ];

            // Extra fields
            $registrationExtraFields = $registration->meta()->get('extra_fields');
            foreach ($this->event->extraFormFields() as $formField) {
                if (is_array($registrationExtraFields) && isset($registrationExtraFields[$formField->slug()])) {
                    $entry[] = esc_html($registrationExtraFields[$formField->slug()]);
                } else {
                    $entry[] = '';
                }
            }

            // tickets
            $tickets = $registration->tickets();
            foreach ($this->event->ticketTypes() as $ticketType) {
                if (is_array($tickets) && isset($tickets[$ticketType->slug()])) {
                    $entry[] = filter_var($tickets[$ticketType->slug()], FILTER_SANITIZE_NUMBER_INT);
                } else {
                    $entry[] = '';
                }
            }
            $data[] = $entry;
        }
        return $data;
    }

    /**
     * Print headers for output
     *
     * @return void
     */
    private function printHeaders() : void
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header(sprintf('Content-Disposition: attachment;filename="%s.xlsx"', $this->event->name()));
        header('Cache-Control: max-age=0');
    }
}
