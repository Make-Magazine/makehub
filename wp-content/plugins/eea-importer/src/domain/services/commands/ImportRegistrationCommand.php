<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Line_Item;
use EE_Transaction;
use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigBase;

/**
 * Class CreateAttendeeCommand
 * DTO for passing data to a ImportRegistrationCommandHandler
 *
 * @package       Event Espresso
 * @author        Michael Nelson
 */
class ImportRegistrationCommand extends ImportSingleModelBase
{

    /**
     * an existing registration to associate this attendee with
     *
     * @var EE_Transaction $transaction
     */
    protected $transaction;
    /**
     * @var EE_Line_Item
     */
    private $line_Item;

    public function __construct(
        EE_Transaction $transaction,
        EE_Line_Item $line_Item,
        array $input_data,
        ImportModelConfigBase $config
    ) {
        $this->transaction = $transaction;
        parent::__construct($input_data, $config);
        $this->line_Item = $line_Item;
    }

    /**
     * @return EE_Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return EE_Line_Item
     */
    public function getLineItem()
    {
        return $this->line_Item;
    }
}
