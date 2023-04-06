<?php
/**
 * Class used to search and retrieve imports
 * @author marcus
 *
 */
class EMIO_Imports extends EMIO_Items {
	
	public static $type = 'import';
	
	public static function upload_mimes( $mime_types = array() ) {
		$formats = array_merge( static::$formats, static::$formats );
		foreach( $formats as $format => $EMIO_Import ){ /* @var EMIO_Import $EMIO_Import */
			//formats can have multiple extensions, but in WP only one mime is supported per extension at the moment
			$format_extensions = !is_array($EMIO_Import::$ext) ? array($EMIO_Import::$ext) : $EMIO_Import::$ext;
			$format_mime_type = is_array($EMIO_Import::$mime_type) ? current($EMIO_Import::$mime_type) : $EMIO_Import::$mime_type; //if this changes, arrays are supported but not useful currently
			//check if any extension exists and if not add extension to array
			foreach( $format_extensions as $extension ){
				if( empty($mime_types[$extension]) ){
					$mime_types[$extension] = $format_mime_type;
				}
			}
		}
		return $mime_types;
	}

}
add_filter('upload_mimes', 'EMIO_Imports::upload_mimes');