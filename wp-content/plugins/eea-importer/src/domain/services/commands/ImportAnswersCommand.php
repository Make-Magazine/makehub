<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Registration;

/**
 * Class ImportAnswersCommand
 * DTO for passing data to a ImportAnswersCommandHandler
 *
 * @package       Event Espresso
 * @author        Michael Nelson
 */
class ImportAnswersCommand extends ImportBaseCommand
{

    /**
     * an existing registration to associate this attendee with
     *
     * @var EE_Registration $registration
     */
    protected $registration;


    /**
     * CreateAttendeeCommand constructor.
     *
     * @param EE_Registration $registration
     * @param array $input_data
     */
    public function __construct(EE_Registration $registration, array $input_data)
    {
        $this->registration = $registration;
        parent::__construct($input_data);
    }

    /**
     * @return EE_Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }
}
