<?php
define('OW_VOTE_NUM_BIG_BLOCK_DEPOT_BLOCKS_POS', 0x2c);
define('OW_VOTE_SMALL_BLOCK_DEPOT_BLOCK_POS', 0x3c);
define('OW_VOTE_ROOT_START_BLOCK_POS', 0x30);
define('OW_VOTE_BIG_BLOCK_SIZE', 0x200);
define('OW_VOTE_SMALL_BLOCK_SIZE', 0x40);
define('OW_VOTE_EXTENSION_BLOCK_POS', 0x44);
define('OW_VOTE_NUM_EXTENSION_BLOCK_POS', 0x48);
define('OW_VOTE_PROPERTY_STORAGE_BLOCK_SIZE', 0x80);
define('OW_VOTE_BIG_BLOCK_DEPOT_BLOCKS_POS', 0x4c);
define('OW_VOTE_SMALL_BLOCK_THRESHOLD', 0x1000);

// property storage offsets
define('OW_VOTE_SIZE_OF_NAME_POS', 0x40);
define('OW_VOTE_TYPE_POS', 0x42);
define('OW_VOTE_START_BLOCK_POS', 0x74);
define('OW_VOTE_SIZE_POS', 0x78);
define('OW_VOTE_IDENTIFIER_OLE', pack("CCCCCCCC",0xd0,0xcf,0x11,0xe0,0xa1,0xb1,0x1a,0xe1));


function wpvc_GetInt4d($data, $pos) {
	$value = ord($data[$pos]) | (ord($data[$pos+1])	<< 8) | (ord($data[$pos+2]) << 16) | (ord($data[$pos+3]) << 24);
	if ($value>=4294967294) {
		$value=-2;
	}
	return $value;
}

function wpvc_gmgetdate($ts = null){
	$k = array('seconds','minutes','hours','mday','wday','mon','year','yday','weekday','month',0);
	return(wpvc_array_comb($k,explode(":",gmdate('s:i:G:j:w:n:Y:z:l:F:U',is_null($ts)?time():$ts))));
	} 

// Added for PHP4 compatibility
function wpvc_array_comb($array1, $array2) {
	$out = array();
	foreach ($array1 as $key => $value) {
		$out[$value] = $array2[$key];
	}
	return $out;
}

function ow_votes_v($data,$pos) {
	return ord($data[$pos]) | ord($data[$pos+1])<<8;
}

class Wpvc_OLERead {
	var $data = '';
	function __construct(){	}

	function wpvc_read($sFileName){
		if(!is_readable($sFileName)) {
			$this->error = 1;
			return false;
		}
		$this->data = @file_get_contents($sFileName);
		if (!$this->data) {
			$this->error = 1;
			return false;
   		}
   		if (substr($this->data, 0, 8) != OW_VOTE_IDENTIFIER_OLE) {
			$this->error = 1;
			return false;
   		}
		$this->numBigBlockDepotBlocks = wpvc_GetInt4d($this->data, OW_VOTE_NUM_BIG_BLOCK_DEPOT_BLOCKS_POS);
		$this->sbdStartBlock = wpvc_GetInt4d($this->data, OW_VOTE_SMALL_BLOCK_DEPOT_BLOCK_POS);
		$this->rootStartBlock = wpvc_GetInt4d($this->data, OW_VOTE_ROOT_START_BLOCK_POS);
		$this->extensionBlock = wpvc_GetInt4d($this->data, OW_VOTE_EXTENSION_BLOCK_POS);
		$this->numExtensionBlocks = wpvc_GetInt4d($this->data, OW_VOTE_NUM_EXTENSION_BLOCK_POS);

		$bigBlockDepotBlocks = array();
		$pos = OW_VOTE_BIG_BLOCK_DEPOT_BLOCKS_POS;
		$bbdBlocks = $this->numBigBlockDepotBlocks;
		if ($this->numExtensionBlocks != 0) {
			$bbdBlocks = (OW_VOTE_BIG_BLOCK_SIZE - OW_VOTE_BIG_BLOCK_DEPOT_BLOCKS_POS)/4;
		}

		for ($i = 0; $i < $bbdBlocks; $i++) {
			$bigBlockDepotBlocks[$i] = wpvc_GetInt4d($this->data, $pos);
			$pos += 4;
		}


		for ($j = 0; $j < $this->numExtensionBlocks; $j++) {
			$pos = ($this->extensionBlock + 1) * OW_VOTE_BIG_BLOCK_SIZE;
			$blocksToRead = min($this->numBigBlockDepotBlocks - $bbdBlocks, OW_VOTE_BIG_BLOCK_SIZE / 4 - 1);

			for ($i = $bbdBlocks; $i < $bbdBlocks + $blocksToRead; $i++) {
				$bigBlockDepotBlocks[$i] = wpvc_GetInt4d($this->data, $pos);
				$pos += 4;
			}

			$bbdBlocks += $blocksToRead;
			if ($bbdBlocks < $this->numBigBlockDepotBlocks) {
				$this->extensionBlock = wpvc_GetInt4d($this->data, $pos);
			}
		}

		$pos = 0;
		$index = 0;
		$this->bigBlockChain = array();

		for ($i = 0; $i < $this->numBigBlockDepotBlocks; $i++) {
			$pos = ($bigBlockDepotBlocks[$i] + 1) * OW_VOTE_BIG_BLOCK_SIZE;
			for ($j = 0 ; $j < OW_VOTE_BIG_BLOCK_SIZE / 4; $j++) {
				$this->bigBlockChain[$index] = wpvc_GetInt4d($this->data, $pos);
				$pos += 4 ;
				$index++;
			}
		}

		$pos = 0;
		$index = 0;
		$sbdBlock = $this->sbdStartBlock;
		$this->smallBlockChain = array();

		while ($sbdBlock != -2) {
		  $pos = ($sbdBlock + 1) * OW_VOTE_BIG_BLOCK_SIZE;
		  for ($j = 0; $j < OW_VOTE_BIG_BLOCK_SIZE / 4; $j++) {
			$this->smallBlockChain[$index] = wpvc_GetInt4d($this->data, $pos);
			$pos += 4;
			$index++;
		  }
		  $sbdBlock = $this->bigBlockChain[$sbdBlock];
		}

		$block = $this->rootStartBlock;
		$pos = 0;
		$this->entry = $this->__wpvc_readData($block);
		$this->__wpvc_readPropertySets();
	}

	function __wpvc_readData($bl) {
		$block = $bl;
		$pos = 0;
		$data = '';
		while ($block != -2)  {
			$pos = ($block + 1) * OW_VOTE_BIG_BLOCK_SIZE;
			$data = $data.substr($this->data, $pos, OW_VOTE_BIG_BLOCK_SIZE);
			$block = $this->bigBlockChain[$block];
		}
		return $data;
	 }

	function __wpvc_readPropertySets(){
		$offset = 0;
		while ($offset < strlen($this->entry)) {
			$d = substr($this->entry, $offset, OW_VOTE_PROPERTY_STORAGE_BLOCK_SIZE);
			$nameSize = ord($d[OW_VOTE_SIZE_OF_NAME_POS]) | (ord($d[OW_VOTE_SIZE_OF_NAME_POS+1]) << 8);
			$type = ord($d[OW_VOTE_TYPE_POS]);
			$startBlock = wpvc_GetInt4d($d, OW_VOTE_START_BLOCK_POS);
			$size = wpvc_GetInt4d($d, OW_VOTE_SIZE_POS);
			$name = '';
			for ($i = 0; $i < $nameSize ; $i++) {
				$name .= $d[$i];
			}
			$name = str_replace("\x00", "", $name);
			$this->props[] = array (
				'name' => $name,
				'type' => $type,
				'startBlock' => $startBlock,
				'size' => $size);
			if ((strtolower($name) == "workbook") || ( strtolower($name) == "book")) {
				$this->wrkbook = count($this->props) - 1;
			}
			if ($name == "Root Entry") {
				$this->rootentry = count($this->props) - 1;
			}
			$offset += OW_VOTE_PROPERTY_STORAGE_BLOCK_SIZE;
		}

	}

	function wpvc_getWorkBook(){
		if ($this->props[$this->wrkbook]['size'] < OW_VOTE_SMALL_BLOCK_THRESHOLD){
			$rootdata = $this->__wpvc_readData($this->props[$this->rootentry]['startBlock']);
			$streamData = '';
			$block = $this->props[$this->wrkbook]['startBlock'];
			$pos = 0;
			while ($block != -2) {
	  			  $pos = $block * OW_VOTE_SMALL_BLOCK_SIZE;
				  $streamData .= substr($rootdata, $pos, OW_VOTE_SMALL_BLOCK_SIZE);
				  $block = $this->smallBlockChain[$block];
			}
			return $streamData;
		}else{
			$numBlocks = $this->props[$this->wrkbook]['size'] / OW_VOTE_BIG_BLOCK_SIZE;
			if ($this->props[$this->wrkbook]['size'] % OW_VOTE_BIG_BLOCK_SIZE != 0) {
				$numBlocks++;
			}

			if ($numBlocks == 0) return '';
			$streamData = '';
			$block = $this->props[$this->wrkbook]['startBlock'];
			$pos = 0;
			while ($block != -2) {
			  $pos = ($block + 1) * OW_VOTE_BIG_BLOCK_SIZE;
			  $streamData .= substr($this->data, $pos, OW_VOTE_BIG_BLOCK_SIZE);
			  $block = $this->bigBlockChain[$block];
			}
			return $streamData;
		}
	}

}

define('OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF8',			 0x600);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF7',			 0x500);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS',   0x5);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_WORKSHEET',		 0x10);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_BOF',		  0x809);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_EOF',		  0x0a);
define('Ow_VOTE_SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET',   0x85);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_DIMENSION',	0x200);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_ROW',		  0x208);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_DBCELL',	   0xd7);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FILEPASS',	 0x2f);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_NOTE',		 0x1c);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_TXO',		  0x1b6);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_RK',		   0x7e);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_RK2',		  0x27e);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_MULRK',		0xbd);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_MULBLANK',	 0xbe);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_INDEX',		0x20b);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_SST',		  0xfc);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_EXTSST',	   0xff);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_CONTINUE',	 0x3c);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_LABEL',		0x204);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_LABELSST',	 0xfd);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_NUMBER',	   0x203);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_NAME',		 0x18);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_ARRAY',		0x221);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_STRING',	   0x207);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FORMULA',	  0x406);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FORMULA2',	 0x6);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FORMAT',	   0x41e);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_XF',		   0xe0);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_BOOLERR',	  0x205);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FONT',	  0x0031);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_PALETTE',	  0x0092);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_UNKNOWN',	  0xffff);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR', 0x22);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS',  0xE5);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS' ,	25569);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904', 24107);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_MSINADAY',		  86400);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_HYPER',	     0x01b8);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_COLINFO',	     0x7d);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_DEFCOLWIDTH',  0x55);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_STANDARDWIDTH', 0x99);
define('OW_VOTE_SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT',	"%s");


/*
* Main Class
*/
class Wpvc_Spreadsheet_Excel_Reader {

	var $colnames = array();
	var $colindexes = array();
	var $standardColWidth = 0;
	var $defaultColWidth = 0;

	function wpvc_myHex($d) {
		if ($d < 16) return "0" . dechex($d);
		return dechex($d);
	}
	
	function wpvc_dumpHexData($data, $pos, $length) {
		$info = "";
		for ($i = 0; $i <= $length; $i++) {
			$info .= ($i==0?"":" ") . $this->wpvc_myHex(ord($data[$pos + $i])) . (ord($data[$pos + $i])>31? "[" . $data[$pos + $i] . "]":'');
		}
		return $info;
	}

	function wpvc_getCol($col) {
		if (is_string($col)) {
			$col = strtolower($col);
			if (array_key_exists($col,$this->colnames)) {
				$col = $this->colnames[$col];
			}
		}
		return $col;
	}

	function wpvc_val($row,$col,$sheet=0) {
		$col = $this->wpvc_getCol($col);
		if (array_key_exists($row,$this->sheets[$sheet]['cells']) && array_key_exists($col,$this->sheets[$sheet]['cells'][$row])) {
			return $this->sheets[$sheet]['cells'][$row][$col];
		}
		return "";
	}
	function wpvc_value($row,$col,$sheet=0) {
		return $this->wpvc_val($row,$col,$sheet);
	}
	function wpvc_info($row,$col,$type='',$sheet=0) {
		$col = $this->wpvc_getCol($col);
		if (array_key_exists('cellsInfo',$this->sheets[$sheet])
				&& array_key_exists($row,$this->sheets[$sheet]['cellsInfo'])
				&& array_key_exists($col,$this->sheets[$sheet]['cellsInfo'][$row])
				&& array_key_exists($type,$this->sheets[$sheet]['cellsInfo'][$row][$col])) {
			return $this->sheets[$sheet]['cellsInfo'][$row][$col][$type];
		}
		return "";
	}
	function wpvc_type($row,$col,$sheet=0) {
		return $this->wpvc_info($row,$col,'type',$sheet);
	}
	function wpvc_raw($row,$col,$sheet=0) {
		return $this->wpvc_info($row,$col,'raw',$sheet);
	}
	function wpvc_rowspan($row,$col,$sheet=0) {
		$val = $this->wpvc_info($row,$col,'rowspan',$sheet);
		if ($val=="") { return 1; }
		return $val;
	}
	function wpvc_colspan($row,$col,$sheet=0) {
		$val = $this->wpvc_info($row,$col,'colspan',$sheet);
		if ($val=="") { return 1; }
		return $val;
	}
	function wpvc_hyperlink($row,$col,$sheet=0) {
		$link = $this->sheets[$sheet]['cellsInfo'][$row][$col]['hyperlink'];
		if ($link) {
			return $link['link'];
		}
		return '';
	}
	function wpvc_rowcount($sheet=0) {
		return $this->sheets[$sheet]['numRows'];
	}
	function wpvc_colcount($sheet=0) {
		return $this->sheets[$sheet]['numCols'];
	}
	function wpvc_colwidth($col,$sheet=0) {
		// Col width is actually the width of the number 0. So we have to estimate and come close
		return $this->colInfo[$sheet][$col]['width']/9142*200; 
	}
	function wpvc_colhidden($col,$sheet=0) {
		return !!$this->colInfo[$sheet][$col]['hidden'];
	}
	function wpvc_rowheight($row,$sheet=0) {
		return $this->rowInfo[$sheet][$row]['height'];
	}
	function wpvc_rowhidden($row,$sheet=0) {
		return !!$this->rowInfo[$sheet][$row]['hidden'];
	}
	
	// GET THE CSS FOR FORMATTING
	// ==========================
	function wpvc_style($row,$col,$sheet=0,$properties='') {
		$css = "";
		$font=$this->wpvc_font($row,$col,$sheet);
		if ($font!="") {
			$css .= "font-family:$font;";
		}
		$align=$this->wpvc_align($row,$col,$sheet);
		if ($align!="") {
			$css .= "text-align:$align;";
		}
		$height=$this->wpvc_height($row,$col,$sheet);
		if ($height!="") {
			$css .= "font-size:$height"."px;";
		}
		$bgcolor=$this->wpvc_bgColor($row,$col,$sheet);
		if ($bgcolor!="") {
			$bgcolor = $this->colors[$bgcolor];
			$css .= "background-color:$bgcolor;";
		}
		$color=$this->wpvc_color($row,$col,$sheet);
		if ($color!="") {
			$css .= "color:$color;";
		}
		$bold=$this->wpvc_bold($row,$col,$sheet);
		if ($bold) {
			$css .= "font-weight:bold;";
		}
		$italic=$this->wpvc_italic($row,$col,$sheet);
		if ($italic) {
			$css .= "font-style:italic;";
		}
		$underline=$this->wpvc_underline($row,$col,$sheet);
		if ($underline) {
			$css .= "text-decoration:underline;";
		}
		// Borders
		$bLeft = $this->wpvc_borderLeft($row,$col,$sheet);
		$bRight = $this->wpvc_borderRight($row,$col,$sheet);
		$bTop = $this->wpvc_borderTop($row,$col,$sheet);
		$bBottom = $this->wpvc_borderBottom($row,$col,$sheet);
		$bLeftCol = $this->wpvc_borderLeftColor($row,$col,$sheet);
		$bRightCol = $this->wpvc_borderRightColor($row,$col,$sheet);
		$bTopCol = $this->wpvc_borderTopColor($row,$col,$sheet);
		$bBottomCol = $this->wpvc_borderBottomColor($row,$col,$sheet);
		// Try to output the minimal required style
		if ($bLeft!="" && $bLeft==$bRight && $bRight==$bTop && $bTop==$bBottom) {
			$css .= "border:" . $this->lineStylesCss[$bLeft] .";";
		}
		else {
			if ($bLeft!="") { $css .= "border-left:" . $this->lineStylesCss[$bLeft] .";"; }
			if ($bRight!="") { $css .= "border-right:" . $this->lineStylesCss[$bRight] .";"; }
			if ($bTop!="") { $css .= "border-top:" . $this->lineStylesCss[$bTop] .";"; }
			if ($bBottom!="") { $css .= "border-bottom:" . $this->lineStylesCss[$bBottom] .";"; }
		}
		// Only output border colors if there is an actual border specified
		if ($bLeft!="" && $bLeftCol!="") { $css .= "border-left-color:" . $bLeftCol .";"; }
		if ($bRight!="" && $bRightCol!="") { $css .= "border-right-color:" . $bRightCol .";"; }
		if ($bTop!="" && $bTopCol!="") { $css .= "border-top-color:" . $bTopCol . ";"; }
		if ($bBottom!="" && $bBottomCol!="") { $css .= "border-bottom-color:" . $bBottomCol .";"; }
		
		return $css;
	}
	
	// FORMAT PROPERTIES
	// =================
	function wpvc_format($row,$col,$sheet=0) {
		return $this->wpvc_info($row,$col,'format',$sheet);
	}
	function wpvc_formatIndex($row,$col,$sheet=0) {
		return $this->wpvc_info($row,$col,'formatIndex',$sheet);
	}
	function wpvc_formatColor($row,$col,$sheet=0) {
		return $this->wpvc_info($row,$col,'formatColor',$sheet);
	}
	
	// CELL (XF) PROPERTIES
	// ====================
	function wpvc_xfRecord($row,$col,$sheet=0) {
		$xfIndex = $this->wpvc_info($row,$col,'xfIndex',$sheet);
		if ($xfIndex!="") {
			return $this->xfRecords[$xfIndex];
		}
		return null;
	}
	function wpvc_xfProperty($row,$col,$sheet,$prop) {
		$xfRecord = $this->wpvc_xfRecord($row,$col,$sheet);
		if ($xfRecord!=null) {
			return $xfRecord[$prop];
		}
		return "";
	}
	function wpvc_align($row,$col,$sheet=0) {
		return $this->wpvc_xfProperty($row,$col,$sheet,'align');
	}
	function wpvc_bgColor($row,$col,$sheet=0) {
		return $this->wpvc_xfProperty($row,$col,$sheet,'bgColor');
	}
	function wpvc_borderLeft($row,$col,$sheet=0) {
		return $this->wpvc_xfProperty($row,$col,$sheet,'borderLeft');
	}
	function wpvc_borderRight($row,$col,$sheet=0) {
		return $this->wpvc_xfProperty($row,$col,$sheet,'borderRight');
	}
	function wpvc_borderTop($row,$col,$sheet=0) {
		return $this->wpvc_xfProperty($row,$col,$sheet,'borderTop');
	}
	function wpvc_borderBottom($row,$col,$sheet=0) {
		return $this->wpvc_xfProperty($row,$col,$sheet,'borderBottom');
	}
	function wpvc_borderLeftColor($row,$col,$sheet=0) {
		return $this->colors[$this->wpvc_xfProperty($row,$col,$sheet,'borderLeftColor')];
	}
	function wpvc_borderRightColor($row,$col,$sheet=0) {
		return $this->colors[$this->wpvc_xfProperty($row,$col,$sheet,'borderRightColor')];
	}
	function wpvc_borderTopColor($row,$col,$sheet=0) {
		return $this->colors[$this->wpvc_xfProperty($row,$col,$sheet,'borderTopColor')];
	}
	function wpvc_borderBottomColor($row,$col,$sheet=0) {
		return $this->colors[$this->wpvc_xfProperty($row,$col,$sheet,'borderBottomColor')];
	}

	// FONT PROPERTIES
	// ===============
	function wpvc_fontRecord($row,$col,$sheet=0) {
	    $xfRecord = $this->wpvc_xfRecord($row,$col,$sheet);
		if ($xfRecord!=null) {
			$font = $xfRecord['fontIndex'];
			if ($font!=null) {
				return $this->fontRecords[$font];
			}
		}
		return null;
	}
	function wpvc_fontProperty($row,$col,$sheet=0,$prop) {
		$font = $this->wpvc_fontRecord($row,$col,$sheet);
		if ($font!=null) {
			return $font[$prop];
		}
		return false;
	}
	function wpvc_fontIndex($row,$col,$sheet=0) {
		return $this->wpvc_xfProperty($row,$col,$sheet,'fontIndex');
	}
	function wpvc_color($row,$col,$sheet=0) {
		$formatColor = $this->wpvc_formatColor($row,$col,$sheet);
		if ($formatColor!="") {
			return $formatColor;
		}
		$ci = $this->wpvc_fontProperty($row,$col,$sheet,'color');
                return $this->rawColor($ci);
        }
        function rawColor($ci) {
		if (($ci <> 0x7FFF) && ($ci <> '')) {
			return $this->colors[$ci];
		}
		return "";
	}
	function wpvc_bold($row,$col,$sheet=0) {
		return $this->wpvc_fontProperty($row,$col,$sheet,'bold');
	}
	function wpvc_italic($row,$col,$sheet=0) {
		return $this->wpvc_fontProperty($row,$col,$sheet,'italic');
	}
	function wpvc_underline($row,$col,$sheet=0) {
		return $this->wpvc_fontProperty($row,$col,$sheet,'under');
	}
	function wpvc_height($row,$col,$sheet=0) {
		return $this->wpvc_fontProperty($row,$col,$sheet,'height');
	}
	function wpvc_font($row,$col,$sheet=0) {
		return $this->wpvc_fontProperty($row,$col,$sheet,'font');
	}
	
	// DUMP AN HTML TABLE OF THE ENTIRE XLS DATA
	// =========================================
	function wpvc_dump($row_numbers=false,$col_letters=false,$sheet=0,$table_class='excel') {
		$out = "<table class=\"$table_class\" cellspacing=0>";
		if ($col_letters) {
			$out .= "<thead>\n\t<tr>";
			if ($row_numbers) {
				$out .= "\n\t\t<th>&nbsp</th>";
			}
			for($i=1;$i<=$this->wpvc_colcount($sheet);$i++) {
				$style = "width:" . ($this->wpvc_colwidth($i,$sheet)*1) . "px;";
				if ($this->wpvc_colhidden($i,$sheet)) {
					$style .= "display:none;";
				}
				$out .= "\n\t\t<th style=\"$style\">" . strtoupper($this->colindexes[$i]) . "</th>";
			}
			$out .= "</tr></thead>\n";
		}
		
		$out .= "<tbody>\n";
		for($row=1;$row<=$this->wpvc_rowcount($sheet);$row++) {
			$rowheight = $this->wpvc_rowheight($row,$sheet);
			$style = "height:" . ($rowheight*(4/3)) . "px;";
			if ($this->wpvc_rowhidden($row,$sheet)) {
				$style .= "display:none;";
			}
			$out .= "\n\t<tr style=\"$style\">";
			if ($row_numbers) {
				$out .= "\n\t\t<th>$row</th>";
			}
			for($col=1;$col<=$this->wpvc_colcount($sheet);$col++) {
				// Account for Rowspans/Colspans
				$rowspan = $this->wpvc_rowspan($row,$col,$sheet);
				$colspan = $this->wpvc_colspan($row,$col,$sheet);
				for($i=0;$i<$rowspan;$i++) {
					for($j=0;$j<$colspan;$j++) {
						if ($i>0 || $j>0) {
							$this->sheets[$sheet]['cellsInfo'][$row+$i][$col+$j]['dontprint']=1;
						}
					}
				}
				if(!$this->sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
					$style = $this->wpvc_style($row,$col,$sheet);
					if ($this->wpvc_colhidden($col,$sheet)) {
						$style .= "display:none;";
					}
					$out .= "\n\t\t<td style=\"$style\"" . ($colspan > 1?" colspan=$colspan":"") . ($rowspan > 1?" rowspan=$rowspan":"") . ">";
					$val = $this->wpvc_val($row,$col,$sheet);
					if ($val=='') { $val="&nbsp;"; }
					else { 
						$val = htmlentities($val); 
						$link = $this->wpvc_hyperlink($row,$col,$sheet);
						if ($link!='') {
							$val = "<a href=\"$link\">$val</a>";
						}
					}
					$out .= "<nobr>".nl2br($val)."</nobr>";
					$out .= "</td>";
				}
			}
			$out .= "</tr>\n";
		}
		$out .= "</tbody></table>";
		return $out;
	}
	
	// --------------
	// END PUBLIC API


	var $boundsheets = array();
	var $formatRecords = array();
	var $fontRecords = array();
	var $xfRecords = array();
	var $colInfo = array();
   	var $rowInfo = array();
	
	var $sst = array();
	var $sheets = array();

	var $data;
	var $_ole;
	var $_defaultEncoding = "UTF-8";
	var $_defaultFormat = OW_VOTE_SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT;
	var $_columnsFormat = array();
	var $_rowoffset = 1;
	var $_coloffset = 1;

	/**
	 * List of default date formats used by Excel
	 */
	var $dateFormats = array (
		0xe => "m/d/Y",
		0xf => "M-d-Y",
		0x10 => "d-M",
		0x11 => "M-Y",
		0x12 => "h:i a",
		0x13 => "h:i:s a",
		0x14 => "H:i",
		0x15 => "H:i:s",
		0x16 => "d/m/Y H:i",
		0x2d => "i:s",
		0x2e => "H:i:s",
		0x2f => "i:s.S"
	);

	/**
	 * Default number formats used by Excel
	 */
	var $numberFormats = array(
		0x1 => "0",
		0x2 => "0.00",
		0x3 => "#,##0",
		0x4 => "#,##0.00",
		0x5 => "\$#,##0;(\$#,##0)",
		0x6 => "\$#,##0;[Red](\$#,##0)",
		0x7 => "\$#,##0.00;(\$#,##0.00)",
		0x8 => "\$#,##0.00;[Red](\$#,##0.00)",
		0x9 => "0%",
		0xa => "0.00%",
		0xb => "0.00E+00",
		0x25 => "#,##0;(#,##0)",
		0x26 => "#,##0;[Red](#,##0)",
		0x27 => "#,##0.00;(#,##0.00)",
		0x28 => "#,##0.00;[Red](#,##0.00)",
		0x29 => "#,##0;(#,##0)",  // Not exactly
		0x2a => "\$#,##0;(\$#,##0)",  // Not exactly
		0x2b => "#,##0.00;(#,##0.00)",  // Not exactly
		0x2c => "\$#,##0.00;(\$#,##0.00)",  // Not exactly
		0x30 => "##0.0E+0"
	);

    var $colors = Array(
        0x00 => "#000000",
        0x01 => "#FFFFFF",
        0x02 => "#FF0000",
        0x03 => "#00FF00",
        0x04 => "#0000FF",
        0x05 => "#FFFF00",
        0x06 => "#FF00FF",
        0x07 => "#00FFFF",
        0x08 => "#000000",
        0x09 => "#FFFFFF",
        0x0A => "#FF0000",
        0x0B => "#00FF00",
        0x0C => "#0000FF",
        0x0D => "#FFFF00",
        0x0E => "#FF00FF",
        0x0F => "#00FFFF",
        0x10 => "#800000",
        0x11 => "#008000",
        0x12 => "#000080",
        0x13 => "#808000",
        0x14 => "#800080",
        0x15 => "#008080",
        0x16 => "#C0C0C0",
        0x17 => "#808080",
        0x18 => "#9999FF",
        0x19 => "#993366",
        0x1A => "#FFFFCC",
        0x1B => "#CCFFFF",
        0x1C => "#660066",
        0x1D => "#FF8080",
        0x1E => "#0066CC",
        0x1F => "#CCCCFF",
        0x20 => "#000080",
        0x21 => "#FF00FF",
        0x22 => "#FFFF00",
        0x23 => "#00FFFF",
        0x24 => "#800080",
        0x25 => "#800000",
        0x26 => "#008080",
        0x27 => "#0000FF",
        0x28 => "#00CCFF",
        0x29 => "#CCFFFF",
        0x2A => "#CCFFCC",
        0x2B => "#FFFF99",
        0x2C => "#99CCFF",
        0x2D => "#FF99CC",
        0x2E => "#CC99FF",
        0x2F => "#FFCC99",
        0x30 => "#3366FF",
        0x31 => "#33CCCC",
        0x32 => "#99CC00",
        0x33 => "#FFCC00",
        0x34 => "#FF9900",
        0x35 => "#FF6600",
        0x36 => "#666699",
        0x37 => "#969696",
        0x38 => "#003366",
        0x39 => "#339966",
        0x3A => "#003300",
        0x3B => "#333300",
        0x3C => "#993300",
        0x3D => "#993366",
        0x3E => "#333399",
        0x3F => "#333333",
        0x40 => "#000000",
        0x41 => "#FFFFFF",

        0x43 => "#000000",
        0x4D => "#000000",
        0x4E => "#FFFFFF",
        0x4F => "#000000",
        0x50 => "#FFFFFF",
        0x51 => "#000000",

        0x7FFF => "#000000"
    );

	var $lineStyles = array(
		0x00 => "",
		0x01 => "Thin",
		0x02 => "Medium",
		0x03 => "Dashed",
		0x04 => "Dotted",
		0x05 => "Thick",
		0x06 => "Double",
		0x07 => "Hair",
		0x08 => "Medium dashed",
		0x09 => "Thin dash-dotted",
		0x0A => "Medium dash-dotted",
		0x0B => "Thin dash-dot-dotted",
		0x0C => "Medium dash-dot-dotted",
		0x0D => "Slanted medium dash-dotted"
	);	

	var $lineStylesCss = array(
		"Thin" => "1px solid", 
		"Medium" => "2px solid", 
		"Dashed" => "1px dashed", 
		"Dotted" => "1px dotted", 
		"Thick" => "3px solid", 
		"Double" => "double", 
		"Hair" => "1px solid", 
		"Medium dashed" => "2px dashed", 
		"Thin dash-dotted" => "1px dashed", 
		"Medium dash-dotted" => "2px dashed", 
		"Thin dash-dot-dotted" => "1px dashed", 
		"Medium dash-dot-dotted" => "2px dashed", 
		"Slanted medium dash-dotte" => "2px dashed" 
	);
	
	function wpvc_read16bitstring($data, $start) {
		$len = 0;
		while (ord($data[$start + $len]) + ord($data[$start + $len + 1]) > 0) $len++;
		return substr($data, $start, $len);
	}
	
	// ADDED by Matt Kruse for better formatting
	function _wpvc_format_value($format,$num,$f) {
		// 49==TEXT format
		// http://code.google.com/p/php-excel-reader/issues/detail?id=7
		if ( (!$f && $format=="%s") || ($f==49) || ($format=="GENERAL") ) { 
			return array('string'=>$num, 'formatColor'=>null); 
		}

		// Custom pattern can be POSITIVE;NEGATIVE;ZERO
		// The "text" option as 4th parameter is not handled
		$parts = explode(";",$format);
		$pattern = $parts[0];
		// Negative pattern
		if (count($parts)>2 && $num==0) {
			$pattern = $parts[2];
		}
		// Zero pattern
		if (count($parts)>1 && $num<0) {
			$pattern = $parts[1];
			$num = abs($num);
		}

		$color = "";
		$matches = array();
		$color_regex = "/^\[(BLACK|BLUE|CYAN|GREEN|MAGENTA|RED|WHITE|YELLOW)\]/i";
		if (preg_match($color_regex,$pattern,$matches)) {
			$color = strtolower($matches[1]);
			$pattern = preg_replace($color_regex,"",$pattern);
		}
		
		// In Excel formats, "_" is used to add spacing, which we can't do in HTML
		$pattern = preg_replace("/_./","",$pattern);
		
		// Some non-number characters are escaped with \, which we don't need
		$pattern = preg_replace("/\\\/","",$pattern);
		
		// Some non-number strings are quoted, so we'll get rid of the quotes
		$pattern = preg_replace("/\"/","",$pattern);

		// TEMPORARY - Convert # to 0
		$pattern = preg_replace("/\#/","0",$pattern);

		// Find out if we need comma formatting
		$has_commas = preg_match("/,/",$pattern);
		if ($has_commas) {
			$pattern = preg_replace("/,/","",$pattern);
		}

		// Handle Percentages
		if (preg_match("/\d(\%)([^\%]|$)/",$pattern,$matches)) {
			$num = $num * 100;
			$pattern = preg_replace("/(\d)(\%)([^\%]|$)/","$1%$3",$pattern);
		}

		// Handle the number itself
		$number_regex = "/(\d+)(\.?)(\d*)/";
		if (preg_match($number_regex,$pattern,$matches)) {
			$left = $matches[1];
			$dec = $matches[2];
			$right = $matches[3];
			if ($has_commas) {
				$formatted = number_format($num,strlen($right));
			}
			else {
				$sprintf_pattern = "%1.".strlen($right)."f";
				$formatted = sprintf($sprintf_pattern, $num);
			}
			$pattern = preg_replace($number_regex, $formatted, $pattern);
		}

		return array(
			'string'=>$pattern,
			'formatColor'=>$color
		);
	}

	/**
	 * Constructor
	 *
	 * Some basic initialisation
	 */
	function __construct($file='',$store_extended_info=true,$outputEncoding='') {
		$this->_ole = new Wpvc_OLERead();
		$this->wpvc_setUTFEncoder('iconv');
		if ($outputEncoding != '') { 
			$this->wpvc_setOutputEncoding($outputEncoding);
		}
		for ($i=1; $i<245; $i++) {
			$name = strtolower(( (($i-1)/26>=1)?chr(($i-1)/26+64):'') . chr(($i-1)%26+65));
			$this->colnames[$name] = $i;
			$this->colindexes[$i] = $name;
		}
		$this->store_extended_info = $store_extended_info;
		if ($file!="") {
			$this->wpvc_read($file);
		}
	}

	/**
	 * Set the encoding method
	 */
	function wpvc_setOutputEncoding($encoding) {
		$this->_defaultEncoding = $encoding;
	}

	/**
	 *  $encoder = 'iconv' or 'mb'
	 *  set iconv if you would like use 'iconv' for encode UTF-16LE to your encoding
	 *  set mb if you would like use 'mb_convert_encoding' for encode UTF-16LE to your encoding
	 */
	function wpvc_setUTFEncoder($encoder = 'iconv') {
		$this->_encoderFunction = '';
		if ($encoder == 'iconv') {
			$this->_encoderFunction = function_exists('iconv') ? 'iconv' : '';
		} elseif ($encoder == 'mb') {
			$this->_encoderFunction = function_exists('mb_convert_encoding') ? 'mb_convert_encoding' : '';
		}
	}

	function wpvc_setRowColOffset($iOffset) {
		$this->_rowoffset = $iOffset;
		$this->_coloffset = $iOffset;
	}

	/**
	 * Set the default number format
	 */
	function wpvc_setDefaultFormat($sFormat) {
		$this->_defaultFormat = $sFormat;
	}

	/**
	 * Force a column to use a certain format
	 */
	function wpvc_setColumnFormat($column, $sFormat) {
		$this->_columnsFormat[$column] = $sFormat;
	}

	/**
	 * Read the spreadsheet file using OLE, then parse
	 */
	function wpvc_read($sFileName) {
		$res = $this->_ole->wpvc_read($sFileName);

		// oops, something goes wrong (Darko Miljanovic)
		if($res === false) {
			// check error code
			if($this->_ole->error == 1) {
				// bad file
				die('The filename ' . $sFileName . ' is not readable');
			}
			// check other error codes here (eg bad fileformat, etc...)
		}
		$this->data = $this->_ole->wpvc_getWorkBook();
		$this->_wp_vote_parse();
	}

	/**
	 * Parse a workbook
	 *
	 * @access private
	 * @return bool
	 */
	function _wp_vote_parse() {
		$pos = 0;
		$data = $this->data;

		$code = ow_votes_v($data,$pos);
		$length = ow_votes_v($data,$pos+2);
		$version = ow_votes_v($data,$pos+4);
		$substreamType = ow_votes_v($data,$pos+6);

		$this->version = $version;

		if (($version != OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF8) &&
			($version != OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF7)) {
			return false;
		}

		if ($substreamType != OW_VOTE_SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS){
			return false;
		}

		$pos += $length + 4;

		$code = ow_votes_v($data,$pos);
		$length = ow_votes_v($data,$pos+2);

		while ($code != OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_EOF) {
			switch ($code) {
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_SST:
					$spos = $pos + 4;
					$limitpos = $spos + $length;
					$uniqueStrings = $this->_wpvc_GetInt4d($data, $spos+4);
					$spos += 8;
					for ($i = 0; $i < $uniqueStrings; $i++) {
						// Read in the number of characters
						if ($spos == $limitpos) {
							$opcode = ow_votes_v($data,$spos);
							$conlength = ow_votes_v($data,$spos+2);
							if ($opcode != 0x3c) {
								return -1;
							}
							$spos += 4;
							$limitpos = $spos + $conlength;
						}
						$numChars = ord($data[$spos]) | (ord($data[$spos+1]) << 8);
						$spos += 2;
						$optionFlags = ord($data[$spos]);
						$spos++;
						$asciiEncoding = (($optionFlags & 0x01) == 0) ;
						$extendedString = ( ($optionFlags & 0x04) != 0);

						// See if string contains formatting information
						$richString = ( ($optionFlags & 0x08) != 0);

						if ($richString) {
							// Read in the crun
							$formattingRuns = ow_votes_v($data,$spos);
							$spos += 2;
						}

						if ($extendedString) {
							// Read in cchExtRst
							$extendedRunLength = $this->_wpvc_GetInt4d($data, $spos);
							$spos += 4;
						}

						$len = ($asciiEncoding)? $numChars : $numChars*2;
						if ($spos + $len < $limitpos) {
							$retstr = substr($data, $spos, $len);
							$spos += $len;
						}
						else{
							// found countinue
							$retstr = substr($data, $spos, $limitpos - $spos);
							$bytesRead = $limitpos - $spos;
							$charsLeft = $numChars - (($asciiEncoding) ? $bytesRead : ($bytesRead / 2));
							$spos = $limitpos;

							while ($charsLeft > 0){
								$opcode = ow_votes_v($data,$spos);
								$conlength = ow_votes_v($data,$spos+2);
								if ($opcode != 0x3c) {
									return -1;
								}
								$spos += 4;
								$limitpos = $spos + $conlength;
								$option = ord($data[$spos]);
								$spos += 1;
								if ($asciiEncoding && ($option == 0)) {
									$len = min($charsLeft, $limitpos - $spos); // min($charsLeft, $conlength);
									$retstr .= substr($data, $spos, $len);
									$charsLeft -= $len;
									$asciiEncoding = true;
								}
								elseif (!$asciiEncoding && ($option != 0)) {
									$len = min($charsLeft * 2, $limitpos - $spos); // min($charsLeft, $conlength);
									$retstr .= substr($data, $spos, $len);
									$charsLeft -= $len/2;
									$asciiEncoding = false;
								}
								elseif (!$asciiEncoding && ($option == 0)) {
									// Bummer - the string starts off as Unicode, but after the
									// continuation it is in straightforward ASCII encoding
									$len = min($charsLeft, $limitpos - $spos); // min($charsLeft, $conlength);
									for ($j = 0; $j < $len; $j++) {
										$retstr .= $data[$spos + $j].chr(0);
									}
									$charsLeft -= $len;
									$asciiEncoding = false;
								}
								else{
									$newstr = '';
									for ($j = 0; $j < strlen($retstr); $j++) {
										$newstr = $retstr[$j].chr(0);
									}
									$retstr = $newstr;
									$len = min($charsLeft * 2, $limitpos - $spos); // min($charsLeft, $conlength);
									$retstr .= substr($data, $spos, $len);
									$charsLeft -= $len/2;
									$asciiEncoding = false;
								}
								$spos += $len;
							}
						}
						$retstr = ($asciiEncoding) ? $retstr : $this->_wpvc_encodeUTF16($retstr);

						if ($richString){
							$spos += 4 * $formattingRuns;
						}

						// For extended strings, skip over the extended string data
						if ($extendedString) {
							$spos += $extendedRunLength;
						}
						$this->sst[]=$retstr;
					}
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FILEPASS:
					return false;
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_NAME:
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FORMAT:
					$indexCode = ow_votes_v($data,$pos+4);
					if ($version == OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF8) {
						$numchars = ow_votes_v($data,$pos+6);
						if (ord($data[$pos+8]) == 0){
							$formatString = substr($data, $pos+9, $numchars);
						} else {
							$formatString = substr($data, $pos+9, $numchars*2);
						}
					} else {
						$numchars = ord($data[$pos+6]);
						$formatString = substr($data, $pos+7, $numchars*2);
					}
					$this->formatRecords[$indexCode] = $formatString;
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FONT:
						$height = ow_votes_v($data,$pos+4);
						$option = ow_votes_v($data,$pos+6);
						$color = ow_votes_v($data,$pos+8);
						$weight = ow_votes_v($data,$pos+10);
						$under  = ord($data[$pos+14]);
						$font = "";
						// Font name
						$numchars = ord($data[$pos+18]);
						if ((ord($data[$pos+19]) & 1) == 0){
						    $font = substr($data, $pos+20, $numchars);
						} else {
						    $font = substr($data, $pos+20, $numchars*2);
						    $font =  $this->_wpvc_encodeUTF16($font); 
						}
						$this->fontRecords[] = array(
								'height' => $height / 20,
								'italic' => !!($option & 2),
								'color' => $color,
								'under' => !($under==0),
								'bold' => ($weight==700),
								'font' => $font,
								'raw' => $this->wpvc_dumpHexData($data, $pos+3, $length)
								);
					    break;

				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_PALETTE:
						$colors = ord($data[$pos+4]) | ord($data[$pos+5]) << 8;
						for ($coli = 0; $coli < $colors; $coli++) {
						    $colOff = $pos + 2 + ($coli * 4);
  						    $colr = ord($data[$colOff]);
  						    $colg = ord($data[$colOff+1]);
  						    $colb = ord($data[$colOff+2]);
							$this->colors[0x07 + $coli] = '#' . $this->myhex($colr) . $this->myhex($colg) . $this->myhex($colb);
						}
					    break;

				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_XF:
						$fontIndexCode = (ord($data[$pos+4]) | ord($data[$pos+5]) << 8) - 1;
						$fontIndexCode = max(0,$fontIndexCode);
						$indexCode = ord($data[$pos+6]) | ord($data[$pos+7]) << 8;
						$alignbit = ord($data[$pos+10]) & 3;
						$bgi = (ord($data[$pos+22]) | ord($data[$pos+23]) << 8) & 0x3FFF;
						$bgcolor = ($bgi & 0x7F);
//						$bgcolor = ($bgi & 0x3f80) >> 7;
						$align = "";
						if ($alignbit==3) { $align="right"; }
						if ($alignbit==2) { $align="center"; }

						$fillPattern = (ord($data[$pos+21]) & 0xFC) >> 2;
						if ($fillPattern == 0) {
							$bgcolor = "";
						}

						$xf = array();
						$xf['formatIndex'] = $indexCode;
						$xf['align'] = $align;
						$xf['fontIndex'] = $fontIndexCode;
						$xf['bgColor'] = $bgcolor;
						$xf['fillPattern'] = $fillPattern;

						$border = ord($data[$pos+14]) | (ord($data[$pos+15]) << 8) | (ord($data[$pos+16]) << 16) | (ord($data[$pos+17]) << 24);
						$xf['borderLeft'] = $this->lineStyles[($border & 0xF)];
						$xf['borderRight'] = $this->lineStyles[($border & 0xF0) >> 4];
						$xf['borderTop'] = $this->lineStyles[($border & 0xF00) >> 8];
						$xf['borderBottom'] = $this->lineStyles[($border & 0xF000) >> 12];
						
						$xf['borderLeftColor'] = ($border & 0x7F0000) >> 16;
						$xf['borderRightColor'] = ($border & 0x3F800000) >> 23;
						$border = (ord($data[$pos+18]) | ord($data[$pos+19]) << 8);

						$xf['borderTopColor'] = ($border & 0x7F);
						$xf['borderBottomColor'] = ($border & 0x3F80) >> 7;
												
						if (array_key_exists($indexCode, $this->dateFormats)) {
							$xf['type'] = 'date';
							$xf['format'] = $this->dateFormats[$indexCode];
							if ($align=='') { $xf['align'] = 'right'; }
						}elseif (array_key_exists($indexCode, $this->numberFormats)) {
							$xf['type'] = 'number';
							$xf['format'] = $this->numberFormats[$indexCode];
							if ($align=='') { $xf['align'] = 'right'; }
						}else{
							$isdate = FALSE;
							$formatstr = '';
							if ($indexCode > 0){
								if (isset($this->formatRecords[$indexCode]))
									$formatstr = $this->formatRecords[$indexCode];
								if ($formatstr!="") {
									$tmp = preg_replace("/\;.*/","",$formatstr);
									$tmp = preg_replace("/^\[[^\]]*\]/","",$tmp);
									if (preg_match("/[^hmsday\/\-:\s\\\,AMP]/i", $tmp) == 0) { // found day and time format
										$isdate = TRUE;
										$formatstr = $tmp;
										$formatstr = str_replace(array('AM/PM','mmmm','mmm'), array('a','F','M'), $formatstr);
										// m/mm are used for both minutes and months - oh SNAP!
										// This mess tries to fix for that.
										// 'm' == minutes only if following h/hh or preceding s/ss
										$formatstr = preg_replace("/(h:?)mm?/","$1i", $formatstr);
										$formatstr = preg_replace("/mm?(:?s)/","i$1", $formatstr);
										// A single 'm' = n in PHP
										$formatstr = preg_replace("/(^|[^m])m([^m]|$)/", '$1n$2', $formatstr);
										$formatstr = preg_replace("/(^|[^m])m([^m]|$)/", '$1n$2', $formatstr);
										// else it's months
										$formatstr = str_replace('mm', 'm', $formatstr);
										// Convert single 'd' to 'j'
										$formatstr = preg_replace("/(^|[^d])d([^d]|$)/", '$1j$2', $formatstr);
										$formatstr = str_replace(array('dddd','ddd','dd','yyyy','yy','hh','h'), array('l','D','d','Y','y','H','g'), $formatstr);
										$formatstr = preg_replace("/ss?/", 's', $formatstr);
									}
								}
							}
							if ($isdate){
								$xf['type'] = 'date';
								$xf['format'] = $formatstr;
								if ($align=='') { $xf['align'] = 'right'; }
							}else{
								// If the format string has a 0 or # in it, we'll assume it's a number
								if (preg_match("/[0#]/", $formatstr)) {
									$xf['type'] = 'number';
									if ($align=='') { $xf['align']='right'; }
								}
								else {
								$xf['type'] = 'other';
								}
								$xf['format'] = $formatstr;
								$xf['code'] = $indexCode;
							}
						}
						$this->xfRecords[] = $xf;
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR:
					$this->nineteenFour = (ord($data[$pos+4]) == 1);
					break;
				case Ow_VOTE_SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET:
						$rec_offset = $this->_wpvc_GetInt4d($data, $pos+4);
						$rec_typeFlag = ord($data[$pos+8]);
						$rec_visibilityFlag = ord($data[$pos+9]);
						$rec_length = ord($data[$pos+10]);

						if ($version == OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF8){
							$chartype =  ord($data[$pos+11]);
							if ($chartype == 0){
								$rec_name	= substr($data, $pos+12, $rec_length);
							} else {
								$rec_name	= $this->_wpvc_encodeUTF16(substr($data, $pos+12, $rec_length*2));
							}
						}elseif ($version == OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF7){
								$rec_name	= substr($data, $pos+11, $rec_length);
						}
					$this->boundsheets[] = array('name'=>$rec_name,'offset'=>$rec_offset);
					break;

			}

			$pos += $length + 4;
			$code = ord($data[$pos]) | ord($data[$pos+1])<<8;
			$length = ord($data[$pos+2]) | ord($data[$pos+3])<<8;
		}

		foreach ($this->boundsheets as $key=>$val){
			$this->sn = $key;
			$this->_wp_vote_parsesheet($val['offset']);
		}
		return true;
	}

	/**
	 * Parse a worksheet
	 */
	function _wp_vote_parsesheet($spos) {
		$cont = true;
		$data = $this->data;
		// read BOF
		$code = ord($data[$spos]) | ord($data[$spos+1])<<8;
		$length = ord($data[$spos+2]) | ord($data[$spos+3])<<8;

		$version = ord($data[$spos + 4]) | ord($data[$spos + 5])<<8;
		$substreamType = ord($data[$spos + 6]) | ord($data[$spos + 7])<<8;

		if (($version != OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF8) && ($version != OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF7)) {
			return -1;
		}

		if ($substreamType != OW_VOTE_SPREADSHEET_EXCEL_READER_WORKSHEET){
			return -2;
		}
		$spos += $length + 4;
		while($cont) {
			$lowcode = ord($data[$spos]);
			if ($lowcode == OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_EOF) break;
			$code = $lowcode | ord($data[$spos+1])<<8;
			$length = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
			$spos += 4;
			$this->sheets[$this->sn]['maxrow'] = $this->_rowoffset - 1;
			$this->sheets[$this->sn]['maxcol'] = $this->_coloffset - 1;
			unset($this->rectype);
			switch ($code) {
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_DIMENSION:
					if (!isset($this->numRows)) {
						if (($length == 10) ||  ($version == OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF7)){
							$this->sheets[$this->sn]['numRows'] = ord($data[$spos+2]) | ord($data[$spos+3]) << 8;
							$this->sheets[$this->sn]['numCols'] = ord($data[$spos+6]) | ord($data[$spos+7]) << 8;
						} else {
							$this->sheets[$this->sn]['numRows'] = ord($data[$spos+4]) | ord($data[$spos+5]) << 8;
							$this->sheets[$this->sn]['numCols'] = ord($data[$spos+10]) | ord($data[$spos+11]) << 8;
						}
					}
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS:
					$cellRanges = ord($data[$spos]) | ord($data[$spos+1])<<8;
					for ($i = 0; $i < $cellRanges; $i++) {
						$fr =  ord($data[$spos + 8*$i + 2]) | ord($data[$spos + 8*$i + 3])<<8;
						$lr =  ord($data[$spos + 8*$i + 4]) | ord($data[$spos + 8*$i + 5])<<8;
						$fc =  ord($data[$spos + 8*$i + 6]) | ord($data[$spos + 8*$i + 7])<<8;
						$lc =  ord($data[$spos + 8*$i + 8]) | ord($data[$spos + 8*$i + 9])<<8;
						if ($lr - $fr > 0) {
							$this->sheets[$this->sn]['cellsInfo'][$fr+1][$fc+1]['rowspan'] = $lr - $fr + 1;
						}
						if ($lc - $fc > 0) {
							$this->sheets[$this->sn]['cellsInfo'][$fr+1][$fc+1]['colspan'] = $lc - $fc + 1;
						}
					}
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_RK:
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_RK2:
					$row = ord($data[$spos]) | ord($data[$spos+1])<<8;
					$column = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
					$rknum = $this->_wpvc_GetInt4d($data, $spos + 6);
					$numValue = $this->_wpvc_GetIEEE754($rknum);
					$info = $this->_wpvc_getCellDetails($spos,$numValue,$column);
					$this->wpvc_addcell($row, $column, $info['string'],$info);
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_LABELSST:
					$row		= ord($data[$spos]) | ord($data[$spos+1])<<8;
					$column	 = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
					$xfindex	= ord($data[$spos+4]) | ord($data[$spos+5])<<8;
					$index  = $this->_wpvc_GetInt4d($data, $spos + 6);
					$this->wpvc_addcell($row, $column, $this->sst[$index], array('xfIndex'=>$xfindex) );
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_MULRK:
					$row		= ord($data[$spos]) | ord($data[$spos+1])<<8;
					$colFirst   = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
					$colLast	= ord($data[$spos + $length - 2]) | ord($data[$spos + $length - 1])<<8;
					$columns	= $colLast - $colFirst + 1;
					$tmppos = $spos+4;
					for ($i = 0; $i < $columns; $i++) {
						$numValue = $this->_wpvc_GetIEEE754($this->_wpvc_GetInt4d($data, $tmppos + 2));
						$info = $this->_wpvc_getCellDetails($tmppos-4,$numValue,$colFirst + $i + 1);
						$tmppos += 6;
						$this->wpvc_addcell($row, $colFirst + $i, $info['string'], $info);
					}
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_NUMBER:
					$row	= ord($data[$spos]) | ord($data[$spos+1])<<8;
					$column = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
					$tmp = unpack("ddouble", substr($data, $spos + 6, 8)); // It machine machine dependent
					if ($this->wpvc_isDate($spos)) {
						$numValue = $tmp['double'];
					}
					else {
						$numValue = $this->wpvc_createNumber($spos);
					}
					$info = $this->_wpvc_getCellDetails($spos,$numValue,$column);
					$this->wpvc_addcell($row, $column, $info['string'], $info);
					break;

				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FORMULA:
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_FORMULA2:
					$row	= ord($data[$spos]) | ord($data[$spos+1])<<8;
					$column = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
					if ((ord($data[$spos+6])==0) && (ord($data[$spos+12])==255) && (ord($data[$spos+13])==255)) {
						//String formula. Result follows in a STRING record
						// This row/col are stored to be referenced in that record
						// http://code.google.com/p/php-excel-reader/issues/detail?id=4
						$previousRow = $row;
						$previousCol = $column;
					} elseif ((ord($data[$spos+6])==1) && (ord($data[$spos+12])==255) && (ord($data[$spos+13])==255)) {
						//Boolean formula. Result is in +2; 0=false,1=true
						// http://code.google.com/p/php-excel-reader/issues/detail?id=4
                        if (ord($this->data[$spos+8])==1) {
                            $this->wpvc_addcell($row, $column, "TRUE");
                        } else {
                            $this->wpvc_addcell($row, $column, "FALSE");
                        }
					} elseif ((ord($data[$spos+6])==2) && (ord($data[$spos+12])==255) && (ord($data[$spos+13])==255)) {
						//Error formula. Error code is in +2;
					} elseif ((ord($data[$spos+6])==3) && (ord($data[$spos+12])==255) && (ord($data[$spos+13])==255)) {
						//Formula result is a null string.
						$this->wpvc_addcell($row, $column, '');
					} else {
						// result is a number, so first 14 bytes are just like a _NUMBER record
						$tmp = unpack("ddouble", substr($data, $spos + 6, 8)); // It machine machine dependent
							  if ($this->wpvc_isDate($spos)) {
								$numValue = $tmp['double'];
							  }
							  else {
								$numValue = $this->wpvc_createNumber($spos);
							  }
						$info = $this->_wpvc_getCellDetails($spos,$numValue,$column);
						$this->wpvc_addcell($row, $column, $info['string'], $info);
					}
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_BOOLERR:
					$row	= ord($data[$spos]) | ord($data[$spos+1])<<8;
					$column = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
					$string = ord($data[$spos+6]);
					$this->wpvc_addcell($row, $column, $string);
					break;
                case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_STRING:
					// http://code.google.com/p/php-excel-reader/issues/detail?id=4
					if ($version == OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF8){
						// Unicode 16 string, like an SST record
						$xpos = $spos;
						$numChars =ord($data[$xpos]) | (ord($data[$xpos+1]) << 8);
						$xpos += 2;
						$optionFlags =ord($data[$xpos]);
						$xpos++;
						$asciiEncoding = (($optionFlags &0x01) == 0) ;
						$extendedString = (($optionFlags & 0x04) != 0);
                        // See if string contains formatting information
						$richString = (($optionFlags & 0x08) != 0);
						if ($richString) {
							// Read in the crun
							$formattingRuns =ord($data[$xpos]) | (ord($data[$xpos+1]) << 8);
							$xpos += 2;
						}
						if ($extendedString) {
							// Read in cchExtRst
							$extendedRunLength =$this->_wpvc_GetInt4d($this->data, $xpos);
							$xpos += 4;
						}
						$len = ($asciiEncoding)?$numChars : $numChars*2;
						$retstr =substr($data, $xpos, $len);
						$xpos += $len;
						$retstr = ($asciiEncoding)? $retstr : $this->_wpvc_encodeUTF16($retstr);
					}
					elseif ($version == OW_VOTE_SPREADSHEET_EXCEL_READER_BIFF7){
						// Simple byte string
						$xpos = $spos;
						$numChars =ord($data[$xpos]) | (ord($data[$xpos+1]) << 8);
						$xpos += 2;
						$retstr =substr($data, $xpos, $numChars);
					}
					$this->wpvc_addcell($previousRow, $previousCol, $retstr);
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_ROW:
					$row	= ord($data[$spos]) | ord($data[$spos+1])<<8;
					$rowInfo = ord($data[$spos + 6]) | ((ord($data[$spos+7]) << 8) & 0x7FFF);
					if (($rowInfo & 0x8000) > 0) {
						$rowHeight = -1;
					} else {
						$rowHeight = $rowInfo & 0x7FFF;
					}
					$rowHidden = (ord($data[$spos + 12]) & 0x20) >> 5;
					$this->rowInfo[$this->sn][$row+1] = Array('height' => $rowHeight / 20, 'hidden'=>$rowHidden );
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_DBCELL:
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_MULBLANK:
					$row = ord($data[$spos]) | ord($data[$spos+1])<<8;
					$column = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
					$cols = ($length / 2) - 3;
					for ($c = 0; $c < $cols; $c++) {
						$xfindex = ord($data[$spos + 4 + ($c * 2)]) | ord($data[$spos + 5 + ($c * 2)])<<8;
						$this->wpvc_addcell($row, $column + $c, "", array('xfIndex'=>$xfindex));
					}
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_LABEL:
					$row	= ord($data[$spos]) | ord($data[$spos+1])<<8;
					$column = ord($data[$spos+2]) | ord($data[$spos+3])<<8;
					$this->wpvc_addcell($row, $column, substr($data, $spos + 8, ord($data[$spos + 6]) | ord($data[$spos + 7])<<8));
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_EOF:
					$cont = false;
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_HYPER:
					//  Only handle hyperlinks to a URL
					$row	= ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$row2   = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$column = ord($this->data[$spos+4]) | ord($this->data[$spos+5])<<8;
					$column2 = ord($this->data[$spos+6]) | ord($this->data[$spos+7])<<8;
					$linkdata = Array();
					$flags = ord($this->data[$spos + 28]);
					$udesc = "";
					$ulink = "";
					$uloc = 32;
					$linkdata['flags'] = $flags;
					if (($flags & 1) > 0 ) {   // is a type we understand
						//  is there a description ?
						if (($flags & 0x14) == 0x14 ) {   // has a description
							$uloc += 4;
							$descLen = ord($this->data[$spos + 32]) | ord($this->data[$spos + 33]) << 8;
							$udesc = substr($this->data, $spos + $uloc, $descLen * 2);
							$uloc += 2 * $descLen;
						}
						$ulink = $this->wpvc_read16bitstring($this->data, $spos + $uloc + 20);
						if ($udesc == "") {
							$udesc = $ulink;
						}
					}
					$linkdata['desc'] = $udesc;
					$linkdata['link'] = $this->_wpvc_encodeUTF16($ulink);
					for ($r=$row; $r<=$row2; $r++) { 
						for ($c=$column; $c<=$column2; $c++) {
							$this->sheets[$this->sn]['cellsInfo'][$r+1][$c+1]['hyperlink'] = $linkdata;
						}
					}
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_DEFCOLWIDTH:
					$this->defaultColWidth  = ord($data[$spos+4]) | ord($data[$spos+5]) << 8; 
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_STANDARDWIDTH:
					$this->standardColWidth  = ord($data[$spos+4]) | ord($data[$spos+5]) << 8; 
					break;
				case OW_VOTE_SPREADSHEET_EXCEL_READER_TYPE_COLINFO:
					$colfrom = ord($data[$spos+0]) | ord($data[$spos+1]) << 8;
					$colto = ord($data[$spos+2]) | ord($data[$spos+3]) << 8;
					$cw = ord($data[$spos+4]) | ord($data[$spos+5]) << 8; 
					$cxf = ord($data[$spos+6]) | ord($data[$spos+7]) << 8; 
					$co = ord($data[$spos+8]); 
					for ($coli = $colfrom; $coli <= $colto; $coli++) {
						$this->colInfo[$this->sn][$coli+1] = Array('width' => $cw, 'xf' => $cxf, 'hidden' => ($co & 0x01), 'collapsed' => ($co & 0x1000) >> 12);
					}
					break;

				default:
					break;
			}
			$spos += $length;
		}

		if (!isset($this->sheets[$this->sn]['numRows']))
			 $this->sheets[$this->sn]['numRows'] = $this->sheets[$this->sn]['maxrow'];
		if (!isset($this->sheets[$this->sn]['numCols']))
			 $this->sheets[$this->sn]['numCols'] = $this->sheets[$this->sn]['maxcol'];
		}

		function wpvc_isDate($spos) {
			$xfindex = ord($this->data[$spos+4]) | ord($this->data[$spos+5]) << 8;
			return ($this->xfRecords[$xfindex]['type'] == 'date');
		}

		// Get the details for a particular cell
		function _wpvc_getCellDetails($spos,$numValue,$column) {
			$xfindex = ord($this->data[$spos+4]) | ord($this->data[$spos+5]) << 8;
			$xfrecord = $this->xfRecords[$xfindex];
			$type = $xfrecord['type'];

			$format = $xfrecord['format'];
			$formatIndex = $xfrecord['formatIndex'];
			$fontIndex = $xfrecord['fontIndex'];
			$formatColor = "";
			$rectype = '';
			$string = '';
			$raw = '';

			if (isset($this->_columnsFormat[$column + 1])){
				$format = $this->_columnsFormat[$column + 1];
			}

			if ($type == 'date') {
				// See http://groups.google.com/group/php-excel-reader-discuss/browse_frm/thread/9c3f9790d12d8e10/f2045c2369ac79de
				$rectype = 'date';
				// Convert numeric value into a date
				$utcDays = floor($numValue - ($this->nineteenFour ? OW_VOTE_SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904 : OW_VOTE_SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS));
				$utcValue = ($utcDays) * OW_VOTE_SPREADSHEET_EXCEL_READER_MSINADAY;
				$dateinfo = wpvc_gmgetdate($utcValue);

				$raw = $numValue;
				$fractionalDay = $numValue - floor($numValue) + .0000001; // The .0000001 is to fix for php/excel fractional diffs

				$totalseconds = floor(OW_VOTE_SPREADSHEET_EXCEL_READER_MSINADAY * $fractionalDay);
				$secs = $totalseconds % 60;
				$totalseconds -= $secs;
				$hours = floor($totalseconds / (60 * 60));
				$mins = floor($totalseconds / 60) % 60;
				$string = date ($format, mktime($hours, $mins, $secs, $dateinfo["mon"], $dateinfo["mday"], $dateinfo["year"]));
			} else if ($type == 'number') {
				$rectype = 'number';
				$formatted = $this->_wpvc_format_value($format, $numValue, $formatIndex);
				$string = $formatted['string'];
				$formatColor = $formatted['formatColor'];
				$raw = $numValue;
			} else{
				if ($format=="") {
					$format = $this->_defaultFormat;
				}
				$rectype = 'unknown';
				$formatted = $this->_wpvc_format_value($format, $numValue, $formatIndex);
				$string = $formatted['string'];
				$formatColor = $formatted['formatColor'];
				$raw = $numValue;
			}

			return array(
				'string'=>$string,
				'raw'=>$raw,
				'rectype'=>$rectype,
				'format'=>$format,
				'formatIndex'=>$formatIndex,
				'fontIndex'=>$fontIndex,
				'formatColor'=>$formatColor,
				'xfIndex'=>$xfindex
			);

		}


	function wpvc_createNumber($spos) {
		$rknumhigh = $this->_wpvc_GetInt4d($this->data, $spos + 10);
		$rknumlow = $this->_wpvc_GetInt4d($this->data, $spos + 6);
		$sign = ($rknumhigh & 0x80000000) >> 31;
		$exp =  ($rknumhigh & 0x7ff00000) >> 20;
		$mantissa = (0x100000 | ($rknumhigh & 0x000fffff));
		$mantissalow1 = ($rknumlow & 0x80000000) >> 31;
		$mantissalow2 = ($rknumlow & 0x7fffffff);
		$value = $mantissa / pow( 2 , (20- ($exp - 1023)));
		if ($mantissalow1 != 0) $value += 1 / pow (2 , (21 - ($exp - 1023)));
		$value += $mantissalow2 / pow (2 , (52 - ($exp - 1023)));
		if ($sign) {$value = -1 * $value;}
		return  $value;
	}

	function wpvc_addcell($row, $col, $string, $info=null) {
		$this->sheets[$this->sn]['maxrow'] = max($this->sheets[$this->sn]['maxrow'], $row + $this->_rowoffset);
		$this->sheets[$this->sn]['maxcol'] = max($this->sheets[$this->sn]['maxcol'], $col + $this->_coloffset);
		$this->sheets[$this->sn]['cells'][$row + $this->_rowoffset][$col + $this->_coloffset] = $string;
		if ($this->store_extended_info && $info) {
			foreach ($info as $key=>$val) {
				$this->sheets[$this->sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset][$key] = $val;
			}
		}
	}


	function _wpvc_GetIEEE754($rknum) {
		if (($rknum & 0x02) != 0) {
				$value = $rknum >> 2;
		} else {
			//mmp
			// I got my info on IEEE754 encoding from
			// http://research.microsoft.com/~hollasch/cgindex/coding/ieeefloat.html
			// The RK format calls for using only the most significant 30 bits of the
			// 64 bit floating point value. The other 34 bits are assumed to be 0
			// So, we use the upper 30 bits of $rknum as follows...
			$sign = ($rknum & 0x80000000) >> 31;
			$exp = ($rknum & 0x7ff00000) >> 20;
			$mantissa = (0x100000 | ($rknum & 0x000ffffc));
			$value = $mantissa / pow( 2 , (20- ($exp - 1023)));
			if ($sign) {
				$value = -1 * $value;
			}
			//end of changes by mmp
		}
		if (($rknum & 0x01) != 0) {
			$value /= 100;
		}
		return $value;
	}

	function _wpvc_encodeUTF16($string) {
		$result = $string;
		if ($this->_defaultEncoding){
			switch ($this->_encoderFunction){
				case 'iconv' :	 $result = iconv('UTF-16LE', $this->_defaultEncoding, $string);
								break;
				case 'mb_convert_encoding' :	 $result = mb_convert_encoding($string, $this->_defaultEncoding, 'UTF-16LE' );
								break;
			}
		}
		return $result;
	}

	function _wpvc_GetInt4d($data, $pos) {
		$value = ord($data[$pos]) | (ord($data[$pos+1]) << 8) | (ord($data[$pos+2]) << 16) | (ord($data[$pos+3]) << 24);
		if ($value>=4294967294) {
			$value=-2;
		}
		return $value;
	}

}

?>