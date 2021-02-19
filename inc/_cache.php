<?php
$cache_active = true; 
$cache_folder = $cache_path; 
$GLOBALS['cache_folder']=$cache_folder;
class acmeCache{ 
	// public functionality, acmeCache::fetch() and acmeCache::save()
	// =========================
	
	public static function fetch($name, $refreshSeconds = 0){
		$cacheContent="";
		if(!$GLOBALS['cache_active']) return false;
		if(!$refreshSeconds) $refreshSeconds = 60;
		$cacheFile = acmeCache::cachePath($name);

		clearstatcache();
		if(file_exists($cacheFile) and ((time()-filemtime($cacheFile))< $refreshSeconds)) $cacheContent = file_get_contents($cacheFile);
		return $cacheContent;
	}

	public static function save($name, $cacheContent){
		if(!$GLOBALS['cache_active']) return;
		if(empty($cacheContent)) return false;
		$cacheFile = acmeCache::cachePath($name);
		acmeCache::savetofile($cacheFile, $cacheContent);
	} 

	// for internal use 
	// ====================
	public static  function cachePath($name){
		$cacheFolder = $GLOBALS['cache_folder']; 
		if(!$cacheFolder) $cacheFolder = trim($_SERVER['DOCUMENT_ROOT'],'/').'/cache/';
		return $cacheFolder . md5(strtolower(trim($name))) . '.cache';
	}

	public static  function savetofile($filename, $data){
		$dir = trim(dirname($filename),'/').'/';
		acmeCache::forceDirectory($dir);
		$file = fopen($filename, 'w');
		fwrite($file, $data); fclose($file);
	} 

	public static  function delfile($filename){
		@unlink($filename);
	}

	public static  function forceDirectory($dir){ // force directory structure 
		return is_dir($dir) or (acmeCache::forceDirectory(dirname($dir)) and mkdir($dir, 0777));
	}
}

function clearCache($name){
	$cacheFile = acmeCache::cachePath($name);
	acmeCache::delfile($cacheFile); 
}

function getCache($name,$time=0){
	global $iCache_ExpireHour;
	$cachetime = (!$time?60*60*$iCache_ExpireHour:$time); 
	return acmeCache::fetch($name, $cachetime);
}

function saveCache($name,$val){
	acmeCache::save($name, $val);
}
?>