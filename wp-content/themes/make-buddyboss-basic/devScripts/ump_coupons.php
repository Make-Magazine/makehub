<?php
include 'db_connect.php';

$sql = 'select * from wp_ihc_coupons';

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");

?>
<!doctype html>

<html lang="en">
    <head>
        <style>
            h1, .h1, h2, .h2, h3, .h3 {
                margin-top: 10px !important;
                margin-bottom: 10px !important;
            }
            ul, ol {
                margin-top: 0 !important;
                margin-bottom: 0px !important;
                padding-top: 0px !important;
                padding-bottom: 0px !important;
            }
            table {font-size: 14px;}
            #headerRow {
                font-size: 1.2em;
                border: 1px solid #98bf21;
                padding: 5px;
                background-color: #A7C942;
                color: #fff;
                text-align: center;
            }

            .detailRow {
                font-size: 1.2em;
                border: 1px solid #98bf21;
            }
            #headerRow td, .detailRow td {
                border-right: 1px solid #98bf21;
                padding: 3px 7px;
                vertical-align: baseline;
            }
            .detailRow td:last-child {
                border-right: none;
            }
            .row-eq-height {
                display: -webkit-box;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
            }
            .tcenter {
                text-align: center;
            }
        </style>
        <link rel='stylesheet' id='make-bootstrap-css'  href='http://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
        <link rel='stylesheet' id='font-awesome-css'  href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=2.819999999999997' type='text/css' media='all' />
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>

    <body>
        <div class="container" style="width:100%; line-height: 1.3em">
            <div style="clear:both"></div>
            <br/>
            <table style="margin: 10px 0;width:100%">
                <thead>
                    <tr id="headerRow">
                        <td style="">Code</td>
                        <td style="">submitted_coupons_count</td>
                        <td style="">Short Description</td>
                        <td style="">Type of Discount</td>
                        <td style="">Discount Value</td>
                        <td style="">period_type</td>
                        <td style="">start_time</td>
                        <td style="">end_time</td>
                        <td style="">repeat</td>
                        <td style="">target_level</td>
                        <td style="">recurring</td>
                    </tr>
                </thead>
                <tbody>
                  <?php
                  // Loop through the coupons
                  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                      echo '<tr>';
                      echo '<td style="">'.$row['code'].'</td>';
                      echo '<td style="">'.$row['submitted_coupons_count'].'</td>';
                      $settings = unserialize($row['settings']);
                      echo '<td style="">'.(isset($settings['description'])?$settings['description']:'').'</td>';
                      echo '<td style="">'.(isset($settings['discount_type'])?$settings['discount_type']:'').'</td>';
                      echo '<td style="">'.(isset($settings['discount_value'])?$settings['discount_value']:'').'</td>';
                      echo '<td style="">'.(isset($settings['period_type'])?$settings['period_type']:'').'</td>';
                      echo '<td style="">'.(isset($settings['start_time'])?$settings['start_time']:'').'</td>';
                      echo '<td style="">'.(isset($settings['end_time'])?$settings['end_time']:'').'</td>';
                      echo '<td style="">'.(isset($settings['repeat'])?$settings['repeat']:'').'</td>';
                      echo '<td style="">'.(isset($settings['target_level'])?$settings['target_level']:'').'</td>';
                      echo '<td style="">'.(isset($settings['recurring'])?$settings['recurring']:'').'</td>';
                      echo '</tr>';
                  }
                  ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
<?php

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}
