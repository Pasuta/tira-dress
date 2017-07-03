<?php

/**
USAGE

if ($res = Cache::get($key))
{
	$res = ($res); // unserialize auto
}
else
{
	$res = rand(10);
	$cachedOk = Cache::put($key, ($res)); // serialize auto
}

TODO !!! NEED ->SYSTEM UPDATE FLAG TO NOT CHANGE F:UPDATED AND DONT TOUCH CACHE ON COUNT_ INC
TODO cache by _parent, uri (or another KEY)
TODO cache check in (SQL IN requests) if (1,2,cached,4,cached,cached,7)
TODO cached views for paged listings cache 

TODO !! use json to serialize
ob + readfile is fastest for fileread
secure /tmp cache folder in webserver (db passwords are in cache!)
*/

class Cache 
{

	static $enabled = ENABLE_CACHE;
	
	public static function enable()
	{
		self::$enabled = true;
	}
	
	public static function disable()
	{
		self::$enabled = false;
	}
	
	public static function is_enabled()
	{
		if (self::$enabled === true)
			return true;
		else
			return false;
	}
	
	public static function is_disabled()
	{
		if (self::$enabled !== true)
			return true;
		else
			return false;
	}
	
	private static function namespace_key($key)
	{
		return HOST.'/'.ENV.'/'.$key;
	}

	static function exists($key)
	{
		/**
		TODO
		*/
	}
	
	public static function put($key, $value) 
	{
		if (ENABLE_CACHE !== true) return null;
		Log::info(">> $key (".self::backend().")", 'cache');
		$value = serialize($value);
		$key = self::namespace_key($key);
		
		if (extension_loaded('xcache'))
		{
			$ok = xcache_set($key, $value);
			return $ok;
		}	
		else if (extension_loaded('apc'))
		{
			return apc_store($key, $value);
		}
		else if (extension_loaded('memcache'))
		{
			if ($memcache_obj = memcache_connect('localhost', 11211))
			{
				memcache_set($memcache_obj, $key, $value, 0, 0); // MEMCACHE_COMPRESSED, TTL seconds
				memcache_close($memcache_obj);
			}
			return true;
		}
		else // filecache
		{
			// if no mem caches
			$filename = BASE_DIR.'/tmp/'.md5($key).'.cache';
			$file = fopen($filename, 'w');
			if (flock($file, LOCK_EX)) 
			{
				fwrite($file, $value);
				flock($file, LOCK_UN); // release the lock
			} 
			else
			{
			   throw new Exception("Couldn't get the WRITE EX lock for $filename file!");
			}
			fclose($file);
			return true;
		}
	}
	
	public static function get($key)
	{
		if (ENABLE_CACHE !== true) return null;
		//Log::info(" << $key (".self::backend().")", 'cache');
		
		$key = self::namespace_key($key);	
		
		if (extension_loaded('xcache'))
		{
			if (xcache_isset($key))
			{
				$data = xcache_get($key);
				$data = unserialize($data);
				if (DEBUG_CACHE === true) printlnd($data); 
				return $data;
			}
			else 
			  return null;
		}
		else if (extension_loaded('apc')) 
		{
			return unserialize(apc_fetch($key));
		}
		else if (extension_loaded('memcache'))
		{
			if ($memcache_obj = memcache_connect('localhost', 11211))
			{
				$res = unserialize(memcache_get($memcache_obj, $key));
				memcache_close($memcache_obj);
				return $res;
			}
			else return null;
		}
		else
		{
			$filename = BASE_DIR.'/tmp/'.md5($key).'.cache';
			if (file_exists($filename))
			{
				$file = fopen($filename, 'r');
				if (flock($file, LOCK_SH))
				{
				  $fs = filesize($filename);
				  //if ($fs < 4096) $fs = 256000;
				  $data = fread($file, $fs);
				  //$data = file_get_contents($file); // slow
				  flock($file, LOCK_UN);
				} 
				else
				{
					throw new Exception("Couldn't get the READ SHARED lock for $filename file!");
				}
				fclose($file);
				Log::info(" << $key (".self::backend().")", 'cache');
				return unserialize($data);
			}
			else
			{
				Log::info(" < $key (".self::backend().")", 'cache');
				return null;
			}
		}
		
	}
	
	
	public static function clear($okey)
	{
		if (ENABLE_CACHE !== true) return null;
		
		Log::info("0 $okey", 'cache');
		
		$key = self::namespace_key($okey);
		
		if (extension_loaded('xcache'))
		{
			return xcache_unset($key);
		}
		
		if (extension_loaded('apc'))
		{
			return apc_delete($key);
		}
		
		if (extension_loaded('memcache'))
		{
			if ($memcache_obj = memcache_connect('localhost', 11211))
			{
				memcache_delete($memcache_obj, $key);
				memcache_close($memcache_obj);
				return true;
			}
			else return null;
		}
		
		// if no mem caches
		$filename = BASE_DIR.'/tmp/'.md5($key).'.cache';
		if (file_exists($filename))
		{
			Log::debug("0 F $okey", 'cache');
			unlink($filename);
		}
		else
		{
			Log::debug("0 F ?404 $okey", 'cache');
			return false;
		}
	
	}
	
	public static function backend()
	{
		if (ENABLE_CACHE !== true) return 'CACHE DISABLED';
		
		if (extension_loaded('xcache'))
		{
			return 'XCACHE';
		}
		if (extension_loaded('apc')) 
		{
			return 'APC';
		}
		if (extension_loaded('memcache'))
		{
			return 'MEMCACHE';
		}
		return 'FILECACHE';
	}

}

?>