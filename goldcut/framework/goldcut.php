<?php 
/**
Boot
grab all places from apps to inc
inc 1 joined src file for framework
+ apps, managers, mq separete?

PHP OPT - http://phplens.com/lens/php-book/optimizing-debugging-php.php
http://raditha.com/wiki/Readfile_vs_include

*/
class Goldcut 
{
	function __construct($dir)
	{	
		// base speed on c2d 1.3 = 250 r/s (without opcode cache)
		$this->init($dir); // >fast
		$this->autoloadRegister(); // > 200
		//require(BASE_DIR.'/goldcut/framework/utils.php'); // > 230 IS FASTER THEN autoloadRegister (it has >200)
		Utils::startTimer('init'); // >150(autoload)|>200(mrequire) // first call with resolve class by autoload register
		//$this->webbase(); // fast
		define('BASE_URI', '/');
		define('WEB_BASE_DIR', $_SERVER['DOCUMENT_ROOT']);
		// primary util functions - scan files for conf files
		$this->loadLoaders(); // >97 just require 3 files gets 30% of speed after this line
		$this->loadConfigs(); // very slow (>27 r/s) - slowest is php unserilize on 100k file (reason in classes with interlinks. arrays is fast) (7 earlier was in preload, mailer)
        if (ENV === 'DEVELOPMENT' or TEST_ENV === true) $this->check_base_consistency();
		$this->loadNamedQueries();
		$this->loadMessageQueuePublishersListeners(); // >20
		if (ENV === 'DEVELOPMENT' || TEST_ENV === true) $this->check_consistency();
		if (ADMIN_AREA === true) $this->loadFormWidgets();
        @include BASE_DIR.'/helpers/global_functions.php';
		$ctime = Utils::reportTimer('init');
		Log::info("@ INIT CTIME: [{$ctime['time']}]",'main');
	}
	
	private function init($dir)
	{
		$basedir = realpath($dir.'/..');
		define('BASE_DIR', $basedir);
        define('TMP_DIR', BASE_DIR.'/tmp');
		define('CLASS_DIR', $dir.'/framework');
		define('CLASS_ENT_DIR', $dir.'/ent');
		define('FORMWIDGETS_LOCAL_DIR', BASE_DIR.'/formwidgets');
		define('FORMWIDGETS_DIR', BASE_DIR.'/goldcut/formwidgets');
		define('MANAGERS_DIR', BASE_DIR.'/goldcut/managers');
		define('MANAGERS_LOCAL_DIR', BASE_DIR.'/managers');
		define('APPS_DIR', BASE_DIR.'/apps/');
		define('SYSTEM_APPS_DIR', BASE_DIR.'/goldcut/apps/');
		define('PLUGINS_DIR', BASE_DIR.'/goldcut/plugins/');
		define('PLUGINS_LOCAL_DIR', BASE_DIR.'/data-plugins/');
		define('FIXTURES_DIR', BASE_DIR.'/importexport');
	}
	
	function check_base_consistency()
	{
		/**
		TODO
		get js/settings.js from goldcut/defaults/js/settings.js, watermark
		777 anf option default mask in config
		*/
		$syspath = array();
		array_push($syspath, BASE_DIR.'/log');
		array_push($syspath, BASE_DIR.'/mq_rpc/listeners');
		//array_push($syspath, BASE_DIR.'/mq_rpc/publishers');
		//array_push($syspath, BASE_DIR.'/views/layout');
		//array_push($syspath, BASE_DIR.'/widgets');
		//array_push($syspath, BASE_DIR.'/data-plugins');
		array_push($syspath, BASE_DIR.'/original');
		//if (MEDIASERVERS > 1) array_push($syspath, BASE_DIR.'/thumb');
		$this->dirsSetup($syspath);
	}
	
	function check_consistency()
	{
		$syspath = array();
		$reportOnConsistency = array();
		foreach (Entity::each_entity() as $e )
		{
			$report = $e->checkConsistency();
			if (count($report)) $reportOnConsistency[$e->name] = $report;
			if ($e->class != 'Photo' or $e->name == 'photo') continue;
			array_push($syspath, BASE_DIR.'/media/'.$e->name);
		}
		foreach($reportOnConsistency as $ename => $reports)
		{
			foreach($reports as $report)
			{
				Log::error($report, 'inconsistency');
				println($report,1,TERM_RED);
			}
		}
		if (count($reportOnConsistency)) throw new Exception('Inconsistencies in entities config');
		$this->dirsSetup($syspath);
	}
	
	private function dirsSetup($dirs)
	{
		foreach($dirs as $path)
		{
			if (!file_exists($path)) 
			{
				mkdir($path, octdec(FS_MODE_DIR), true);
				println("create folder $path",1,TERM_VIOLET);
			}
			if (!is_writable) 
			{
				if (!chmod($path, octdec(FS_MODE_DIR))) throw new Exception("Can't chmod $path");
				else dprintln("make ug writable $path",1,TERM_YELLOW);
			}
		}
	}
	/*
	function webbase()
	{
		define('BASE_URI', '/');
		define('WEB_BASE_DIR', $_SERVER['DOCUMENT_ROOT']);
	}
	*/
	function autoloadRegister()
	{
		set_include_path(get_include_path() . PATH_SEPARATOR . CLASS_ENT_DIR . PATH_SEPARATOR. MANAGERS_LOCAL_DIR . PATH_SEPARATOR. MANAGERS_DIR . PATH_SEPARATOR .  PLUGINS_LOCAL_DIR . PATH_SEPARATOR . PLUGINS_DIR . PATH_SEPARATOR . FORMWIDGETS_LOCAL_DIR . PATH_SEPARATOR . FORMWIDGETS_DIR);  //    PATH_SEPARATOR . CLASS_DIR  - already defined in boot.php
		spl_autoload_register();
		require "assert.php";
	}
	
	function loadFormWidgets()
	{
		$this->load_divided('goldcut/formwidgets');
	}
	
	function loadNamedQueries()
	{
		include BASE_DIR.'/config/namedquery/namedquery.php';
	}
	
	function loadLoaders()
	{
		require BASE_DIR.'/config/core.php';
		require BASE_DIR.'/config/local.php';
		require CLASS_DIR.'/coreutils.php';
	}
	
	function loadConfigs()
	{
        if (!defined('FS_MODE_DIR')) define('FS_MODE_DIR', '0750');
        if (!defined('FS_MODE_FILE')) define('FS_MODE_FILE', '0640');
		/**
		TODO recursive load from subdirs XML configs.
		Cache all to mem or php one conf file
		*/
		if ($sysConfig = Cache::get('sys:config'))
		{
			$GLOBALS['CONFIG'] = ($sysConfig); // unserialize
		}
		else
		{
			$this->load_divided('goldcut/systemfield');
			$this->load_divided('config/field');
			//$this->load_divided('goldcut/systementity'); // kegacy goldcut php configs
            $this->load_divided_xml('goldcut/systementity');
			if (defined('LEGACY_ENTITY_CONFIGS_ASPHPSRC') && LEGACY_ENTITY_CONFIGS_ASPHPSRC === true)
			{
				$this->load_divided('config/entity');
				$this->check_configs();
			}
			else
			{
				$this->load_divided_xml('config/entity');
			}
            $this->load_overlay_xml('config/entityoverlay');
			$this->load_divided('goldcut/config/route');
			$this->load_divided('config/route');
			$this->load_divided('config/db');
			$this->load_divided('config/mail');
			Cache::put('sys:config', $GLOBALS['CONFIG']); // serialize
		}
		require BASE_DIR.'/config/preload/preload.php';
	}
	
	private function check_configs()
	{
		$em = Entity::each_managed_entity('Photo');
		foreach($em['Photo'] as $manager)
		{
			if ($manager->name == 'photo') continue;
			if(is_string($manager->mediaoptions)) throw new Exception("Photo entity:{$manager->name} mediaoptions {$manager->mediaoptions} in legacy string format. Migrate to array('image'=>'XxY', 'preview'=>'XxY:crop')");
		}
	}
	
	function loadMessageQueuePublishersListeners()
	{
		//$this->load_divided('mq_rpc/publishers');
		$broker = Broker::instance();
		$broker->exchange_declare ("MANAGERS", DURABLE, ROUTED);
		$broker->exchange_declare ("ENTITY", DURABLE, ROUTED);
		$broker->exchange_declare ("SCHEDULE", DURABLE, ROUTED);

		$this->load_divided('goldcut/mq');
		$this->load_divided('goldcut/ent/mq');
		$this->load_divided('mq_rpc/listeners');
	}
	
	
	private function load_divided($dir)
	{
		$directory = BASE_DIR.DIRECTORY_SEPARATOR.$dir;
		//$iterator = new DirectoryIterator($directory); // plain
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS), RecursiveIteratorIterator::SELF_FIRST);
		$objects->setMaxDepth(5);
		foreach ($objects as $fileinfo) 
		{
			if ($fileinfo->isFile()) 
			{
				$fname = $fileinfo->getFilename();
				$fpath = $fileinfo->getPath();
				if (substr($fname,-4,4) == '.php')
				{
					require($fpath.'/'.$fname);
				}
			}
		}
	}
	
	private function load_divided_xml($dir)
	{
		$directory = BASE_DIR.DIRECTORY_SEPARATOR.$dir;
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS), RecursiveIteratorIterator::SELF_FIRST);
		$objects->setMaxDepth(5);
		foreach ($objects as $fileinfo) 
		{
			if ($fileinfo->isFile()) 
			{
				$fname = $fileinfo->getFilename();
				$fpath = $fileinfo->getPath();
				if (substr($fname,-4,4) == '.xml')
				{
					$filepath = $fpath.'/'.$fname;
					XMLConfigLoader::load($filepath, 'entity');
				}
			}
		}
	}

    private function load_overlay_xml($dir)
    {
        $directory = BASE_DIR.DIRECTORY_SEPARATOR.$dir;
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS), RecursiveIteratorIterator::SELF_FIRST);
        $objects->setMaxDepth(5);
        foreach ($objects as $fileinfo)
        {
            if ($fileinfo->isFile())
            {
                $fname = $fileinfo->getFilename();
                $fpath = $fileinfo->getPath();
                if (substr($fname,-4,4) == '.xml')
                {
                    $filepath = $fpath.'/'.$fname;
                    XMLConfigLoader::loadoverlay($filepath, 'entity');
                }
            }
        }
    }

}
?>