<?php
/*
	WordPress with BuddyPress
	Installer for Pagoda Box v1.05
	Copyright 2012 by Martin Zeitler
	http://codefx.biz/contact
*/

/* the environment */
$fn1='latest.zip';
$src1='http://wordpress.org/'.$fn1;
$fn2='buddypress.1.6.1.zip';
$src2='http://downloads.wordpress.org/plugin/'.$fn2;
$base_dir = str_replace('/pagoda','', dirname(__FILE__));
$v_info=dirname(__FILE__).'/wordpress/wp-includes/version.php';
$dst1=$base_dir.'/pagoda/'.$fn1;
$dst2=$base_dir.'/pagoda/'.$fn2;

/* fetch the packages */
retrieve($src1, $dst1);
retrieve($src1, $dst1);

/* unzip the WordPress package */
$zip = new ZipArchive;
if($zip->open($dst1) === TRUE) {
	$zip->extractTo(dirname(__FILE__));
	$zip->close();
}

/* unzip the BuddyPress package */
$zip = new ZipArchive;
if($zip->open($dst2) === TRUE) {
	$zip->extractTo(dirname(__FILE__).'/wordpress/wp-content/plugins');
	$zip->close();
}

/* retrieve version number */
if(file_exists($v_info)){
	require_once($v_info);
	echo 'WordPress v'.$wp_version.' with BuddyPress v1.6.1 will now be deployed.';
}

function format_size($size=0) {
	if($size < 1024){
		return $size.'b';
	}
	elseif($size < 1048576){
		return round($size/1024,2).'kb';
	}
	else {
		return round($size/1048576,2).'mb';
	}
}

function retrieve($src, $dst){
	$fp = fopen($dst, 'w');
	$curl = curl_init();
	$opt = array(CURLOPT_URL => $src, CURLOPT_HEADER => false, CURLOPT_FILE => $fp);
	curl_setopt_array($curl, $opt);
	$rsp = curl_exec($curl);
	if($rsp===false){
		die("[cURL] errno:".curl_errno($curl)."\n[cURL] error:".curl_error($curl)."\n");
	}
	$info = curl_getinfo($curl);
	curl_close($curl);
	fclose($fp);
	
	/* cURL stats */
	$time = $info['total_time']-$info['namelookup_time']-$info['connect_time']-$info['pretransfer_time']-$info['starttransfer_time']-$info['redirect_time'];
	echo "Fetched '$src' @ ".abs(round(($info['size_download']*8/$time/1024/1024/1024),2))."GBps.\n";
}
?>