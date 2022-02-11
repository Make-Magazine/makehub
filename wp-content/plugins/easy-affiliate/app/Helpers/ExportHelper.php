<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Lib\Utils;

class ExportHelper {
  public static function render_csv($struct,$filename='',$is_debug=false) {
    if(!$is_debug) {
      header('Content-Type: text/csv');

      if(!empty($filename)) {
        header("Content-Disposition: attachment; filename=\"{$filename}.csv\"");
      }
    }

    header('Content-Type: text/plain');

    die(self::to_csv($struct));
  }

  // Deep convert to associative array using JSON
  public static function deep_convert_to_associative_array($struct) {
    return json_decode(json_encode($struct),true);
  }

  public static function is_associative_array($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
  }

  public static function array_insert($array, $index, $insert) {
    $pos    = array_search($index, array_keys($array));
    $pos    = empty($pos) ? 0 : (int)$pos;
    $before = array_slice($array, 0, $pos + 1);
    $after  = array_slice($array, $pos);
    $array  = $before + $insert + $after;

    return $array;
  }

  public static function header_insert($headers, $new_path, $last_path) {
    if(!isset($headers[$new_path])) {
      $headers = self::array_insert($headers, $last_path, array($new_path => ''));
    }

    return $headers;
  }

  /**
   * Expects an associative array for a row of this data structure. Should
   * handle nested arrays by telescoping header values with the $telescope arg.
   */
  public static function process_csv_row( $row, &$headers, &$last_path, $path='',
    $delimiter = ',',
    $enclosure = '"',
    $enclose_all = false,
    $telescope = '.',
    $null_to_mysql_null=false ) {

    $output = array();

    foreach($row as $label => $field) {
      $new_path = (empty($path) ? $label : $path.$telescope.$label);

      if(is_null($field) and $null_to_mysql_null) {
        $headers = self::header_insert( $headers, $new_path, $last_path );
        $last_path = $new_path;
        $output[$new_path] = 'NULL';

        continue;
      }

      if(is_array($field)) {
        $output += self::process_csv_row($field, $headers, $last_path, $new_path, $delimiter, $enclosure, $enclose_all, $telescope, $null_to_mysql_null);
      }
      else {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');
        $headers = self::header_insert( $headers, $new_path, $last_path );
        $last_path = $new_path;

        // Enclose fields containing $delimiter, $enclosure or whitespace
        if($enclose_all or preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)) {
          $output[$new_path] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        }
        else {
          $output[$new_path] = $field;
        }
      }
    }

    return $output;
  }

  /**
   * Formats an associative array as CSV and returns the CSV as a string.
   * Can handle nested arrays, headers are named by associative array keys.
   * Adapted from http://us3.php.net/manual/en/function.fputcsv.php#87120
   *
   * @param $struct
   * @param string $delimiter
   * @param string $enclosure
   * @param bool $enclose_all
   * @param string $telescope
   * @param bool $null_to_mysql_null
   *
   * @return string
   */
  public static function to_csv( $struct,
    $delimiter = ',',
    $enclosure = '"',
    $enclose_all = false,
    $telescope = '.',
    $null_to_mysql_null = false ) {
    $struct = self::deep_convert_to_associative_array($struct);

    if(self::is_associative_array($struct)) {
      $struct = array($struct);
    }

    $csv = '';
    $headers = array();
    $lines = array();

    foreach( $struct as $row ) {
      $last_path=''; // tracking for the header
      $lines[] = self::process_csv_row(
        $row, $headers, $last_path, '', $delimiter,
        $enclosure, $enclose_all,
        $telescope, $null_to_mysql_null );
    }

    // Always enclose headers
    $csv .= $enclosure . implode( $enclosure.$delimiter.$enclosure, array_keys($headers) ) . $enclosure . "\n";

    foreach( $lines as $line ) {
      $csv_line = array_merge($headers, $line);
      $csv .= implode( $delimiter, array_values($csv_line) ) . "\n";
    }

    return $csv;
  }

  public static function export_table_link($action, $nonce_action, $nonce_name, $itemcount, $all = false) {
    $params = array('action' => $action);

    if($all) {
      $params['all'] = 1;
      $label         = __('Export all as CSV (%s records)', 'easy-affiliate');
    } else {
      $label = __('Export table as CSV (%s records)', 'easy-affiliate');
    }

    ?>
    <a href="<?php
    echo Utils::admin_url(
      'admin-ajax.php',
      array($nonce_action, $nonce_name),
      $params,
      true
    ); ?>"><?php printf($label, \EasyAffiliate\Helpers\AppHelper::format_number($itemcount)); ?></a>
    <?php
  }
}
