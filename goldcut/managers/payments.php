<?php

class Payments extends EManager
{

	protected function config()
	{
		$this->behaviors[] = 'general_crud';
	}
	
	function pay($i)
	{

		// check ballance
		$invoice = $i->urn->resolve()->current();
		$user = $invoice->user;
		//assertURN($user->urn);
		if (Entity::exists('account'))
		{
			$account = $user->account;
			$wallet = $account->wallet;
		}
		else
		{
			$account = $user;
			$wallet = $user->wallet;
		}
		
		if ($wallet < $invoice->total)
			return new Message('{"error": "not_enought_money_in_wallet", "wallet": '.$wallet.'}');
				
		// withdraw amount
		$upw = new Message();
		$upw->urn = $account->urn;
		//$upw->action = 'update';
		//$upw->wallet = $wallet - $invoice->total;
		$upw->action = 'decrement';
		$upw->field = 'wallet';
		$upw->value = $invoice->total;
		$upw->deliver();
		
		// set payed status to Yes
		$payit = new Message();
		$payit->urn = $i->urn;
		$payit->action = 'update';
		$payit->payed = true;
		$payit->payed_at = TimeOp::now();
		$payit->deliver();		
	
		// return payed status
		$m = new Message();
		$m->info = 'payed';
		$m->total = $invoice->total;
		if ($invoice->uri)
			$m->uri = $invoice->uri;
		//$m->wallet = $NEEDPRELOADwallet;
		
		// deliver goos or service
		// TODO MQ Payed callback
		$invoice = $invoice->urn->resolve();
		//dprintln("payed.{$invoice->mqname}",1,TERM_YELLOW);
		if ($invoice->mqname)
			Broker::instance()->send($invoice, "MANAGERS", "payed.{$invoice->mqname}");
		else 
			Log::debug("$invoice has no MQNAME for notify payed",'mqincost');
		
		return $m;
	}

}

?>