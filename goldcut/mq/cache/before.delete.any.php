<?php
/**
before - ДО, тк ПОСЛЕ данных уже нет и связи отследить невозможно
*/

function before_delete_any($del)
{
	if (Cache::is_enabled())
	{
		$key = $del->urn->entity->name.':'.$del->urn->uuid.':'.$eachlang; // TODO! - del all langs data
		Cache::clear($key);
	}
}

if (Cache::is_enabled())
{
	$broker = Broker::instance();
	$broker->queue_declare ("ENITY_USERFOLDER_CONSUMER", DURABLE, NO_ACK);
	$broker->bind("ENTITY", "ENITY_USERFOLDER_CONSUMER", "before.delete");
	$broker->bind_rpc ("ENITY_USERFOLDER_CONSUMER", "before_delete_any");
}
?>