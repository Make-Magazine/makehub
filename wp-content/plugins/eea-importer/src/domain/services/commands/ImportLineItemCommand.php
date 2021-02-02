<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Ticket;
use EE_Transaction;
use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigBase;

/**
 * Class CreateAttendeeCommand
 * DTO for passing data to a ImportTransactionCommandHandler
 *
 * @package       Event Espresso
 * @author        Michael Nelson
 */
class ImportLineItemCommand extends ImportSingleModelBase
{
    /**
     * @var EE_Ticket
     */
    private $ticket;
    /**
     * @var EE_Transaction
     */
    private $transaction;

    public function __construct(
        EE_Transaction $transaction,
        EE_Ticket $ticket,
        array $input_data,
        ImportModelConfigBase $config
    ) {
        parent::__construct($input_data, $config);
        $this->ticket = $ticket;
        $this->transaction = $transaction;
    }

    /**
     * @return EE_Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @return EE_Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
