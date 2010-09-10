<?php
class FileNotFoundException extends Exception
{
	
}
class InvalidPropertiesException extends Exception
{
	
}
class Properties
{
	protected $_props;
	public $inifile;
	
	public function __construct()
	{
		$this->inifile=null;
		$this->_props=array();
	}
	
	public function setPropsFromFlatArray($flatarr)
	{
		
		$this->_props=$this->getIniStruct($flatarr);
	}
	
	public function load($file)
	{
		if(!file_exists($file))
		{
			throw new FileNotFoundException();
			
		}
		try
		{
			$this->inifile=$file;
			$this->_props=parse_ini_file($this->inifile,true);
		}
		catch(Exception $e)
		{
			throw new InvalidPropertiesException();
		}
	}

	public function getIniStruct($arr)
	{
		$conf=array();
		foreach($arr as $k=>$v)
		{
			list($section,$value)=explode(":",$k,2);
			if(!isset($conf[$section]))
			{
				$conf[$section]=array();
			}
			$conf[$section][$value]=$v;
		}
		return $conf;
	}

	public function save($fname=null)
	{
		if($fname==null)
		{
			$fname==$this->inifile;
		}
		$this->write_ini_file($this->_props,$fname,true);
	}
	
	public function write_ini_file($assoc_arr, $path, $has_sections=FALSE) { 
    $content = ""; 
    if ($has_sections) { 
        foreach ($assoc_arr as $key=>$elem) { 
            $content .= "[".$key."]\n"; 
            foreach ($elem as $key2=>$elem2) { 
                if(is_array($elem2)) 
                { 
                    for($i=0;$i<count($elem2);$i++) 
                    { 
                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = \"".$elem2."\"\n"; 
            } 
        } 
    } 
    else { 
        foreach ($assoc_arr as $key=>$elem) { 
            if(is_array($elem)) 
            { 
                for($i=0;$i<count($elem);$i++) 
                { 
                    $content .= $key2."[] = \"".$elem[$i]."\"\n"; 
                } 
            } 
            else if($elem=="") $content .= $key2." = \n"; 
            else $content .= $key2." = \"".$elem."\"\n"; 
        } 
    } 

    if (!$handle = fopen($path, 'w')) { 
        return false; 
    } 
    if (!fwrite($handle, $content)) { 
        return false; 
    } 
    fclose($handle); 
    return true; 
}
	
	/**
	 * retrieve property value with default if not found
	 * @param string $secname section name
	 * @param string $pname property name
	 * @param string $default default value if not found (null if  not set)
	 * @return string value if found or default if not found
	 */
	public function get($secname,$pname,$default=null)
	{
		if(isset($this->_props[$secname]) && isset($this->_props[$secname][$pname]))
		{
			return $this->_props[$secname][$pname];
		}
		else
		{
			return $default;
		}
	}
	
	
}