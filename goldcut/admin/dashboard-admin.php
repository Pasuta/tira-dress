<table border=0 width=100%>
<tr valign=top>

<td width=40%>

	<h3>Админ</h3>
	<ul id='dashboard-actions'>
		<?php 
			if (Cache::is_enabled()) echo '<li><a href="/goldcut/admin/?plugin=clearcache&key=sys:config">Сброс кеша конфигурации</a></li>';
		?>
		<li><a href="/goldcut/sync.php?action=status">Sync STATUS</a></li>
		<li><a href="/goldcut/sync.php?action=push">Sync PUSH to server</a></li>
		<li><a href="/goldcut/sync.php?action=pull">Sync PULL from server</a></li>
		<li><a href="/goldcut/test/sys/elist.php">Структура данных</a></li>
		<li><a href="/goldcut/admin/db.migrate.php">Миграция структуры данных</a></li>
		<li><a href="/goldcut/test/sys/production.load.php">Пересоздание базы данных и загрузка тестовых данных</a></li>
		<li><a href="/goldcut/admin/?plugin=rebuildlistdb">Пересоздание list базы данных из реляционной</a></li>
		<li><a href="/goldcut/admin/?plugin=timedebug">Сверка часов сервера и клиента</a></li>
		<li><a href="/goldcut/admin/?plugin=dbconsistency">Проверка целостности базы данных</a></li>
		<li><a href="/goldcut/admin/?plugin=cleanendfilenewlines">Очистка кода от end file newlines</a></li>
	</ul>

</td>
<td>

<?php 
	echo "<p>CACHE BACKEND: ".Cache::backend() . '</p>';
	
	if (NEWUSERMODEL === true) echo "<p>NEWUSERMODEL</p>";
	if (LEGACY_ENTITY_CONFIGS_ASPHPSRC === true) echo "<p>LEGACY CONFIG FORMAT</p>";
	if (EXTENDEDSTRUCTURE === true) echo "<p>EXTENDED STRUCTURE</p>";
	
	if (Cache::is_enabled())
	{
		$key = 'gctest';
		if ($res = Cache::get($key))
		{
			printlnd("$res - from cache");
		}
		else
		{
			$res = mt_rand(1,1000);
			$cachedOk = Cache::put($key, $res);
			printlnd("Cache this: $res. Is cached:($cachedOk)");
		}
	}
	
		$directory = BASE_DIR.DIRECTORY_SEPARATOR.'test';
		if (file_exists($directory))
		{
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS), RecursiveIteratorIterator::SELF_FIRST);
			$objects->setMaxDepth(5);
			println("<h2>Тесты</h2>");
			foreach ($objects as $fileinfo) 
			{
				if ($fileinfo->isFile()) 
				{
					$fname = $fileinfo->getFilename();
					$fpath = $fileinfo->getPath();
					$lpath = substr($fpath,strlen(BASE_DIR));
					if (substr($fname,-4,4) == '.php')
					{
						echo "<p><a href='{$lpath}/{$fname}'>$fname</a></p>";
					}
				}
			}
		}

		$directory = BASE_DIR.DIRECTORY_SEPARATOR.'/goldcut/test';
		if (file_exists($directory))
		{
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS), RecursiveIteratorIterator::SELF_FIRST);
			$objects->setMaxDepth(5);
			println("<h2>Системные тесты</h2>");
			foreach ($objects as $fileinfo) 
			{
				if ($fileinfo->isFile()) 
				{
					$fname = $fileinfo->getFilename();
					$fpath = $fileinfo->getPath();
					$lpath = substr($fpath,strlen(BASE_DIR)+1);
					if (substr($fname,-4,4) == '.php' || substr($fname,-5,5) == '.html')
					{
						echo "<p><a href='{$lpath}/{$fname}'>$fname</a></p>";
					}
				}
			}
		}

		
		
include "utilsdashboard.php";

?>

</td>
</tr>
</table>