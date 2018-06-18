<?php
class RequestUtils	{

	public static function IsValidNUM($request,$data,$min,$max)
	{
		if(isset($request[$data]) && is_numeric($request[$data]) && ($request[$data] > $min-1 && $request[$data] < $max+1))
			return true;
		
		return false;
	}
	public static function IsValidInt($request,$data)
	{
		if(isset($request[$data]) && strval(intval($request[$data])) == strval($request[$data]))
			return true;
		
		return false;
	}

	public static function isValidVal($request,$data,$values)
	{
		if(isset($request[$data]))
		{
			foreach($values as $value)
			{
				if(strtolower($request[$data]) == strtolower($value) )
					return true;
			}
		}
		
		return false;
	}

	public static function isValidLen($request,$data,$len)
	{
		if(isset($request[$data]))
		{
			if(strlen($request[$data]) == $len )
					return true;
		}
		
		return false;
	}
	
	public static function getArraySearchVal($barTypeArr,$matchkey,$type,$value = 'Id')
	{
		if(isset($barTypeArr) && isset($matchkey) && isset($type))
		{  
			foreach ($barTypeArr as $val)
			{
				if($val[$matchkey] == $type)
				{
					 $barcodetypeid = $val[$value];
				}
			}	
			return 	$barcodetypeid;
		}
		
		return false;
	}
}
?>