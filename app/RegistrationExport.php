<?php

namespace Otomaties\Events;

use Otomaties\Events\Models\Event;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RegistrationExport
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function execute()
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

    private function data()
    {
        $data = [];

        // Headers
        $headers = [
            'first_name',
            'last_name',
            'phone',
            'email'
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
                esc_html($registration->get('first_name')),
                esc_html($registration->get('last_name')),
                esc_html($registration->get('phone')),
                esc_html($registration->get('email'))
            ];

            // Extra fields
            $registrationExtraFields = $registration->get('extra_fields');
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
                    $entry[] = esc_html($tickets[$ticketType->slug()]);
                } else {
                    $entry[] = '';
                }
            }
            $data[] = $entry;
        }

        return $data;
    }

    private function printHeaders()
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header(sprintf('Content-Disposition: attachment;filename="%s.xlsx"', $this->event->name()));
        header('Cache-Control: max-age=0');
    }
}
