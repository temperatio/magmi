<?php

/**
 * MAGENTO MASS IMPORTER CLI SCRIPT
 * 
 * version : 0.1
 * author : S.BRACQUEMONT aka dweeves
 * updated : 2010-08-02
 * 
 */

require_once("magento_mass_importer.class.php");

$script=array_shift($argv);
$csvfile=array_shift($argv);
$options=array();
foreach($argv as $option)
{
	$isopt=$option[0]=="-";

	if($isopt)
	{
		$optarr=explode("=",substr($option,1),2);
		$optname=$optarr[0];
		if(count($optarr)>1)
		{
			$optval=$optarr[1];
		}
		else
		{
			$optval=1;
		}
		$options[$optname]=$optval;
	}
}
$options["filename"]=$csvfile;

function clilog($data,$type)
{
	print $data."\n";
}

$importer=new MagentoMassImporter();
$importer->loadProperties("magento_mass_importer.ini");
$importer->setLoggingCallback("clilog");
$importer->import($options);
?>