<?php
namespace Indeed\Uap\Db;

class Products
{

    private $limit          = 30;
    private $offset         = 0;
    private $searchPhrase   = '';
    private $type           = '';
    private $affiliateId    = 0;
    private $category       = 0;
    private $orderBy        = '';

    public function __construct(){}

    public function setSearchPhrase( $searchPhrase='' )
    {
        $this->searchPhrase = $searchPhrase;
        return $this;
    }

    public function setLimit( $limit=0 )
    {
        $this->limit = $limit;
        return $this;
    }

    public function setOffset( $offset=0 )
    {
        $this->offset = $offset;
        return $this;
    }

    public function setType( $type=0 )
    {
        $this->type = $type;
        return $this;
    }

    public function setAffiliateId( $affiliateId=0 )
    {
        $this->affiliateId = $affiliateId;
        return $this;
    }

    public function setProductCategory( $category=0 )
    {
        $this->category = $category;
        return $this;
    }

    public function setOrderBy( $orderBy='' )
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function getResults( $search='', $limit=0, $offset=0 )
    {
        switch ( $this->type ){
            case 'woo':
              return $this->searchIntoWoo();
              break;
            case 'edd':
              return $this->searchIntoEdd();
              break;
            case 'ulp':
              return $this->searchIntoUlp();
              break;
        }
        return [];
    }

    public function getCount()
    {
        switch ( $this->type ){
            case 'woo':
              return $this->countsForWoo();
              break;
            case 'edd':
              return $this->countsForEdd();
              break;
            case 'ulp':
              return $this->countsForUlp();
              break;
        }

    }

    public function searchIntoWoo()
    {
        global $wpdb;
        $search = esc_sql( $this->searchPhrase );
        $query = "SELECT a.ID, a.post_title, CAST(c.meta_value AS DECIMAL(10,2)) as price, CAST(d.meta_value AS UNSIGNED) as total_sales FROM {$wpdb->posts} a ";
        if ( $this->category ){
            $query .= " INNER JOIN {$wpdb->term_relationships} b ON a.ID=b.object_id  ";
        }
        $query .= " INNER JOIN {$wpdb->postmeta} c ON a.ID=c.post_id ";
        $query .= " INNER JOIN {$wpdb->postmeta} d ON a.ID=d.post_id ";
        $query .= " WHERE 1=1 ";
        if ( $this->category ){
            $query .= $wpdb->prepare( " AND b.term_taxonomy_id=%d ", $this->category );
        }
        if ( $search != '' ){
            $query .= " AND a.post_title LIKE '%$search%' ";
        }
        $query .= " AND a.post_type='product' AND a.post_status='publish' ";
        $query .= " AND c.meta_key='_regular_price' ";
        $query .= " AND d.meta_key='total_sales' ";
        if ( $this->orderBy ){
            switch ( $this->orderBy ){
                case 'popularity':
                  $query .= " ORDER BY total_sales DESC ";
                  break;
                case 'date':
                  $query .= " ORDER BY a.post_date DESC ";
                  break;
                case 'price':
                  $query .= " ORDER BY price ASC ";
                  break;
                case 'price-desc':
                  $query .= " ORDER BY price DESC ";
                  break;
            }
        }
        $query .= $wpdb->prepare( " LIMIT %d OFFSET %d;", $this->limit, $this->offset);

        $data = $wpdb->get_results( $query );
        if ( !$data ){
            return [];
        }
        $currency = get_option('woocommerce_currency');
        $return = [];


        require_once UAP_PATH . 'public/Affiliate_Referral_Amount.class.php';
        $do_math = new \Affiliate_Referral_Amount( $this->affiliateId, 'woo');

        foreach ( $data as $productData){
            $product = wc_get_product( $productData->ID );
            $price = $productData->price;
			      $regular_price = $product->get_regular_price();
            $referralAmount = $do_math->get_result( $price, $productData->ID );// input price, product id
            $return[$productData->ID] = [
                  'price'             => $price . $currency,
				          'regular_price'     => $regular_price . $currency,
                  'label'             => $productData->post_title,
                  'featureImage'      => get_the_post_thumbnail_url( $productData->ID ),
                  'id'                => $productData->ID,
                  'product_type'      => 'woo',
                  'referral_amount'   => $referralAmount . $currency,
                  'permalink'         => get_permalink( $productData->ID ),
                  'categories'        => get_the_terms( $productData->ID, 'product_cat' ),
            ];
        }
        return $return;
    }

    private function countsForWoo()
    {
        global $wpdb;
        $search = esc_sql( $this->searchPhrase );
        $query = "SELECT COUNT(a.ID) FROM {$wpdb->posts} a ";
        if ( $this->category ){
            $query .= " INNER JOIN {$wpdb->term_relationships} b ON a.ID=b.object_id ";
        }
        $query .= " WHERE 1=1 ";
        if ( $this->category ){
            $query .= $wpdb->prepare( " AND b.term_taxonomy_id=%d ", $this->category );
        }
        if ( $search != '' ){
            $query .= " AND a.post_title LIKE '%$search%' ";
        }
        $query .= " AND a.post_type='product' AND a.post_status='publish' ";
        return $wpdb->get_var( $query );
    }

    private function searchIntoEdd()
    {
        global $wpdb;
        $search = esc_sql( $this->searchPhrase );
        $query = "SELECT a.ID, a.post_title, CAST(c.meta_value AS DECIMAL(10,2)) as price, CAST(d.meta_value AS UNSIGNED) as total_sales FROM {$wpdb->posts} a ";
        if ( $this->category ){
            $query .= " INNER JOIN {$wpdb->term_relationships} b ON a.ID=b.object_id  ";
        }
        $query .= " INNER JOIN {$wpdb->postmeta} c ON a.ID=c.post_id ";
        $query .= " INNER JOIN {$wpdb->postmeta} d ON a.ID=d.post_id ";
        $query .= " WHERE 1=1 ";
        if ( $this->category ){
            $query .= $wpdb->prepare( " AND b.term_taxonomy_id=%d ", $this->category );
        }
        if ( $search != '' ){
            $query .= " AND a.post_title LIKE '%$search%' ";
        }
        $query .= " AND a.post_type='download' AND a.post_status='publish' ";
        $query .= " AND c.meta_key='edd_price' ";
        $query .= " AND d.meta_key='_edd_download_sales' ";
        if ( $this->orderBy ){
            switch ( $this->orderBy ){
                case 'popularity':
                  $query .= " ORDER BY total_sales DESC ";
                  break;
                case 'date':
                  $query .= " ORDER BY a.post_date DESC ";
                  break;
                case 'price':
                  $query .= " ORDER BY price ASC ";
                  break;
                case 'price-desc':
                  $query .= " ORDER BY price DESC ";
                  break;
            }
        }
        $query .= $wpdb->prepare( " LIMIT %d OFFSET %d;", $this->limit, $this->offset);

        $data = $wpdb->get_results( $query );
        if ( !$data ){
            return [];
        }
        $return = [];

        $currency = edd_get_currency();
        require_once UAP_PATH . 'public/Affiliate_Referral_Amount.class.php';
        $do_math = new \Affiliate_Referral_Amount( $this->affiliateId, 'edd');

        foreach ( $data as $productData){
            $price = edd_price( $productData->ID, false );;
            $referralAmount = $do_math->get_result( $price, $productData->ID );// input price, product id

            $return[$productData->ID] = [
                  'price'             => $price . $currency,
                  'label'             => $productData->post_title,
                  'featureImage'      => get_the_post_thumbnail_url( $productData->ID ),
                  'id'                => $productData->ID,
                  'product_type'      => 'edd',
                  'referral_amount'   => $referralAmount . $currency,
                  'permalink'         => get_permalink( $productData->ID ),
                  'categories'        => get_the_terms( $productData->ID, 'download_category' ),
            ];
        }
        return $return;
    }

    private function countsForEdd()
    {
        global $wpdb;
        $search = esc_sql( $this->searchPhrase );
        $query = "SELECT COUNT(a.ID) FROM {$wpdb->posts} a ";
        if ( $this->category ){
            $query .= " INNER JOIN {$wpdb->term_relationships} b ON a.ID=b.object_id ";
        }
        $query .= " WHERE 1=1 ";
        if ( $this->category ){
            $query .= $wpdb->prepare( " AND b.term_taxonomy_id=%d ", $this->category );
        }
        if ( $search != '' ){
            $query .= " AND a.post_title LIKE '%$search%' ";
        }
        $query .= " AND a.post_type='download' AND a.post_status='publish' ";
        return $wpdb->get_var( $query );
    }

    private function searchIntoUlp()
    {
        global $wpdb;
        $search = esc_sql( $this->searchPhrase );
        $query = "SELECT a.ID, a.post_title, CAST(c.meta_value AS DECIMAL(10,2)) as price, COUNT(d.id) as total_sales FROM {$wpdb->posts} a ";
        if ( $this->category ){
            $query .= " INNER JOIN {$wpdb->term_relationships} b ON a.ID=b.object_id  ";
        }
        $query .= " LEFT JOIN {$wpdb->postmeta} c ON a.ID=c.post_id ";
        $query .= " LEFT JOIN {$wpdb->prefix}ulp_user_entities_relations d ON a.ID=d.entity_id ";
        $query .= " WHERE 1=1 ";
        if ( $this->category ){
            $query .= $wpdb->prepare( " AND b.term_taxonomy_id=%d ", $this->category );
        }
        if ( $search != '' ){
            $query .= " AND a.post_title LIKE '%$search%' ";
        }
        $query .= " AND a.post_type='ulp_course' AND a.post_status='publish' ";
        $query .= " AND c.meta_key='ulp_course_price' ";
        $query .= " GROUP BY a.ID ";
        if ( $this->orderBy ){
            switch ( $this->orderBy ){
                case 'popularity':
                  $query .= " ORDER BY total_sales DESC ";
                  break;
                case 'date':
                  $query .= " ORDER BY a.post_date DESC ";
                  break;
                case 'price':
                  $query .= " ORDER BY price ASC ";
                  break;
                case 'price-desc':
                  $query .= " ORDER BY price DESC ";
                  break;
            }
        }
        $query .= $wpdb->prepare( " LIMIT %d OFFSET %d;", $this->limit, $this->offset);

        $data = $wpdb->get_results( $query );
        if ( !$data ){
            return [];
        }
        $currency = get_option('ulp_currency');
        $return = [];

        require_once UAP_PATH . 'public/Affiliate_Referral_Amount.class.php';
        $do_math = new \Affiliate_Referral_Amount( $this->affiliateId, 'ulp');

        foreach ( $data as $productData){
            $price = get_post_meta( $productData->ID, 'ulp_course_price', true );
            if ( is_numeric($price) ){
                $price .= $currency;
            }
            $referralAmount = $do_math->get_result( $price, $productData->ID );// input price, product id

            $return[$productData->ID] = [
                  'price'             => $price,
                  'label'             => $productData->post_title,
                  'featureImage'      => get_the_post_thumbnail_url( $productData->ID ),
                  'id'                => $productData->ID,
                  'product_type'      => 'ulp',
                  'referral_amount'   => $referralAmount . $currency,
                  'permalink'         => get_permalink( $productData->ID ),
                  'categories'        => get_the_terms( $productData->ID, 'ulp_course_categories' ),
            ];
        }
        return $return;
    }

    private function countsForUlp()
    {
        global $wpdb;
        $search = esc_sql( $this->searchPhrase );
        $query = "SELECT COUNT(a.ID) FROM {$wpdb->posts} a ";
        if ( $this->category ){
            $query .= " INNER JOIN {$wpdb->term_relationships} b ON a.ID=b.object_id ";
        }
        $query .= " WHERE 1=1 ";
        if ( $this->category ){
            $query .= $wpdb->prepare( " AND b.term_taxonomy_id=%d ", $this->category );
        }
        if ( $search != '' ){
            $query .= " AND a.post_title LIKE '%$search%' ";
        }
        $query .= " AND a.post_type='ulp_course' AND a.post_status='publish' ";
        return $wpdb->get_var( $query );
    }

}
