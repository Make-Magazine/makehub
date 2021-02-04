<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Ticket;
use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigBase;

/**
 * Class CreateAttendeeCommand
 * DTO for passing data to a ImportTransactionCommandHandler
 *
 * @package       Event Espresso
 * @author        Michael Nelson
 */
class ImportTransactionCommand extends ImportSingleModelBase
{
    /**
     * @var EE_Ticket
     */
    private $ticket;

    public function __construct(
        EE_Ticket $ticket,
        array $input_data,
        ImportModelConfigBase $config
    ) {
        parent::__construct($input_data, $config);
        $this->ticket = $ticket;
    }

    /**
     * @return EE_Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}
