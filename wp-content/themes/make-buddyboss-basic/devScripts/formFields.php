<?php
include 'db_connect.php';

$blogID = get_current_blog_id();

if ($blogID == 1) {
    $tbl_form_meta = 'wp_gf_form_meta';
    $tbl_form = 'wp_gf_form';
} else {
    $tbl_form_meta = 'wp_' . $blogID . '_gf_form_meta';
    $tbl_form = 'wp_' . $blogID . '_gf_form';
}

$sql = 'select display_meta from ' . $tbl_form_meta . ' left outer join ' . $tbl_form . ' on ' . $tbl_form . '.id = form_id where is_trash=0';
if (isset($_GET['formID']))
    $sql .= ' where form_id=' . $_GET['formID'];

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
            <div style="text-align: center">
                <div style="font-size: 12px;line-height: 12px;">
                    <i>add ?formID=xxx to the end of the URL to specify a specific form - ie: global.makerfaire.com/wp-content/themes/MiniMakerFaire/devScripts/formFields.php?formID=77</i>
                </div>
            </div>
            <?php
            // Loop through the forms
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $json = json_decode($row['display_meta']);

                if (!$json->is_trash) {

                    echo '<h3 style="float:left">Form ' . $json->id . ' - ' . $json->title . '</h3>';
                    echo '<span style="float:right; margin-top: 15px;"><i>Blog ID = ' . $blogID . '</i></span>';
                    ?>
                    <div style="clear:both"></div>
                    <?php
                    //Display rules conditional logic
                    if (isset($json->button->conditionalLogic->rules) && is_array($json->button->conditionalLogic->rules)) {
                        ?>
                        Submit Button Conditional Logic
                        <ul>
                            <?php
                            foreach ($json->button->conditionalLogic->rules as $rule) {
                                echo '<li>' . $rule->fieldId . ' ' . $rule->operator . ' ' . $rule->value . '</li>';
                            }
                            ?>
                        </ul>
                        <?php
                    }
                    ?>
                    <table style="margin: 10px 0;width:100%">
                        <thead>
                            <tr id="headerRow">
                                <td style="">ID</td>
                                <td style="">Label</td>
                                <td style="">Type</td>
                                <td style="">Options</td>
                                <td style="">Parameter Name</td>
                                <td style="">Visibility</td>
                                <td style="">Required</td>
                            </tr>
                        </thead><?php
                        $jsonArray = (array) $json->fields;
                        foreach ($jsonArray as &$array) {
                            $array->id = (float) $array->id;
                            $array = (array) $array;
                        }

                        usort($jsonArray, "cmp");
                        // buld table of field data
                        foreach ($jsonArray as $field) {
                            if ($field['type'] != 'html' && $field['type'] != 'page') {
                                //var_dump($field);
                                $label = (isset($field['adminLabel']) && trim($field['adminLabel']) != '' ? $field['adminLabel'] : $field['label']);
                                if ($label == '' && $field['type'] == 'checkbox')
                                    $label = $field['choices'][0]->text;
                                ?>
                                <tr class="detailRow">
                                    <td class="tcenter"><?php echo $field['id']; ?></td>
                                    <td><?php echo $label; ?></td>
                                    <td><?php echo $field['type']; ?></td>
                                    <td><?php
                                        if ($field['type'] == 'product') {
                                            echo '<table width="100%">';
                                            echo '<tr><th>Label</th><th>Price</th></tr>';
                                            foreach ($field['choices'] as $choice) {
                                                echo '<tr><td>' . ($choice->value != $choice->text ? $choice->value . '-' . $choice->text : $choice->text) . '</td><td>' . $choice->price . '</td></tr>';
                                            }
                                            echo '</table>';
                                        } elseif ($field['type'] == 'checkbox' || $field['type'] == 'radio' || $field['type'] == 'select' || $field['type'] == 'address') {
                                            echo '<ul style="padding-left: 20px;">';
                                            if (isset($field['inputs']) && !empty($field['inputs'])) {
                                                foreach ($field['inputs'] as $choice) {
                                                    echo '<li>' . $choice->id . ' : ' . $choice->label . '</li>';
                                                }
                                            } else {
                                                foreach ($field['choices'] as $choice) {
                                                    echo '<li>' . ($choice->value != $choice->text ? $choice->value . '-' . $choice->text : $choice->text) . '</li>';
                                                }
                                            }
                                            echo '</ul>';
                                        }
                                        ?>
                                    </td>
                                    <td class="tcenter"><?php echo (isset($field['inputName']) ? $field['inputName'] : ''); ?></td>
                                    <td class="tcenter"><?php echo (isset($field['visibility']) ? $field['visibility'] : ''); ?></td>
                                    <td class="tcenter"><?php echo ($field['isRequired'] ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''); ?></td>            
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table><?php
                }
            }
            ?>
        </div>
    </body>
</html>
<?php

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}
