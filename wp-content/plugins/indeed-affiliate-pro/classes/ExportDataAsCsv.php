<?php
namespace Indeed\Uap;

class ExportDataAsCsv
{
    private $typeOfData             = '';
    private $linkToDownload         = '';

    public function __construct()
    {

    }

    public function setTypeOfData( $typeOfData='' )
    {
        $this->typeOfData = $typeOfData;
        return $this;
    }

    public function run()
    {
        switch ( $this->typeOfData ){
            case 'affiliates':
              $this->affiliates();
              break;
            case 'visits':
              $this->visits();
              break;
            case 'referrals':
              $this->referrals();
              break;
        }
        return $this;
    }

    private function affiliates()
    {
        global $indeed_db;
        $affiliates = $indeed_db->get_affiliates( -1, -1, false, '', '', [], 0 );
        if ( empty( $affiliates ) ){
            return;
        }

        $targetFile = UAP_PATH . 'affiliates.csv';
        $fileResource = fopen( $targetFile, 'w' );

        $data = [
                    __( 'Affiliate ID', 'uap' ),
                    __( 'UserName', 'uap' ),
                    __( 'Name', 'uap' ),
                    __( 'Rank', 'uap' ),
                    __( 'Visits', 'uap' ),
                    __( 'Referrals', 'uap' ),
                    __( 'Paid Amount', 'uap' ),
                    __( 'UnPaid Amount', 'uap' ),
                    __( 'Wp Role', 'uap' ),
                    __( 'Affiliate Since', 'uap' ),
        ];

        /// top of CSV file
        fputcsv( $fileResource, $data, ',' );
        unset( $data );

        $currency = get_option( 'uap_currency' );
        $ranksList = uap_get_wp_roles_list();
        foreach ( $affiliates as $affiliateId => $affiliate ){
            $data = [
                        $affiliateId,
                        $affiliate['username'],
                        $affiliate['name'],
                        $affiliate['email'],
                        $affiliate['rank_label'],
                        @$affiliate['stats']['visits'],
                        @$affiliate['stats']['referrals'],
                        uap_format_price_and_currency( $currency, @$affiliate['stats']['paid_payments_value'] ),
                        uap_format_price_and_currency( $currency, @$affiliate['stats']['unpaid_payments_value'] ),
                        (isset($ranksList[$affiliate['role']])) ? $ranksList[$affiliate['role']] : '',
                        uap_convert_date_to_us_format( $affiliate['start_data'] ),
            ];
            fputcsv( $fileResource, $data, "," );
            unset( $data );
        }

        fclose( $fileResource );
        $this->linkToDownload = UAP_URL . 'affiliates.csv';
    }

    private function visits()
    {
        global $indeed_db;
        $visits = $indeed_db->get_visits( -1, -1, false, '', '', [] );

        if ( empty( $visits ) ){
            return;
        }

        $targetFile = UAP_PATH . 'visits.csv';
        $fileResource = fopen( $targetFile, 'w' );

        $data = [
                  __( 'IP', 'uap' ),
                  __( 'Affiliate Username', 'uap' ),
                  __( 'Referral ID', 'uap' ),
                  __( 'URL', 'uap' ),
                  __( 'Browser', 'uap' ),
                  __( 'Device', 'uap' ),
                  __( 'Date', 'uap' ),
                  __( 'Status', 'uap' ),
        ];

        /// top of CSV file
        fputcsv( $fileResource, $data, ',' );
        unset( $data );

        foreach ( $visits as $visit ){
            $data = [
                      $visit['ip'],
                      empty( $visit['username'] ) ? __( 'Unknown', 'uap' ) : $visit['username'],
                      $visit['referral_id'],
                      $visit['url'],
                      $visit['browser'],
                      $visit['device'],
                      uap_convert_date_to_us_format( $visit['visit_date'] ),
                      empty( $visit['referral_id'] ) ? __('Just Visit', 'uap') : __('Converted', 'uap'),
            ];
            fputcsv( $fileResource, $data, "," );
            unset( $data );
        }

        fclose( $fileResource );
        $this->linkToDownload = UAP_URL . 'visits.csv';
    }

    private function referrals()
    {
        global $indeed_db;
        $referrals = $indeed_db->get_referrals( -1, -1, false, '', '', [] );

        if ( empty( $referrals ) ){
            return;
        }

        $targetFile = UAP_PATH . 'referrals.csv';
        $fileResource = fopen( $targetFile, 'w' );

        $data = [
                    __( 'User ID', 'uap' ),
                    __( 'Affiliate', 'uap' ),
                    __( 'ID', 'uap' ),
                    __( 'From', 'uap' ),
                    __( 'Reference', 'uap' ),
                    __( 'Description', 'uap' ),
                    __( 'Amount', 'uap' ),
                    __( 'Date', 'uap' ),
                    __( 'Status', 'uap' ),
        ];

        /// top of CSV file
        fputcsv( $fileResource, $data, ',' );
        unset( $data );

        foreach ( $referrals as $referral ){
            $status = __( 'Refuse', 'uap' );
            if ( $referral['status'] == 1 ){
                $status = __( 'Unverified', 'uap' );
            } else if ( $referral['status'] == 2 ){
                $status = __( 'Unverified', 'uap' );
            }
            $data = [
                      $indeed_db->get_uid_by_affiliate_id( $referral['affiliate_id'] ),
                      empty( $referral['username'] ) ? __( 'Unknown', 'uap' ) : $referral['username'],
                      $referral['id'],
                      uap_service_type_code_to_title( $referral['source'] ),
                      $referral['reference'],
                      $referral['description'],
                      uap_format_price_and_currency( $referral['currency'], $referral['amount'] ),
                      uap_convert_date_to_us_format( $referral['date'] ),
                      $status
            ];
            fputcsv( $fileResource, $data, "," );
            unset( $data );
        }

        fclose( $fileResource );
        $this->linkToDownload = UAP_URL . 'referrals.csv';
    }

    public function getDownloadLink()
    {
        return $this->linkToDownload;
    } 
}
