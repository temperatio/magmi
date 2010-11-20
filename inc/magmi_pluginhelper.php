<?php
$base_dir=dirname(__FILE__);
$plugin_dir=realpath(dirname(__FILE__)."/../plugins");
set_include_path(ini_get("include_path").PATH_SEPARATOR."$plugin_dir/inc".PATH_SEPARATOR."$base_dir");

require_once("magmi_item_processor.php");
require_once("magmi_datasource.php");


class Magmi_PluginHelper
{
	
	static $_plugins_cache=null;
	
	public static function fnsort($f1,$f2)
	{
		return strcmp(basename($f1),basename($f2));	
	}
	
	public static function initPluginInfos($basedir,$baseclass)
	{
		$pgdir=dirname(__FILE__);
		$basedir="$pgdir/../$basedir";
		$candidates=glob("$basedir/*/*.php");
		usort($candidates,array("Magmi_PluginHelper","fnsort"));
		$pluginclasses=array();
		foreach($candidates as $pcfile)
		{
			$content=file_get_contents($pcfile);
			if(preg_match_all("/class\s+(.*?)\s+extends\s+$baseclass/mi",$content,$matches,PREG_SET_ORDER))
			{
				require_once($pcfile);				
				foreach($matches as $match)
				{
					$pluginclasses[]=array("class"=>$match[1],"dir"=>dirname(substr($pcfile,strlen($pgdir))));
				}
			}
		}
		return $pluginclasses;
	}

	public static function getPluginClasses()
	{
		return self::getPluginsInfo("class");
	}
	
	public static function getPluginsInfo($filter=null)
	{
		if(self::$_plugins_cache==null)
		{
			self::scanPlugins();
		}
		
		if(isset($filter))
		{
			$out=array();
			foreach(self::$_plugins_cache as $k=>$arr)
			{
				if(!isset($out[$k]))
				{
					$out[$k]=array();
				}
				foreach($arr as $desc)
				{
					$out[$k][]=$desc[$filter];
				}
			}	
			$plugins=$out;
		}
		else
		{
			$plugins=self::$_plugins_cache;
		}
		return $plugins;
		
	}
	public static function scanPlugins()
	{
		if(!isset(self::$_plugins_cache))
		{
			self::$_plugins_cache=array("itemprocessors"=>self::initPluginInfos("plugins/itemprocessors","Magmi_ItemProcessor"),
				"datasources"=>self::initPluginInfos("plugins/datasources","Magmi_Datasource"),
				"general"=>self::initPluginInfos("plugins/general","Magmi_GeneralImportPlugin"));
		}
	}
	
	
	public static function createInstance($pclass)
	{
	
		if(self::$_plugins_cache==null)
		{
			self::scanPlugins();
		}
		$plinst=new $pclass();
		$plinst->pluginInit(null,null,false);
		return $plinst;
	}
	
	public static function getPluginDir($pinst)
	{
		if(self::$_plugins_cache==null)
		{
			self::scanPlugins();
		}
		
		foreach(self::$_plugins_cache as $t=>$l)
		{
			foreach($l as $pdesc)
			{
				if($pdesc["class"]==get_class($pinst))
				{
					return $pdesc["dir"];
				}
			}
		}
	}
}