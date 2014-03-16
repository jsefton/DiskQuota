<?php
/*
 * @name		Disk Quota
 *
 * Used to generate statistics based on the folder directory and amount of files stored within.
 * 
 * Usage Example
 *
 * DiskQuota::get_quota(dirname(__FILE__), 1000);
 * Which is passing the current file directory of the file and setting the maximum size allowed to 1000MB / 1GB.
 *
 *
 * @author		Jamie Sefton
 * @version		1.0
 * @date		16/03/2014
 *
 */
class DiskQuota
{
	/*
	 *	Get Quota
	 *
	 * @param	string	$path 		path which will be scanned
	 * @param	int		$disk_limit	Amount in Megabytes that you want to limit the user too.
	 *
	 * @return	array	$data		Includes summary of usuage, free, total and percentage
	 */
	public function get_quota($path, $disk_limit = 500)
	{
		$total = (1024 * 1024 * $disk_limit);
		$used = self::get_used($path, $disk_limit);
		$free = self::get_free_space($path, $disk_limit);
		
		$data = array(
			'directory'		=>	$path,
			'free_space'	=>	($disk_limit == 0) ? 'UNLIMITED' : self::format_bytes($free),
			'used_space'	=>	self::format_bytes($used),
			'total'			=>	($disk_limit == 0) ? 'UNLIMITED' : self::format_bytes($total),
			'percentage'	=>	($disk_limit == 0) ? 0 : round($used / $total, 2) * 100
		);
		return (array) $data;
	}
	
	/*
	 * Get Used
	 *
	 * @param	string	$path 		path which will be scanned
	 *
	 * @return	int		$total_size	Total Size of scanned directory
	 */
	public static function get_used($path) 
	{
		/*** Reset the total size to be zero ***/
		$total_size = 0;
		
		/*** Scan the directory to get a list of files ***/
		$files = scandir($path);
		
		/*** Remove files that are parent directories ***/
		if($files[0] == ".")
			unset($files[0]);
		if($files[1] == "..")
			unset($files[1]);
	
		/*** Loop through each file from the set directory ***/
		foreach($files as $t) 
		{
			/*** Clean filename to get a directory name without slashes ***/
			$dir = rtrim($path, '/') . '/' . $t;
			
			/*** Check if it is a directory or not ***/
			if (is_dir($dir)) 
			{
				/*** Make sure its not a parent level directory listing ***/
				if ($t<>"." && $t<>"..") 
				{
					/*** Open the subfolder by calling this same function and passing the folder path ***/
					$size = self::get_used(rtrim($path, '/') . '/' . $t);
					
					/*** Add on the size of the sub-folder values to the total size ***/
					$total_size += $size;
				}
			} 
			else 
			{
				/*** If not a directory then get the size of the file ***/
				$size = filesize(rtrim($path, '/') . '/' . $t);
				
				/*** Add on the file size to the total size ***/
				$total_size += $size;
			}   
		}
		
		return $total_size;
	}
	
	/*
	 * Get Free Space
	 *
	 * @param	string	$path 		path which will be scanned
	 * @param	int		$disk_limit	Amount in Megabytes that you want to limit the user too.
	 *
	 * @return	int		$total_free	Total Size of free space which is used space minus total allowed
	 */
	public static function get_free_space($path, $disk_limit)
	{
		/*** Get total used space ***/
		$total = self::get_used($path);
		
		/*** Minus the used from the total allowed ***/
		$total_free = (1024 * 1024 * $disk_limit) - $total;
		
		return $total_free;
	}
	
	
	/*
	 * Format Bytes
	 *
	 * @param	int		$size 		Total size in bytes to be converted
	 * @param	int		$precision	Precision level of returned value. E.g. 2000MB / 2GB
	 *
	 * @return	string	$converted	Formated value of bytes with current suffix
	 */
	public function format_bytes($size, $precision = 2)
	{
		$base = log($size) / log(1024);
		$suffixes = array('', 'kb', 'MB', 'GB', 'TB');   
	
		$converted = round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
		
		return $converted;
	}
	
	
}
?>