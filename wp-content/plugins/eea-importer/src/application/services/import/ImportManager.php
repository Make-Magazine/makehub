<?php

namespace EventEspresso\AttendeeImporter\application\services\import;

use EventEspresso\core\services\collections\CollectionDetails;
use EventEspresso\core\services\collections\CollectionDetailsException;
use EventEspresso\core\services\collections\CollectionInterface;
use EventEspresso\core\services\collections\CollectionLoader;
use EventEspresso\core\services\collections\CollectionLoaderException;
use EventEspresso\core\services\loaders\Loader;

/**
 * Class ImportManager
 *
 * Is aware of what different Event Espresso importers are available.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportManager
{
    protected $loader;
    /**
     * @var CollectionInterface|ImportTypeUiManagerInterface[]
     */
    protected $managers;
    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Gets all the import type ui managers that the current user can use.
     * @since 1.0.0.p
     * @return CollectionInterface|ImportTypeUiManagerInterface[]
     * @throws CollectionLoaderException
     * @throws CollectionDetailsException
     */
    protected function loadImportTypeUiManagers()
    {
        $loader = new CollectionLoader(
            new CollectionDetails(
                // collection name
                'import_type_ui_managers',
                // collection interface
                'EventEspresso\AttendeeImporter\application\services\import\ImportTypeUiManagerInterface',
                // FQCNs for classes to add (all classes within that namespace will be loaded)

                array('EventEspresso\AttendeeImporter\domain\services\import\managers\ui'),
                // filepaths to classes to add
                //                $import_source_types,
                array(),
                // file mask to use if parsing folder for files to add
                //                '*UiManager.php',
                '',
                // what to use as identifier for collection entities
                // using CLASS NAME prevents duplicates (works like a singleton)
                CollectionDetails::ID_CALLBACK_METHOD,
                'getSlug'
            )
        );
        $this->managers = $loader->getCollection();
        foreach ($this->managers as $manager) {
            if (! current_user_can($manager->getImportType()->cap())) {
                $this->managers->remove($manager);
            }
        }
        return $this->managers;
    }

    /**
     * Gets import type managers
     * @since 1.0.0.p
     * @return CollectionInterface|ImportTypeManagerInterface[]
     * @throws CollectionDetailsException
     * @throws CollectionLoaderException
     */
    public function getImportTypeUiManagers()
    {
        if (! $this->managers instanceof CollectionInterface) {
            $this->loadImportTypeUiManagers();
        }
        return $this->managers;
    }

    /**
     * @since 1.0.0.p
     * @param $slug
     * @return ImportTypeUiManagerInterface
     * @throws CollectionLoaderException
     */
    public function getUiManager($slug)
    {
        $collection = $this->getImportTypeUiManagers();
        return $collection->get($slug);
    }
}
// End of file ImportManager.php
// Location: EventEspresso\core\domain\services\import/ImportManager.php
