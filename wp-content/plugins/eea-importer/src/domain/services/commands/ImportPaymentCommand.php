<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Transaction;
use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigBase;

/**
 * Class CreateAttendeeCommand
 * DTO for passing data to a ImportPaymentCommandHandler
 *
 * @package       Event Espresso
 * @author        Michael Nelson
 */
class ImportPaymentCommand extends ImportSingleModelBase
{

    /**
     * an existing registration to associate this attendee with
     *
     * @var EE_Transaction $transaction
     */
    protected $transaction;

    public function __construct(
        EE_Transaction $transaction,
        array $input_data,
        ImportModelConfigBase $config
    ) {
        parent::__construct($input_data, $config);
        $this->transaction = $transaction;
    }

    /**
     * @return EE_Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
