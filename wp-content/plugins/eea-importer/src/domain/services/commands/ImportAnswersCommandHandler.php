<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Answer;
use EE_Error;
use EEM_Question;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config\ImportCsvAttendeesConfig;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\commands\CommandHandler;
use EventEspresso\core\services\commands\CommandInterface;
use InvalidArgumentException;
use ReflectionException;

/**
 * Class ImportAnswersCommandHandler
 * Creates the answers to custom questions for the command's registration based on its data.
 *
 * @package       Event Espresso
 * @author        Michal Nelson
 */
class ImportAnswersCommandHandler extends CommandHandler
{
    /**
     * @var ImportCsvAttendeesConfig
     */
    private $config;


    /**
     * @param ImportCsvAttendeesConfig $config
     */
    public function __construct(
        ImportCsvAttendeesConfig $config
    ) {
        $this->config = $config;
    }


    /**
     * @param CommandInterface|ImportAnswersCommand $command
     * @return array
     * @throws EE_Error
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function handle(CommandInterface $command)
    {
        $answers = [];
        foreach ($this->config->getQuestionMapping() as $question_id => $csv_column) {
            $question = EEM_Question::instance()->get_one_by_ID($question_id);
            $answer = $command->valueFromInput($csv_column);
            if (EEM_Question::instance()->question_type_is_in_category($question->type(), 'multi-answer-enum')) {
                $answer = array_map(
                    'trim',
                    explode('|', $answer)
                );
            }
            $answer = EE_Answer::new_instance(
                [
                    'REG_ID' => $command->getRegistration()->ID(),
                    'QST_ID' => $question_id,
                    'ANS_value' => $answer
                ]
            );
            $answer->save();
            $answers[] = $answer;
        }
        return $answers;
    }
}
