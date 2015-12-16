<?php
/*********************************************************************************
 * CSV-Import is a Tool for importing CSV for the Wordpress
 * plugin developed by Smackcoder. Copyright (C) 2014 Smackcoders.
 *
 * CSV-Import is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY 
 * CSV-Import, CSV-Import DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * CSV-Import is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * CSV-Import copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2014. All rights reserved".
 ********************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
$noncevar = isset($_POST['postdata']['wpnonce']) ? $_POST['postdata']['wpnonce'] : '';
if(!wp_verify_nonce($noncevar, 'smack_nonce'))
die('You are not allowed to do this operation.Please contact your admin.');

$impCheckobj = CallWPcsvImportObj::checkSecurity();
if($impCheckobj != 'true')
die($impCheckobj);
require_once(WP_CONST_CSV_IMP_DIRECTORY . 'lib/skinnymvc/core/base/SkinnyBaseActions.php');
require_once(WP_CONST_CSV_IMP_DIRECTORY . 'lib/skinnymvc/core/SkinnyActions.php');
$skinnyObj = new CallWPcsvImportObj();
$curr_action = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'];
$importedAs = Null;
$inserted_post_count = 0;
$noofrecords = '';
if ($curr_action != 'post' && $curr_action != 'page' && $curr_action != 'custompost') {
	require_once(CSVIMP_PLUGIN_BASE . '/modules/' . $curr_action . '/actions/actions.php');
}
if ($curr_action == 'post' || $curr_action == 'page' || $curr_action == 'custompost') {
	$importObj = new WPcsvImport_includes_helper();
	if ($curr_action == 'post') {
		$importedAs = 'Post';
	}
	if ($curr_action == 'page') {
		$importedAs = 'Page';
	}
	if ($curr_action == 'custompost') {
		$importedAs = 'Custom Post';
	}
	$importObj->MultiImages = $_POST['postdata']['importinlineimage'];
} 

$limit = $_POST['postdata']['limit'];
$totRecords = $_POST['postdata']['totRecords'];
$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['importlimit'] = $_POST['postdata']['importlimit'];
$count = $_POST['postdata']['importlimit'];
$requested_limit = $_POST['postdata']['importlimit'];
$tmpCnt = $_POST['postdata']['tmpcount'];
if ($count < $totRecords) {
	$count = $tmpCnt + $count;
	if ($count > $totRecords) {
		$count = $totRecords;
	}
} else {
	$count = $totRecords;
}
$resultArr = array();
$res2 = array();
$res1 = array();
$get_mapped_array = array();
$mapping_value = '';
$filename = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['uploadedFile'];
$delim = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['select_delimeter'];
$resultArr = $skinnyObj->csv_file_data($filename);
if ($_POST['postdata']['dupTitle']) {
	$importObj->titleDupCheck = $_POST['postdata']['dupTitle'];
}
if ($_POST['postdata']['dupContent']) {
	$importObj->conDupCheck = $_POST['postdata']['dupContent'];
}
$csv_rec_count = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['h2'];
$available_groups = $skinnyObj->get_availgroups($curr_action);

//mapped and unmapped count
foreach ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES'] as $seskey => $sesval ) {
        foreach($available_groups as $groupKey => $groupVal) {
                $current_mapped = explode($groupVal.'mapping', $seskey);
                        if(is_array($current_mapped) && count($current_mapped) == 2) {
                                $get_mapped_array['mapping'.$current_mapped[1]] = $sesval;
                                if($sesval == '-- Select --' ) {
                                        $res1[$seskey] = $sesval;
                                }
                                else {
                                        if ($sesval != '')
                                        $res2[] = $sesval;
                                }
                        }
        }
}
        $mapped = count($res2);
        $unmapped = count($res1);

for ($i = $limit; $i < $count; $i++) {
        if ($limit == 0) {
		echo "<div style='margin-left:10px;'>"; echo __(' Total no of records -','csv-import'); echo $totRecords; echo ".</div><br>";
                echo "<div style='margin-left:10px;'>"; echo __(' Total no of mapped fields for single record -','csv-import'); echo  $mapped;  echo".</div><br>";
                echo "<div style='margin-left:10px;'>"; echo __(' Total no of unmapped fields for a record -','csv-import'); echo $unmapped; echo ".</div><br>";
		echo "<div style='margin-left:10px;'>"; echo __(' Chosen server request is ','csv-import'); echo $count; echo " .</div><br>";
        }
	$colCount = count($resultArr[$i]);
	$_SESSION['SMACK_SKIPPED_RECORDS'] = $i;
        $to_be_import_rec = $resultArr[$i];
	$importObj->detailedLog = array();
	$extracted_image_location = null;
	$importinlineimageoption = null;
	if(isset($_POST['postdata']['inline_image_location'])) {
		$importinlineimageoption = 'imagewithextension';
	        $extracted_image_location = $_POST['postdata']['inline_image_location'];
	}
	if($_POST['postdata']['inlineimagehandling'] != 'imagewithextension') {
		$importinlineimageoption = 'imagewithurl';
		$extracted_image_location = $_POST['postdata']['inline_image_location'];
		$sample_inlineimage_url = $_POST['postdata']['inlineimagehandling'];
	}
	$importObj->processDataInWP($to_be_import_rec,$_SESSION['SMACK_MAPPING_SETTINGS_VALUES'], $_SESSION['SMACK_MAPPING_SETTINGS_VALUES'], $i, $extracted_image_location, $importinlineimageoption, $sample_inlineimage_url);
	$logarr = array('post_id','assigned_author','category','tags','postdate','image','poststatus');
	if ($curr_action == 'post' || $curr_action == 'page' || $curr_action == 'custompost') {
		foreach($importObj->detailedLog as $logKey => $logVal) {
		     if(array_key_exists($logarr[0],$logVal)) {
			foreach($logarr as $logarrkey){
				if(!array_key_exists($logarrkey,$logVal)){
					$logVal[$logarrkey] = '';
				}
			}
			if($curr_action != 'page') {
				echo "<p style='margin-left:10px;'> " . $logVal['post_id'] . " , " . $logVal['assigned_author'] . " , " . $logVal['category'] . " , " . $logVal['tags'] . " , " . $logVal['postdate'] . " , " . $logVal['image'] . " , " . $logVal['poststatus'] ." ,". $logVal['verify_here'] . "</p>";
			} else {
				echo "<p style='margin-left:10px;'> " . $logVal['post_id'] . " , " . $logVal['assigned_author'] . " , " . $logVal['postdate'] . " , " . $logVal['image'] . " , " . $logVal['poststatus'] ." ,". $logVal['verify_here'] . "</p>";
			}  } 
		        else {
                                   echo "<p style='margin-left:10px;'>" . $logVal['verify_here'] . "</p>";
                        }
		}
	}

	$limit++;
        unset($to_be_import_rec);
}

if ($limit >= $totRecords) {
	$advancemedia = $_POST['postdata']['advance_media'];
	$dir = $skinnyObj->getUploadDirectory();
	$get_inline_imageDir = explode('/', $extracted_image_location);
	$explodedCount = count($get_inline_imageDir);
	$inline_image_dirname = $get_inline_imageDir[$explodedCount - 1];
	$uploadDir = $skinnyObj->getUploadDirectory('inlineimages');
	$inline_images_dir = $uploadDir . '/smack_inline_images/' . $inline_image_dirname;
	if($advancemedia == 'true'){
		$skinnyObj->deletefileafterprocesscomplete($inline_images_dir);
	}
	$skinnyObj->deletefileafterprocesscomplete($dir);
}
if ($importObj->insPostCount != 0 || $importObj->dupPostCount != 0 || $importObj->updatedPostCount != 0) {
	if (!isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'])) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] = 0;
	}
	if (!isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'])) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] = 0;
	}
	if (!isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'])) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'] = 0;
	}
	if (!isset($importObj->capturedId)) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'] = 0;
	}
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] + $importObj->insPostCount;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] + $importObj->dupPostCount;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'] + $importObj->updatedPostCount;
	if (isset($importObj->capturedId)) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'] = $importObj->capturedId;
	}
}
$inserted_post_count = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'];
if ($inserted_post_count != 0) {
	if (!isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'])) {
		$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'] = 0;
	}
		$importObj->addStatusLog($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'], $importedAs);
		$importObj->addPieChartEntry($importedAs, $inserted_post_count);
		$inserted_post_count = 0;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount'] = 0;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount'] = 0;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount'] = 0;
	$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId'] = 0;
	unset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['insPostCount']);
	unset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['dupPostCount']);
	unset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['updatedPostCount']);
	unset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['captureId']);
}
if ($limit == $totRecords) {
	echo "<br><div style='margin-left:10px; color:green;'>";
	echo __('Import process has been completed!.','csv-import');
	echo "</div>";
}
foreach ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES'] as $key => $value) {
	for ($j = 0; $j < $csv_rec_count; $j++) {
		if ($key == 'mapping' . $j) {
			$mapArr[$j] = $value;
		}
	}
}
