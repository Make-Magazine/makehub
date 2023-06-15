<?php
/*  The purpose of this script is to output a list of recent memberpress transactions
    for Cathy to use to add memberships to Omeda
*/
include 'db_connect.php';

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$year_filter = (isset($_GET['year_filter'])?$_GET['year_filter']:'2023');
$export_csv  = (isset($_GET['export_csv'])?TRUE:FALSE);

//sql query to retrieve transactions
$sql = 'SELECT wp_mepr_transactions.ID, amount, user_id, product_id, coupon_id, status, created_at, 
            txn_type, parent_transaction_id,            
            (select post_title from wp_posts where wp_posts.ID=coupon_id) as coupon_name,
            (select post_title from wp_posts where wp_posts.id = (select meta_value from wp_mepr_transaction_meta where meta_key="_gift_coupon_id" and transaction_id=wp_mepr_transactions.ID limit 1)) as gifted_coupon,
            expires_at,
            (select post_title from wp_posts where wp_posts.ID=product_id) as product_name,
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
        where status="complete" and YEAR(created_at)="'.$year_filter.'"
          AND (select post_title from wp_posts where wp_posts.ID=product_id) <> "Community" 
          AND (select post_title from wp_posts where wp_posts.ID=product_id) <> "Maker Camp"
        ORDER BY `wp_mepr_transactions`.`id`  DESC';

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");

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
    fputcsv($file, array('Trx ID', 'Status', 'Membership', 'Amount', 'Coupon Used', 'Gifted Coupon ID', 'Created At', 
                         'Expires', 'User ID', 'Customer Name', 'Customer Email', 'Address', 'Address 2', 'City', 'State', 'Zip', 'Country', 'Trx Type', 'Parent Trx ID'));

    //send the data
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $address = ($row['customer_address'] != '' ? $row['customer_address'] : $row['mepr_address_one']);
        $address2 = ($row['customer_address2']!=''?$row['customer_address2']:$row['mepr_address_two']);
        $city = ($row['customer_city']!=''?$row['customer_city']:$row['mepr_address_city']);
        $state = ($row['customer_state']!=''?$row['customer_state']:$row['mepr_address_state']);
        $country = ($row['customer_country']!=''?$row['customer_country']:$row['mepr_address_country']);
        $zip = ($row['customer_zip']!=''?$row['customer_zip']:$row['mepr_address_zip']);
        $giftStatus = ($row['gifted_coupon']!='NULL'?$row['gifted_coupon']:'');
        $output = array($row['ID'],$row['status'],$row['product_name'],
                        $row['amount'], $row['coupon_name'], $giftStatus,$row['created_at'],$row['expires_at'],
                        $row['user_id'],
                        $row['first_name'] . ' '.$row['last_name'],
                        $row['user_email'],
                        $address,
                        ($address2!=''?$address2.'<br/>':''),
                        $city, $state, $zip, $country, $row['txn_type'], $row['parent_transaction_id']
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
                <div class="col-sm-2"><a href="/wp-content/themes/make-experiences/devScripts/membership_report.php?year_filter=2023" class="btn btn-primary">2023</a></div>
                <div class="col-sm-2"><a href="/wp-content/themes/make-experiences/devScripts/membership_report.php?year_filter=2022" class="btn btn-primary">2022</a></div>
                <div class="col-sm-2"><a href="/wp-content/themes/make-experiences/devScripts/membership_report.php?year_filter=2021" class="btn btn-primary">2021</a></div>
                <div class="col-sm-2"><a href="/wp-content/themes/make-experiences/devScripts/membership_report.php?year_filter=2020" class="btn btn-primary">2020</a></div>
            </div>


        </div>
        <table width="100%" id="customers">
            <thead>
                <tr id="headerRow">
                    <td>Trx ID</td>
                    <td>Trx Type</td>
                    <td>Parent Trx ID</td>
                    <td>Membership</td>
                    <!-- Transaction information -->
                    <td>Amount</td>
                    <td>Coupon Used</td>
                    <td>Gifted Coupon ID</td>
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
                    $gifted_coupon = ($row['gifted_coupon']!='NULL'?$row['gifted_coupon']:'');
                    ?>
                    <tr>
                        <td tabindex=<?php echo $tabIndex;?>><?php echo $row['ID']?></td>
                        <td tabindex=<?php echo $tabIndex+1;?>><?php echo $row['txn_type'];?></td>
                        <td tabindex=<?php echo $tabIndex+1;?>><?php echo $row['parent_transaction_id'];?></td>                
                        <td tabindex=<?php echo $tabIndex+2;?>><?php echo $row['product_name']?></td>
                        <td tabindex=<?php echo $tabIndex+3;?>><?php echo $row['amount']?></td>
                        <td tabindex=<?php echo $tabIndex+4?>><?php echo $row['coupon_name']?></td>
                        <td tabindex=<?php echo $tabIndex+4?>><?php echo $gifted_coupon;?></td>
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
