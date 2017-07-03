<?php

function onScheduleDayly1($s)
{
	Log::info('System mq '.$s->time, 'cron-d');
}

function onScheduleHourly1($s)
{
	Log::info('System mq '.$s->time, 'cron-h');
}


$broker = Broker::instance();

$broker->queue_declare ("SCHEDULEDAILY", DURABLE, NO_ACK);
$broker->bind("SCHEDULE", "SCHEDULEDAILY", "schedule.daily");
$broker->bind_rpc ("SCHEDULEDAILY", "onScheduleDayly1");

$broker->queue_declare ("SCHEDULEDAILY", DURABLE, NO_ACK);
$broker->bind("SCHEDULE", "SCHEDULEDAILY", "schedule.hourly");
$broker->bind_rpc ("SCHEDULEDAILY", "onScheduleHourly1");

?>