<?php
//Removes ecSTATic tables from Wordpress blog database, and options from WP Options table
global $wpdb;
$tables = array("options", "hits", "ips", "user_agents", "refs", "ruris", "cumulative", "estats", "aux_lists", "aux_kill", "aux_spider", "aux_se", "aux_nolog", "aux_wlist");
foreach ($tables as $table) {
	$t2d = $wpdb->prefix . "ecstatic_" . $table;
	$goodbye = $wpdb->query("DROP TABLE IF EXISTS $t2d");
	}
delete_option("ecstatic_loadlatest");
delete_option("ecstatic_ignored_ids");
?>