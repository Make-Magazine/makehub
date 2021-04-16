<?php
namespace Indeed\Uap;

class ExportDataAsCsv
{
    private $typeOfData             = '';
    private $linkToDownload         = '';
    private $filters                = [];

    public function __construct()
    {

    }

    public function setTypeOfData( $typeOfData='' )
    {
        $this->typeOfData = $typeOfData;
        return $this;
    }

    public function setFilters( $filters=[] )
    {
        $this->filters = $filters;
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

        $rank = 0;
        if ( !empty($this->filters['rank']) && $this->filters['rank'] > 0 ){
          $rank = $this->filters['rank'];
        }

        $affiliates = $indeed_db->get_affiliates( -1, -1, false, '', '', [], $rank );
        if ( empty( $affiliates ) ){
            return;
        }

        $this->removeOldFiles();
        $hash = bin2hex( random_bytes( 20 ) );
        $filename = $hash . '.csv';
        $targetFile = UAP_PATH . 'temporary/' . $filename;
        $fileResource = fopen( $targetFile, 'w' );

        $data = [
                    __( 'Affiliate ID', 'uap' ),
                    __( 'UserName', 'uap' ),
                    __( 'Name', 'uap' ),
                    __( 'Email', 'uap' ),
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
        $this->linkToDownload = UAP_URL . 'temporary/' . $filename;
    }

    private function visits()
    {
        global $indeed_db;
        $where = array();

        if ( !empty($this->filters['start']) && !empty($this->filters['end']) ){
          $where[] = " v.visit_date>'" . $this->filters['start'] . "' ";
          $where[] = " v.visit_date<'" . $this->filters['end'] . "' ";
        }

        if (isset($this->filters['status'])){
  				switch($this->filters['status']){
  					case '0':
  							$where[] = " v.referral_id = 0 ";
  							break;
  					case '1':
  							$where[] = " v.referral_id != 0 ";
  							break;
  				}
  			}
        if (!empty($this->filters['affiliate_username'])){
            $where[] = " ((u.user_login LIKE '%" . $this->filters['affiliate_username'] . "%') OR  (u.user_email LIKE '%" . $this->filters['affiliate_username'] . "%') )";
        }

        $visits = $indeed_db->get_visits( -1, -1, false, '', '', $where);

        if ( empty( $visits ) ){
            return;
        }

        $this->removeOldFiles();
        $hash = bin2hex( random_bytes( 20 ) );
        $filename = $hash . '.csv';
        $targetFile = UAP_PATH . 'temporary/' . $filename;
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
        $this->linkToDownload = UAP_URL . 'temporary/' . $filename;
    }

    private function referrals()
    {
        global $indeed_db;
        $where = [];
        if ( !empty($this->filters['start']) && !empty($this->filters['end']) ){
          $where[] = " r.date>'" . $this->filters['start'] . "' ";
          $where[] = " r.date<'" . $this->filters['end'] . "' ";
        }
        if (isset($this->filters['status']) && $this->filters['status']!=-1){
          $where[] = " r.status='" . $this->filters['status'] . "' ";
        }
        if (isset($this->filters['source']) && $this->filters['source']!=-1){
          $where[] = " r.source LIKE '%" . $this->filters['source'] . "%' ";
        }
        if (!empty($this->filters['affiliate_username'])){
          $where[] = " ((u.user_login LIKE '%" . $this->filters['affiliate_username'] . "%') OR  (u.user_email LIKE '%" . $this->filters['affiliate_username'] . "%') )";
        }

        $referrals = $indeed_db->get_referrals( -1, -1, false, '', '', $where );

        if ( empty( $referrals ) ){
            return;
        }

        $this->removeOldFiles();
        $hash = bin2hex( random_bytes( 20 ) );
        $filename = $hash . '.csv';
        $targetFile = UAP_PATH . 'temporary/' . $filename;
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
                $status = __( 'Verified', 'uap' );
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
        $this->linkToDownload = UAP_URL . 'temporary/' . $filename;
    }

    public function getDownloadLink()
    {
        return $this->linkToDownload;
    }

    private function removeOldFiles()
    {
        $directory = UAP_PATH . 'temporary/';
        $files = scandir( $directory );
        foreach ( $files as $file ){
            $fileFullPath = $directory . $file;
            if ( file_exists( $fileFullPath ) && filetype( $fileFullPath ) == 'file' ){
                $extension = pathinfo( $fileFullPath, PATHINFO_EXTENSION );
                if ( $extension == 'csv' ){
                    unlink( $fileFullPath );
                }
            }
        }
    }
}
