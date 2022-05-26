<?php
include 'db_connect.php';
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo 'BP_AVATAR_THUMB_WIDTH='.BP_AVATAR_THUMB_WIDTH.'<br/>';
echo 'BP_AVATAR_THUMB_HEIGHT='.BP_AVATAR_THUMB_HEIGHT.'<br/>';

//echo bp_core_fetch_avatar(array('item_id' => 10391, 'object'=>'user', 'type'=>'thumb'));
echo 'base directory is '.$_SERVER['DOCUMENT_ROOT'].'<br/>';
$dir = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/avatars/";
$count = 0;
if(file_exists($dir)){
  echo 'looking in '.$dir.'<br/>';
  $dirList = scandir($dir);
  foreach($dirList as $directory){
    if(is_dir($dir.$directory)){
      if ( strpos( $directory, '.' ) === false ) {
        $fileList = scandir($dir.$directory);
        if (($key = array_search('.', $fileList)) !== false) {
            unset($fileList[$key]);
        }
        if (($key = array_search('..', $fileList)) !== false) {
            unset($fileList[$key]);
        }

        $thumbArr = array();
        $fullArr = array();
        foreach($fileList as $file){
          if ( strpos( $file, 'thumb' ) !== false ) {
            $thumbArr[]=$dir.$directory.'/'.$file;
          }
          if ( strpos( $file, 'full' ) !== false ) {
            $fullArr[]=$dir.$directory.'/'.$file;
          }
        }
        if(count($thumbArr) >1 || count($fullArr) > 1){
          $count++;
        }
        if(count($thumbArr) > 1){
          sort($thumbArr);
          unlink($thumbArr[0]);
          echo 'delete '.$thumbArr[0].'<br/>';
          //echo $dir.$directory.' has '. count($thumbArr).' thumb files<br/>';
          //var_dump($thumbArr);
          //echo '<br/>';
        }
        if(count($fullArr) > 1){
          sort($fullArr);
          unlink($fullArr[0]);
          echo 'delete '.$fullArr[0].'<br/>';
          //echo $dir.$directory.' has '. count($fullArr).' full files<br/>';
          //var_dump($fullArr);
          //echo '<br/>';
        }
      }

    }
  }
  //var_dump($dirList);
}
echo 'found '.$count.' users with more than 2 avatars';
