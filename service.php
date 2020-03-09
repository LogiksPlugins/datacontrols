<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("datacontrols","api");

handleActionMethodCalls();

function _service_list() {
    if(!isset($_GET['dcmode'])) return [];
    if(!isset($_GET['recache'])) $_GET['recache'] = "false";
    
    $fData = [];
    
    $fData = getDataControls($_GET['dcmode'], ($_GET['recache']==="true"?true:false));
    
    return $fData;
}

function getDataControls($dcMode, $reCache=false) {
    $basePath = getBasePath();
    $dcList = [];
    $dcModules = [];
    $cacheHash = getDCCacheHash($dcMode);
    
    if(!$reCache) {
        $fss = _cache($cacheHash);
    } else {
        $fss = false;
    }
    
    if(!$fss) {
        $fss = scanSubdirFiles($basePath);
        
        foreach($fss as $k=>$f) {
            $fss[$k] = str_replace("#".$basePath,"","#{$f}");
        }
        _cache($cacheHash, json_encode($fss));
    } else {
        if(is_string($fss)) {
            $fss = json_decode($fss, true);
        }
    }
    // printArray($fss);exit($dcMode);
    $_ENV['dcmode'] = $dcMode;
    $fss1 = array_filter($fss, "filterDataControls", ARRAY_FILTER_USE_BOTH);
    
    foreach($fss1 as $f) {
        $module = basename(dirname($f));
        $dt = filemtime($basePath.$f);
        if(in_array($module,$_ENV['SYSFOLDERS'])) $module = "System";
        $dcList[] = [
                "name"=>str_replace(".json","",basename($f)),
                "fpath"=>$f,
                "module"=>$module,
                "last_updated"=>date('d/m/Y H:i A', $dt),
                "type"=>strtolower($_ENV['dcmode']),
            ];
        $dcModules[$module] = toTitle($module);
    }
    
    return [
            "modules"=>$dcModules,
            "files"=>$dcList,
        ];
}

function filterDataControls($fPath) {
    if(!isset($_ENV['dcmode'])) $_ENV['dcmode'] = "";
    
    switch($_ENV['dcmode']) {
        case "reports":
            if(strpos($fPath,"reports/")) return true;
            break;
        case "forms":
            if(strpos($fPath,"forms/")) return true;
            break;
        case "infoviews":
            if(strpos($fPath,"forms/")) return true;
            break;
        case "infovisuals":case "visuals":
            if(strpos($fPath,"infovisuals/")) return true;
            break;
        case "views":
            if(strpos($fPath,"views/")) return true;
            break;
        case "templates":
            if(strpos($fPath,"templates/")) return true;
            break;
    }
    return false;
}

function scanSubdirFiles($dir) {
    $bname = basename($dir);
    
    if(!file_exists($dir) || !is_dir($dir)) return [];
    if(substr($bname,0,1)=="." || substr($bname,0,1)=="~")  return [];
    if(in_array($bname, $_ENV['NOSCAN'])) return [];
    
    $fss = scandir($dir);
    $fss = array_slice($fss,2);
    
    $list = [];
    foreach($fss as $f) {
        if(is_dir($dir.$f)) {
            $fss1 = scanSubdirFiles($dir.$f."/");
            
            foreach($fss1 as $f1) {
                $list[] = $f1;
            }
        } else {
            $list[] = $dir.$f;
        }
    }
    
    return $list;
}
?>