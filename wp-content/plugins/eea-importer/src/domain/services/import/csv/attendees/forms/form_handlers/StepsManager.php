<?php
namespace EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers;

use EE_Request;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidIdentifierException;
use EventEspresso\core\libraries\form_sections\form_handlers\FormHandler;
use EventEspresso\core\libraries\form_sections\form_handlers\SequentialStepFormManager;
use EventEspresso\core\services\collections\Collection;
use EventEspresso\core\services\collections\CollectionDetails;
use EventEspresso\core\services\collections\CollectionDetailsException;
use EventEspresso\core\services\collections\CollectionInterface;
use EventEspresso\core\services\collections\CollectionLoader;
use EventEspresso\core\services\collections\CollectionLoaderException;
use InvalidArgumentException;

/**
 * Class StesManager
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class StepsManager extends SequentialStepFormManager
{

    /**
     * StepsManager constructor
     *
     * @param string      $base_url
     * @param string      $default_form_step
     * @param string      $form_action
     * @param string      $form_config
     * @param string      $progress_step_style
     * @param EE_Request $request
     * @throws InvalidDataTypeException
     * @throws InvalidArgumentException
     */
    public function __construct(
        $base_url,
        $default_form_step,
        $form_action = '',
        $form_config = FormHandler::ADD_FORM_TAGS_AND_SUBMIT,
        $progress_step_style = 'number_bubbles',
        EE_Request $request = null
    ) {
        parent::__construct(
            $base_url,
            $default_form_step,
            $form_action,
            $form_config,
            $progress_step_style,
            $request
        );
    }


    /**
     * @return Collection|null
     * @throws CollectionDetailsException
     * @throws CollectionLoaderException
     */
    protected function getFormStepsCollection()
    {
        if (! $this->form_steps instanceof CollectionInterface) {
            $loader = new CollectionLoader(
                new CollectionDetails(
                    // collection name
                    'importer_form_steps',
                    // collection interface
                    'EventEspresso\core\libraries\form_sections\form_handlers\SequentialStepForm',
                    // FQCNs for classes to add
                    apply_filters(
                        // @codingStandardsIgnoreStart
                        'FHEE__EventEspresso\AttendeeImporter\core\domain\services\import\csv\attendees\forms\form_handlers\StepsManager__getFormStepsCollection__form_step_classes',
                        // @codingStandardsIgnoreEnd
                        array(
                            'EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers\ChooseEvent',
                            'EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers\ChooseTicket',
                            'EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers\UploadCsv',
                            'EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers\MapCsvColumns',
                            'EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers\Verify',
                            'EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers\Complete',
                        )
                    ),
                    // filepaths to classes to add
                    array(),
                    // filemask to use if parsing folder for files to add
                    '',
                    // what to use as identifier for collection entities
                    CollectionDetails::ID_CALLBACK_METHOD,
                    // we'll use the slug() method on our collection objects for setting the identifier
                    'slug'
                )
            );
            $this->form_steps = $loader->getCollection();
        }
        return $this->form_steps;
    }


    /**
     * @return CollectionInterface|ImportCsvAttendeesStep[]
     * @throws CollectionDetailsException
     * @throws CollectionLoaderException
     * @since 1.0.0.p
     */
    public function getSteps()
    {
        $this->getFormStepsCollection();
        // Don't return the original steps collection as when we loop through it, it borks later logic.
        return $this->form_steps;
    }


    /**
     * @throws InvalidDataTypeException
     * @throws InvalidIdentifierException
     * @since 1.0.0.p
     */
    public function setCurrentStepFromRequest()
    {
        parent::setCurrentStepFromRequest();
    }
}
// End of file StesManager.php
// Location: EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers/StepsManager.php
