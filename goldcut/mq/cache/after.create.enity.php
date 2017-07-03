<?php

function after_any_created($new)
{
	if (Cache::is_enabled())
	{
		$key = $new->urn->entity->name.':'.$new->urn->uuid.':'; // lang?
		Cache::clear($key);
		Cache::put($key, $new->urn->resolve(false, true));
	}

}

if (Cache::is_enabled())
{
	$broker = Broker::instance();
	$broker->queue_declare ("ENITYCONSUMERANY", DURABLE, NO_ACK);
	$broker->bind("ENTITY", "ENITYCONSUMERANY", "after.create");
	$broker->bind_rpc ("ENITYCONSUMERANY", "after_any_created");
}

?>