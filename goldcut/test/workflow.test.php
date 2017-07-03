<?php require_once dirname(__FILE__).'/../../goldcut/boot.php';

define('DEBUG_SQL',TRUE);

/**
TODO horizontal, vertical order. Interface in admin - stages by Transitions
pre planned work VS self initiated process
*/

class WorkflowTest implements TestCase
{
	private $urns = array();
	public $fixtures = true;
	
	/**
	create Wf, Tasks, task Transitions
	*/
	function createWorkflow()
	{
		$m = new Message();
		$m->action = 'create';
		$m->id = 10001;
		$m->urn = 'urn-wfa';
		$m->title = 'Editorial 1';
		$wfa = $m->deliver();
		
		$m = new Message();
		$m->action = 'create';
		$m->urn = 'urn-task';
		$m->id = 101;
		$m->title = 'ReTitle';
		$m->timelimit = 30;
		$m->automated = false;
		$m->wfa = $wfa->urn;
		$task1 = $m->deliver();
		
		$m = new Message();
		$m->action = 'create';
		$m->urn = 'urn-task';
		$m->id = 102;
		$m->title = 'Rewrite text';
		$m->timelimit = 300;
		$m->automated = false;
		$m->wfa = $wfa->urn;
		$task2 = $m->deliver();
		
		$m = new Message();
		$m->action = 'create';
		$m->urn = 'urn-task';
		$m->id = 103;
		$m->title = 'Illustrate text';
		$m->wfa = $wfa->urn;
		$task3 = $m->deliver();
		
		$m = new Message();
		$m->action = 'create';
		$m->urn = 'urn-transition';
		$m->taskfrom = $task1->urn;
		$m->taskto = $task2->urn;
		$m->wfa = $wfa->urn;
		$m->startpoint = true;
		$trans12 = $m->deliver();
		
		$m = new Message();
		$m->action = 'create';
		$m->urn = 'urn-transition';
		$m->taskfrom = $task2->urn;
		$m->taskto = $task3->urn;
		$m->wfa = $wfa->urn;
		$trans23 = $m->deliver();
	}
	
	/**
	start Process, get first Task, get Contract for it. Start work on Task by Contract.
	MQ. on contract signed put work data to #work.userid.taskname
	user gets work from #work.userid.taskname 
	put result to #done.taskname (workerUser, entityDataSetDone)
	*/
	function startWfProactiveProcess()
	{
		$wfaURN = new URN('urn-wfa-10001');
		$wfa = $wfaURN->resolve();
        println($wfa);

		$m = new Message();
		$m->action = 'create';
		$m->urn = 'urn-process';
		$m->wfa = 'urn-wfa-10001';
		$process = $m->deliver();
        println($process);
		
		$m = new Message();
		$m->action = 'load';
		$m->urn = 'urn-transition';
		$m->startpoint = true;
		$firstTransition = $m->deliver();
		$firstTaskURN = new URN($firstTransition->taskfrom);
		$firstTask = $firstTaskURN->resolve();
		printlnd($firstTask);
        println($firstTask);
		
		// Planned - get ready Conract with URN but with blank User
		// Proactive - create Contract with blank URN but on User
		
		$m = new Message();
		$m->action = 'create';
		$m->urn = 'urn-contract';
		$m->task = $firstTask->urn;
		$m->user = 'urn-user-903';
		$taskFirstContract = $m->deliver();
        println($taskFirstContract);
		
		// MQ on signed. Call task rewrite cnt signed. #planner.taskname (optionally concrete urn). 
		// assign urnlink to contract in mq
		// #planner.taskname call(put) job dataset to #work.userid.taskname
		
		// #done.taskname (workerUser, entityDataSetDone)
		
		// UPDATE PROCESS
		$m = new Message();
		$m->action = 'update';
		$m->urn = $process->urn;
		$m->wfa = 'urn-wfa-10001';
		$m->active = true;
		$m->task = $firstTask;
		$m->contract = $taskFirstContract;
		$updateProcess = $m->deliver();
        println($updateProcess);
		
		$transitions = $wfa->transition;
		foreach ($transitions as $trans)
            println($trans);

	}
	
	/**
	planned 1 retitle task (5 copy), 1 rewrite task (2 copy), 1 illustrate (always each is diff but copies BINDED(variationId)) of one news
	planned 1 retitle task (3 copy), 0 rewrite, 0 illustrate of another news
	*/
	private function startWfPrePlannedProcess()
	{
		
	}
		
}
?>