<?php
include '../../../../wp-load.php';
// include 'db_connect.php';
global $wpdb;

$findme  = (isset($_GET['findme']) ? $_GET['findme'] : '');
$status  = (isset($_GET['status']) ? $_GET['status'] : '');

if ($findme != '') {
    echo 'looking for ' . $findme . ' '.($status!=''?' with a post_status of '.$status:'').'<br/><br/>';
}else{
  $file_path = __DIR__;
  $url_path = get_site_url().str_replace($_SERVER['DOCUMENT_ROOT'], '', $file_path);
  echo 'Please enter your search term in the URL using the findme variable. IE: '.$url_path.'/findInPostContent.php?findme=sometext<br/>';
  echo 'To limit post type, use the status variable. IE: '.$url_path.'/findInPostContent.php?findme=sometext&status=publish<br/>';
  die();
}
global $wpdb;

$blogSql = "select blog_id, domain from wp_blogs  ORDER BY `wp_blogs`.`blog_id` ASC";

$results = $wpdb->get_results($blogSql, ARRAY_A);

$blogArray = array();
//loop thru blogs
$count = 0;
foreach ($results as $blogrow) {
    $blogID = $blogrow['blog_id'];

    if ($blogID == 1) {
        $table = 'wp_posts';
    } else {
        $table = 'wp_' . $blogID . '_posts';
    }

    $postResults = $wpdb->get_results('select ID, post_title, post_date, post_status, post_type from ' . $table.
    ' where post_content like "%'.$findme.'%" '.
    ($status !=''? ' and post_status = "'.$status.'"':'') .
    'order by post_date', ARRAY_A);

    foreach ($postResults as $postRow) {
      $blogArray[] = array(
          'blog_id' => $blogID,
          'blog_name' => $blogrow['domain'],
          'post_id' => $postRow['ID'],
          'post_date'  => $postRow['post_date'],
          'post_status'  => $postRow['post_status'],
          'post_type'  => $postRow['post_type'],
          'post_title' => $postRow['post_title']
      );
    }
}


if ((isset($_GET['debug']) && trim($_GET['debug']) != '')) {
    $debug = 1;
    echo 'Turning on DEBUG mode <br>';
}
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
            td, th {
                padding: 5px !important;
                border: thin solid lightgrey;
            }
        </style>
        <link rel='stylesheet' id='make-bootstrap-css'  href='http://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
        <link rel='stylesheet' id='font-awesome-css'  href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=2.819999999999997' type='text/css' media='all' />
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>

    <body>
        <div class="container" style="width:100%; line-height: 1.3em">
            <?php
            if(!empty($blogArray)){
              ?>
              <div style="clear:both">
                <table width="100%">
                    <tr>
                        <td width="15%">Blog Name</td>
                        <td width="5%">Post ID</td>
                        <td width="15%">Post Date</td>
                        <td width="5%">Post Status</td>
                        <td width="10%">Post Type</td>
                        <td width="50%">Post Name</td>
                    </tr>
                    <?php
                    foreach ($blogArray as $blogData) {
                      echo '<tr>';
                        echo '<td>' . $blogData['blog_name']  . '</td>';
                        echo '<td>' . $blogData['post_id']    . '</td>';
                        echo '<td>' . $blogData['post_date']    . '</td>';
                        echo '<td>' . $blogData['post_status']    . '</td>';
                        echo '<td>' . $blogData['post_type']    . '</td>';
                        echo '<td><a target="_blank" href="https://' . $blogData['blog_name'] . '/?p='.$blogData['post_id'].'">' . $blogData['post_title'] . '</a></td>';
                      echo '</tr>';
                    }
                    ?>
                  </table>
                </div>
              <?php
            }
            ?>
        </div>
    </body>
</html>
