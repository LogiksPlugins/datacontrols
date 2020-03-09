<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getDCCacheHash")) {
    $_ENV['NOSCAN']=[
            "usermedia",
            "userdata",
    		"tmp",
    		"temp",
    		"log",
    		"logs",
    		".git",
    		".install",
    		"node_modules",
    		"..",
    		"vendors",
    		"sql",
    		"css",
    		"js",
    		"media",
    		"config",
    		"pages",
    		"services",
    		
    		"widgets",
    		"dashlets",
    	];
    	
    $_ENV['SYSFOLDERS'] = ["forms","reports","infoviews","infovisuals","views","templates"];
    
    $_ENV['DBLOGIC']=[
            "eq" => "EQUALS",
            "ne" => "NOT EQUALS",
            "lt" => "LESS THEN",
            "le" => "LESS THEN EQUALS",
            "gt" => "GREATER THEN",
            "ge" => "GREATER THEN EQUALS",
            "nn" => "NOT NULL",
            "nu" => "IS NULL",
            "bw" => "STARTS WITH",
            "bn" => "NOT STARTS WITH",
            "lw" => "ENDS WITH",
            "ln" => "NOT ENDS WITH",
            "cw" => "CONTAINS",
            "cn" => "DOES NOT CONTAIN",
            "find" => "FIND IN SET",
            "in" => "FIND IN",
            "ni" => "NOT FIND IN",
            "range" => "BETWEEN",
            "rangestr" => "RANGE STRING",
        ];

    function getDCCacheHash($dcMode) {
        return md5(SiteLocation.SITENAME.CMS_SITENAME);
    }
  
    function getBasePath() {
        return CMS_APPROOT;
    }
}
?>