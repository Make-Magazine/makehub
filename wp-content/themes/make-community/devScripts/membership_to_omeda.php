<?php
/*  The purpose of this script is to output a list of recent memberpress transactions
    for Cathy to use to add memberships to Omeda
*/
include 'db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$export_csv  = (isset($_GET['export_csv'])?TRUE:FALSE);
$offset = (isset($_GET['offset']) ? $_GET['offset']:0);
$limit = (isset($_GET['limit']) ? $_GET['limit']:100);
echo 'offset = '.$offset.' limit='.$limit.'<br/>';

//sql query to retrieve transactions
$sql = 'SELECT wp_mepr_transactions.ID, amount, user_id, product_id, created_at, expires_at,
            (select post_title from wp_posts where wp_posts.ID=product_id) as product_name,
            (select post_title from wp_posts where wp_posts.ID=coupon_id) as coupon_name,
            (select meta_value from wp_usermeta where wp_usermeta.user_id =wp_mepr_transactions.user_id  and meta_key = "first_name" limit 1) as first_name,
            (select meta_value from wp_usermeta where wp_usermeta.user_id =wp_mepr_transactions.user_id  and meta_key = "last_name" limit 1) as last_name,
            (SELECT value FROM `wp_bp_xprofile_data` where field_id=201 and wp_bp_xprofile_data.user_id = wp_mepr_transactions.user_id ORDER BY `last_updated` DESC limit 1) as customer_address,
            (SELECT value FROM `wp_bp_xprofile_data` where field_id=203 and wp_bp_xprofile_data.user_id = wp_mepr_transactions.user_id ORDER BY `last_updated` DESC limit 1) as customer_address2,
            (SELECT value FROM `wp_bp_xprofile_data` where field_id=202 and wp_bp_xprofile_data.user_id = wp_mepr_transactions.user_id ORDER BY `last_updated` DESC limit 1) as customer_city,
            (SELECT value FROM `wp_bp_xprofile_data` where field_id=391 and wp_bp_xprofile_data.user_id = wp_mepr_transactions.user_id ORDER BY `last_updated` DESC limit 1) as customer_state,
            (SELECT value FROM `wp_bp_xprofile_data` where field_id=392 and wp_bp_xprofile_data.user_id = wp_mepr_transactions.user_id ORDER BY `last_updated` DESC limit 1) as customer_country,
            (SELECT value FROM `wp_bp_xprofile_data` where field_id=393 and wp_bp_xprofile_data.user_id = wp_mepr_transactions.user_id ORDER BY `last_updated` DESC limit 1) as customer_zip,
            (SELECT meta_value FROM `wp_usermeta` where wp_usermeta.user_id=wp_mepr_transactions.user_id and meta_key = "mepr-address-one"      ORDER BY wp_usermeta.umeta_id DESC limit 1) as mepr_address_one,
            (SELECT meta_value FROM `wp_usermeta` where wp_usermeta.user_id=wp_mepr_transactions.user_id and meta_key = "mepr-address-two"      ORDER BY wp_usermeta.umeta_id DESC limit 1) as mepr_address_two,
            (SELECT meta_value FROM `wp_usermeta` where wp_usermeta.user_id=wp_mepr_transactions.user_id and meta_key = "mepr-address-city"     ORDER BY wp_usermeta.umeta_id DESC limit 1) as mepr_address_city,
            (SELECT meta_value FROM `wp_usermeta` where wp_usermeta.user_id=wp_mepr_transactions.user_id and meta_key = "mepr-address-state"    ORDER BY wp_usermeta.umeta_id DESC limit 1) as mepr_address_state,
            (SELECT meta_value FROM `wp_usermeta` where wp_usermeta.user_id=wp_mepr_transactions.user_id and meta_key = "mepr-address-country"  ORDER BY wp_usermeta.umeta_id DESC limit 1) as mepr_address_country,
            (SELECT meta_value FROM `wp_usermeta` where wp_usermeta.user_id=wp_mepr_transactions.user_id and meta_key = "mepr-address-zip"      ORDER BY wp_usermeta.umeta_id DESC limit 1) as mepr_address_zip,
            (SELECT user_email FROM `wp_users`    where wp_users.ID = wp_mepr_transactions.user_id) as user_email
        FROM `wp_mepr_transactions`
        where status="complete" and 
              (expires_at >= now() or expires_at="0000-00-00 00:00:00") and 
              product_id in(10732) AND 
              (coupon_id !=0 or (amount >=40 and amount < 125))
        limit '.$offset.', '.$limit;
        
        //and product_id in(10732, 10735, 10736, 10737, 15353)';
//note to self - check txn_type, parent_transaction_id to know what type of trx to give

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
echo $result->num_rows . ' Active Premium members between $39 and $125<br/>';
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

$output = array();
$issueFudge = 91;
foreach($rows as $trx_key=>$trx) {    
    $user_id    = $trx['user_id'];
    $user_email = $trx['user_email'];
    $address    = ($trx['customer_address'] != '' ? $trx['customer_address'] : $trx['mepr_address_one']);
    $address2   = ($trx['customer_address2']!= '' ? $trx['customer_address2']:$trx['mepr_address_two']);
    $city       = ($trx['customer_city']    != '' ? $trx['customer_city']:$trx['mepr_address_city']);
    $state      = ($trx['customer_state']   != '' ? $trx['customer_state']:$trx['mepr_address_state']);
    $country    = ($trx['customer_country'] != '' ? $trx['customer_country']:$trx['mepr_address_country']);
    $zip        = ($trx['customer_zip']     != '' ? $trx['customer_zip']:$trx['mepr_address_zip']);
        
    $trx_data  = array(
        "ID"            => $trx["ID"],      
        "amount"        => $trx["amount"],            
        "product_id"    => $trx["product_id"],  
        "product_name"  => $trx["product_name"],
        "coupon_name"   => $trx["coupon_name"],    
        "created_at"    => $trx["created_at"],
        "expires_at"    => $trx["expires_at"]);
            
    $cust_data = array( 
        'Firstname' => $trx['first_name'],
        'Lastname' => $trx['last_name'],
        'address'  => $address,
        'address2' => $address2,
        'city'     => $city,
        'state'    => $state,
        'zip'      => $zip,
        'country'  => $country,
        'email'    => $user_email);

    $omeda_data = array();    
    //retrieve omeda information - Subscription Lookup By Email        
    $sub_by_email_api = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/email/'.$user_email.'/subscription/product/7/*';
    $header = array("x-omeda-appid: 0387143E-E0DB-4D2F-8441-8DAB0AF47954");

    $subscriptionJson = json_decode(basicCurl($sub_by_email_api, $header));
    if(!isset($subscriptionJson->Customers)) {
        $customers = array();
    }else{
        // check if customer found at omeda, otherwise skip
        $customers = $subscriptionJson->Customers;
    }

    $writeRec = false;
    if(is_array($customers)){
        foreach($customers as $customer){
            $customer_id = (isset($customer->OmedaCustomerId) ? $customer->OmedaCustomerId:0);            
            foreach($customer->Subscriptions as $customer_sub){
                $cust_subscriptions = array();
                $IssueExpirationDate = (isset($customer_sub->IssueExpirationDate)?$customer_sub->IssueExpirationDate:0);
                $donor_id = (isset($customer_sub->DonorId) ? $customer_sub->DonorId:'');
                //if($customer_sub->Receive == "1" || strtotime($IssueExpirationDate." + ".$issueFudge." days") >= strtotime('now')){
                    //if donor id is set, but not = customer id, skip this record. it is a gift
                    if($donor_id !='' && $donor_id != $customer_id){
                        //skip  
                    }else{
                        /*
                        if(strtolower($customer_sub->PromoCode)  != 'v21mempd' && 
                            strtolower($customer_sub->PromoCode) != 'v21memp' && 
                            strtolower($customer_sub->PromoCode) != 'v21memd' &&
                            $customer_sub->MarketingClassDescription != 'Kill/Refunds' &&
                            $customer_sub->MarketingClassDescription != 'Credit Suspends'
                        ){*/
                            $writeRec = true;
                    
                            $cust_subscription = array(                            
                                'ShippingAddressId'     => $customer_sub->ShippingAddressId,
                                'DonorId'               => (isset($customer_sub->DonorId) ? $customer_sub->DonorId:''),
                                'Status'                => $customer_sub->Status,
                                'term'                  => (isset($customer_sub->Term)?$customer_sub->Term:''),
                                'PromoCode'             => (isset($customer_sub->PromoCode)?$customer_sub->PromoCode:''),
                                'Amount'                => $customer_sub->Amount,                            
                                'RequestedVersionCode'  => (isset($customer_sub->RequestedVersionCode)?$customer_sub->RequestedVersionCode:''),
                                'OrderDate'             => $customer_sub->OrderDate,
                                'IssueExpirationDate'   => (isset($customer_sub->IssueExpirationDate)?$customer_sub->IssueExpirationDate:''),                                                    
                                'MarketingClassDescription' => $customer_sub->MarketingClassDescription
                            );                        
                            $omeda_data[$customer_id]['subscriptions'][] = $cust_subscription;
                                                            
                        //}
                    }                
                //}    
            }            
        }
    } //end loop through omeda results

    //if($writeRec){
    $output[] = array(
        'user_id'  => $user_id,
        'cust_data' => $cust_data,
        'trx_data'  => $trx_data,
        'omeda_data'=> $omeda_data
    );
    //}
} //end foreach rows
echo 'total in array '. count($output).'<br/>';


    foreach($output as $out2){
        echo $out2['user_id'].', ';
        echo implode(", ", $out2['cust_data']).', ';
        echo implode(", ", $out2['trx_data']).', ';
        
        foreach($out2['omeda_data'] as $custId=>$omeda_data){
            echo $custId.', ';
            foreach($omeda_data['subscriptions'] as $omeda_sub){
                echo $omeda_sub['ShippingAddressId'].', '; 
                echo $omeda_sub['DonorId'].', '; 
                echo $omeda_sub['Status'].', ';
                echo $omeda_sub['term'].', ';
                echo $omeda_sub['PromoCode'].', ';
                echo $omeda_sub['Amount'].', '; 
                echo $omeda_sub['RequestedVersionCode'].', ';                
                echo $omeda_sub['OrderDate'].', ';
                echo $omeda_sub['IssueExpirationDate'].', ';
                echo $omeda_sub['MarketingClassDescription'].', ';                
            }
        }        
        echo '<br/>';
    }
    

die('done');


if($export_csv){
    // output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="membership-transactions-' . $year_filter . '.csv"');

    // do not cache the file
    header('Pragma: no-cache');
    header('Expires: 0');

    // create a file pointer connected to the output stream
    $file = fopen('php://output', 'w');

    // send the column headers
    fputcsv($file, array('Trx ID', 'Status', 'Membership', 'Amount', 'Coupon', 'Gift Status', 'Created At', 
                         'Expires', 'User ID', 'Customer Name', 'Customer Email', 'Address', 'Address 2', 'City', 'State', 'Zip', 'Country'));

    //send the data
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $address = ($row['customer_address'] != '' ? $row['customer_address'] : $row['mepr_address_one']);
        $address2 = ($row['customer_address2']!=''?$row['customer_address2']:$row['mepr_address_two']);
        $city = ($row['customer_city']!=''?$row['customer_city']:$row['mepr_address_city']);
        $state = ($row['customer_state']!=''?$row['customer_state']:$row['mepr_address_state']);
        $country = ($row['customer_country']!=''?$row['customer_country']:$row['mepr_address_country']);
        $zip = ($row['customer_zip']!=''?$row['customer_zip']:$row['mepr_address_zip']);
        $giftStatus = ($row['gift_status']!='NULL'?$row['gift_status']:'');
        $output = array($row['ID'],$row['status'],$row['product_name'],
                        $row['amount'], $row['coupon_name'], $giftStatus,$row['created_at'],$row['expires_at'],
                        $row['user_id'],
                        $row['first_name'] . ' '.$row['last_name'],
                        $row['user_email'],
                        $address,
                        ($address2!=''?$address2.'<br/>':''),
                        $city, $state, $zip, $country
                    );
        fputcsv($file, $output);
    }

    exit();
}else{
?>
<!doctype html>

<html lang="en">
    <head>
        <style>
            #customers {
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            #customers td, #customers th {
                border: 1px solid #ddd;
                padding: 8px;
            }

            #customers tr:nth-child(even){background-color: #f2f2f2;}

            #customers tr:hover {background-color: #ddd;}

            #customers th {
                padding-top: 12px;
                padding-bottom: 12px;
                text-align: left;
                background-color: #04AA6D;
                color: white;
            }
            #year_filters {
                padding: 20px;
            }
        </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    </head>

    <body>
        <div id="year_filters">
            <div class="row">
                <div class="col-sm-3"><a href="/wp-content/themes/make-experiences/devScripts/membership_report.php?year_filter=<?php echo $year_filter?>&export_csv" class="btn btn-success">Export</a></div>
                <div>
                    <?php echo mysqli_num_rows($result) .' active members found<br/>';?>
                </div> 
            </div>


        </div>
        <table width="100%" id="customers">
            <thead>
                <tr id="headerRow">
                    <td>Trx ID</td>
                    <td>Status</td>
                    <td>Membership</td>
                    <!-- Transaction information -->
                    <td>Amount</td>
                    <td>Coupon</td>
                    <td>Gift Status</td>
                    <td>Created At</td>
                    <td>Expires</td>
                    <td>User ID</td>
                    <td>Customer Name</td>
                    <td>Customer Email</td>
                    <td>Address</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $tabIndex=1;
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $address = ($row['customer_address'] != '' ? $row['customer_address'] : $row['mepr_address_one']);
                    $address2 = ($row['customer_address2']!=''?$row['customer_address2']:$row['mepr_address_two']);
                    $city = ($row['customer_city']!=''?$row['customer_city']:$row['mepr_address_city']);
                    $state = ($row['customer_state']!=''?$row['customer_state']:$row['mepr_address_state']);
                    $country = ($row['customer_country']!=''?$row['customer_country']:$row['mepr_address_country']);
                    $zip = ($row['customer_zip']!=''?$row['customer_zip']:$row['mepr_address_zip']);
                    $giftStatus = ($row['gift_status']!='NULL'?$row['gift_status']:'');
                    ?>
                    <tr>
                        <td tabindex=<?php echo $tabIndex;?>><?php echo $row['ID']?></td>
                        <td tabindex=<?php echo $tabIndex+1;?>><?php echo $row['status']?></td>
                        <td tabindex=<?php echo $tabIndex+2;?>><?php echo $row['product_name']?></td>
                        <td tabindex=<?php echo $tabIndex+3;?>><?php echo $row['amount']?></td>
                        <td tabindex=<?php echo $tabIndex+4?>><?php echo $row['coupon_name']?></td>
                        <td tabindex=<?php echo $tabIndex+4?>><?php echo $giftStatus;?></td>
                        <td tabindex=<?php echo $tabIndex+5;?>><?php echo $row['created_at']?></td>
                        <td tabindex=<?php echo $tabIndex+6;?>><?php echo $row['expires_at']?></td>
                        <!--User information -->
                        <td tabindex=<?php echo $tabIndex+7;?>><a href="https://make.co/wp-admin/user-edit.php?user_id=<?php echo $row['user_id'];?>" target="_blank"><?php echo $row['user_id']?></a></td>
                        <td tabindex=<?php echo $tabIndex+8;?>><?php echo $row['first_name'] . ' '.$row['last_name']?></td>
                        <td tabindex=<?php echo $tabIndex+9;?>><?php echo $row['user_email'];?></td>
                        <td tabindex=<?php echo $tabIndex+10;?>>
                            <?php
                            echo $address.'<br/>';
                            echo ($address2!=''?$address2.'<br/>':'');
                            echo $city.', '.$state.' '.$zip.'<br/>';
                            echo $country;
                            ?>
                        </td>
                    </tr>
                    <?php
                     $tabIndex = $tabIndex+11;
                }
                ?>
            </tbody>
        </table>
        <br/><br/>
    </body>
</html>
<?php } ?>
