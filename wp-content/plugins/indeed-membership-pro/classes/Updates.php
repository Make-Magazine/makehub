<?php
namespace Indeed\Ihc;
/*
 * @since 7.4
 */

class Updates
{

    private $optionName = 'ihc_plugin_current_version';

    public function __construct()
    {
        add_action( 'init', array( $this, 'check' ) );
    }

    public function check()
    {
        $currentVersion = indeed_get_plugin_version( IHC_PATH . 'indeed-membership-pro.php' );
        $versionValueInDatabase = get_option( $this->optionName );
        if ( !$versionValueInDatabase ){
            $versionValueInDatabase = '7.3';
        }

        if ( version_compare( '8', $versionValueInDatabase )==1 ){
            $this->addIndexes();
        }

        if ( version_compare( $currentVersion, $versionValueInDatabase )==1 ){
            $this->updateRegisterFields();
            update_option( $this->optionName, $currentVersion );
        }

        if ( version_compare( '8.7', $versionValueInDatabase )==1 ){
            $this->removeCsvOldFiles();
            $this->removeOldExportFiles();
        }

    }

    public function updateRegisterFields()
    {
        $data = get_option( 'ihc_user_fields' );
        if ( !$data ){
            return false;
        }
        foreach ( $data as $fieldData ){
            if ( !isset( $fieldData['display_on_modal'] ) ){
                $fieldData['display_on_modal'] = 0;
            }
        }
        ///
        require_once IHC_PATH . 'admin/includes/functions/register.php'; /// double check this

        if ( ihc_array_value_exists( $data, 'ihc_optin_accept', 'name' ) === false ){
            $fieldData = array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'display_on_modal'=> 0, 'name'=>'ihc_optin_accept', 'label' => __( 'Accept Opt-in', 'ihc' ), 'type'=>'single_checkbox', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' );
            ihc_save_user_field($fieldData);
        }
        if ( ihc_array_value_exists( $data, 'ihc_memberlist_accept', 'name' ) === false ){
            $fieldData = array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'display_on_modal'=> 0, 'name'=>'ihc_memberlist_accept', 'label' => __( 'Accept display on Memberlist', 'ihc' ), 'type'=>'single_checkbox', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' );
            ihc_save_user_field($fieldData);
        }
    }

    /**
     * @since version 9
     * @param none
     * @return none
     */
    public function addIndexes()
    {
        $this->userLevelsIndex();
        $this->userLogsIndex();
        $this->membersPaymentsIndex();
        $this->ordersIndex();
        $this->orderMetaIndex();
    }

    /**
     * @since version 9
     * @param none
     * @return none
     */
    private function userLevelsIndex()
    {
        global $wpdb;
        $indexList = $wpdb->get_results( "SHOW INDEX FROM {$wpdb->prefix}ihc_user_levels;");
        if ( !$indexList ){
            return;
        }
        foreach ( $indexList as $indexObject ){
            if ( isset( $indexObject->Key_name ) && $indexObject->Key_name == 'idx_ihc_user_levels_user_id' ){
                return;
            }
        }
        $wpdb->query( "CREATE INDEX idx_ihc_user_levels_user_id ON {$wpdb->prefix}ihc_user_levels(user_id)" );
    }

    /**
     * @since version 9
     * @param none
     * @return none
     */
    private function userLogsIndex()
    {
        global $wpdb;
        $indexList = $wpdb->get_results( "SHOW INDEX FROM {$wpdb->prefix}ihc_user_logs;" );
        if ( !$indexList ){
            return;
        }
        foreach ( $indexList as $indexObject ){
            if ( isset( $indexObject->Key_name ) && $indexObject->Key_name == 'idx_ihc_user_logs_uid' ){
                return;
            }
        }
        $wpdb->query( "CREATE INDEX idx_ihc_user_logs_uid ON {$wpdb->prefix}ihc_user_logs(uid)" );
    }

    /**
     * @since version 9
     * @param none
     * @return none
     */
    private function membersPaymentsIndex()
    {
        global $wpdb;
        $indexList = $wpdb->get_results( "SHOW INDEX FROM {$wpdb->prefix}indeed_members_payments;" );
        if ( !$indexList ){
            return;
        }
        foreach ( $indexList as $indexObject ){
            if ( isset( $indexObject->Key_name ) && $indexObject->Key_name == 'idx_indeed_members_payments_uid' ){
                return;
            }
        }
        $wpdb->query( "CREATE INDEX idx_indeed_members_payments_uid ON {$wpdb->prefix}indeed_members_payments(u_id)" );
    }

    /**
     * @since version 9
     * @param none
     * @return none
     */
    private function ordersIndex()
    {
        global $wpdb;
        $indexList = $wpdb->get_results( "SHOW INDEX FROM {$wpdb->prefix}ihc_orders;" );
        if ( !$indexList ){
            return;
        }
        foreach ( $indexList as $indexObject ){
            if ( isset( $indexObject->Key_name ) && $indexObject->Key_name == 'idx_ihc_orders_uid' ){
                return;
            }
        }
        $wpdb->query( "CREATE INDEX idx_ihc_orders_uid ON {$wpdb->prefix}ihc_orders(uid)" );
    }

    /**
     * @since version 9
     * @param none
     * @return none
     */
    private function orderMetaIndex()
    {
        global $wpdb;
        $indexList = $wpdb->get_results( "SHOW INDEX FROM {$wpdb->prefix}ihc_orders_meta;" );
        if ( !$indexList ){
            return;
        }
        foreach ( $indexList as $indexObject ){
            if ( isset( $indexObject->Key_name ) && $indexObject->Key_name == 'idx_ihc_orders_meta_order_id' ){
                return;
            }
        }
        $wpdb->query( "CREATE INDEX idx_ihc_orders_meta_order_id ON {$wpdb->prefix}ihc_orders_meta(order_id)" );
    }

    /**
     * @param none
     * @return none
     */
    private function removeCsvOldFiles()
    {
        $directory = IHC_PATH;
        $files = scandir( $directory );
        foreach ( $files as $file ){
            $fileFullPath = $directory . $file;
            if ( file_exists( $fileFullPath ) && filetype( $fileFullPath ) == 'file' ){
                $extension = pathinfo( $fileFullPath, PATHINFO_EXTENSION );
        				if ( $extension == 'csv' && $file == 'users.csv' ){
                    unlink( $fileFullPath );
                }
            }
        }
    }

    /**
     * @param none
     * @return none
     */
    private function removeOldExportFiles()
    {
        $directory = IHC_PATH;
        $files = scandir( $directory );
        foreach ( $files as $file ){
            $fileFullPath = $directory . $file;
            if ( file_exists( $fileFullPath ) && filetype( $fileFullPath ) == 'file' ){
                $extension = pathinfo( $fileFullPath, PATHINFO_EXTENSION );
                if ( $extension == 'xml' && $file == 'export.xml' ){
                    unlink( $fileFullPath );
                }
            }
        }
    }

}
