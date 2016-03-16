<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
try {
	require_once __DIR__ . '/lib/legionboard.php';
	$API = new LegionBoard($_REQUEST['request']);
	// Gzip all output
	ob_start('ob_gzhandler');
	echo $API->processAPI();
} catch (Exception $e) {
	echo json_encode(Array('error' => $e->getMessage()));
}
?>
