<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Registration;
use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportAttendeeConfig;

/**
 * Class CreateAttendeeCommand
 * DTO for passing data to a ImportAttendeeCommandHandler
 *
 * @package       Event Espresso
 * @author        Michael Nelson
 */
class ImportAttendeeCommand extends ImportSingleModelBase
{

    /**
     * @var EE_Registration
     */
    private $registration;

    public function __construct(
        EE_Registration $reg,
        array $input_data,
        ImportAttendeeConfig $config
    ) {
        parent::__construct($input_data, $config);
        $this->registration = $reg;
    }

    /**
     * @return EE_Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }
}
