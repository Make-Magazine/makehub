<?php
namespace Indeed\Ihc\Db;

class Orders
{
    private $id             = 0;
    private $data           = null;

    public function setData( $data = array() )
    {
        if ( !$data ){
            return;
        }
        foreach ( $data as $key => $value ){
            $this->data[ $key ] = $value;
        }
        return $this;
    }

    public function setId( $id=0 )
    {
        $this->id = $id;
        return $this;
    }

    public function fetch()
    {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT id, uid, lid, amount_type, amount_value, automated_payment, status, create_date FROM {$wpdb->prefix}ihc_orders WHERE id=%d;", $this->id );
        $this->data = $wpdb->get_row( $query );
        $this->data = $this->data;
        return $this;
    }

    public function get()
    {
        return $this->data;
    }

    public function save()
    {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT id, uid, lid, amount_type, amount_value, automated_payment, status, create_date FROM {$wpdb->prefix}ihc_orders WHERE id=%d;", $this->id );
        $writeData = $wpdb->get_row( $query );
        if ( $writeData ){
            /// update
            $writeData = (array)$writeData;
            foreach ( $this->data as $key => $value ){
                $writeData[$key] = $value;
            }
            $query = $wpdb->prepare( "UPDATE {$wpdb->prefix}ihc_orders SET
                                          uid=%d,
                                          lid=%d,
                                          amount_type=%s,
                                          amount_value=%s,
                                          automated_payment=%s,
                                          status=%s,
                                          create_date=%s
                                          WHERE id=%d;",
            $writeData['uid'], $writeData['lid'], $writeData['amount_type'], $writeData['amount_value'], $writeData['automated_payment'],
            $writeData['status'], $writeData['create_date'], $writeData['id'] );
            $wpdb->query( $query );
            return $writeData['id'];
        } else {
            /// insert
            $query = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}ihc_orders
                                          VALUES( NULL, %d, %d, %s, %s, %d, %s, NOW() );",
            $this->data['uid'], $this->data['lid'], $this->data['amount_type'], $this->data['amount_value'], $this->data['automated_payment'],
            $this->data['status'] );
            $wpdb->query( $query );
            return $wpdb->insert_id;
        }

    }

    public function getStatus()
    {
        return isset( $this->data->status ) ? $this->data->status : false;
    }
}
