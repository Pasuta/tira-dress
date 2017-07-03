<?php
// TODO 2 stage template - feed context data (1 pass, 1 time), feed user data (2 pass, N times)
class Mail
{

	static function sendUserTemplatesContext($user, $mailBaseTemplate, $mailContentTemplate, $contexts)
	{
		if (!count($user)) throw new Exception("User not exists");
		$to = $user->email;
		if (!$to) throw new Exception("User->email not exists");
		
		if ($user->name)
			$nameto = $user->email;
		elseif ($user->nickname)
			$nameto = $user->email;	
		else
			$nameto = $user->email;
		
		$m = new Message();
		$m->action = "load";
		$m->urn = "urn-mailtemplate";
		$m->uri	= $mailContentTemplate;
		$m->last = 1;
		$m->lang = SystemLocale::$REQUEST_LANG;
		$tmpl = $m->deliver();

		if (!count($tmpl)) throw new Exception("mailContentTemplate $mailContentTemplate not exists");

		$from = $tmpl->fromemail;
		$namefrom = $tmpl->fromname;
		$subject = $tmpl->title;

		$body = urldecode($tmpl->mailhtml);
		$T = new Template($body);
		foreach ($contexts as $contextKey => $contextValue)
		{
			$T->context->add($contextKey, $contextValue);
		}
		$body = (string) $T;
		
		$body = str_replace('/img', BASEURL.'/img', $body);
		$body = str_replace('class="FS22"', 'style="font-size:22px;"', $body);
		$body = str_replace('class="FS20"', 'style="font-size:20px;"', $body);
		$body = str_replace('class="FS18"', 'style="font-size:18px;"', $body);
		$body = str_replace('class="FS16"', 'style="font-size:16px;"', $body);
		$body = str_replace('class="FS14"', 'style="font-size:14px;"', $body);

		$fullEmail = Mail::prepare($mailBaseTemplate, $body);

		Mail::send($from, $namefrom, $to, $nameto, $subject, $fullEmail);
		// print $fullEmail;	
	}
	static function prepare($tmplName = 'mail', $body)
	{
		$globalTemplate = file_get_contents(BASE_DIR.'/views/layout/'.$tmplName.'.html');
		$fullEmail = str_replace('<content />', $body, $globalTemplate);
		return $fullEmail;
	}
	
	public static function send($from = null, $namefrom = null, $to, $nameto, $subject, $body)
	{
        if (!is_array($to)) $to = array($to);
        $todbg = json_encode($to);
		Log::debug($todbg, 'mail');
		if (ENV == 'DEVELOPMENT')
		{
            dprintln("mail to:{$todbg} \"$subject\"",1,TERM_GRAY);
            foreach ($to as $to1) Log::debug($body, 'mail-'.$to1);
            // todo return rnd path to html file created with mail
			return null;
		}
		
		require_once BASE_DIR.'/lib/php-mailer/class.phpmailer.php';
		require_once BASE_DIR.'/lib/php-mailer/class.smtp.php';
		try 
		{
			$mail = new PHPMailer(true); // enable exceptions 
			$mail->IsSMTP();
			$mail->Port = 25;
			$mail->Host = $GLOBALS['CONFIG'][ENV]['SMTP']['server'];
			Log::debug("Mail env server {$GLOBALS['CONFIG'][ENV]['SMTP']['server']} @{$GLOBALS['CONFIG'][ENV]['SMTP']['username']}", 'mail');
			if ($GLOBALS['CONFIG'][ENV]['SMTP']['username'])
			{
				$mail->SMTPAuth = true;
				$mail->Username = $GLOBALS['CONFIG'][ENV]['SMTP']['username'];
				$mail->Password = $GLOBALS['CONFIG'][ENV]['SMTP']['password'];
			}
			else
				$mail->SMTPAuth = false;
			$mail->Subject  = $subject;
			
			$mail->From = ($from) ? $from : $GLOBALS['CONFIG'][ENV]['SMTP']['fromEmail'];
			$mail->FromName = ($namefrom) ? $namefrom : $GLOBALS['CONFIG'][ENV]['SMTP']['fromName'];

            foreach ($to as $to1) $mail->AddAddress($to1);

			//$mail->AddReplyTo("email@","Имя");
			// $mail->AltBody    = "enable html"; // optional
			$mail->WordWrap = 80;
			$mail->IsHTML(true);
			$mail->MsgHTML($body);
			
			$mail->Send();
		} 
		catch (phpmailerException $e) 
		{
			println($e->errorMessage(),1,TERM_RED);
		}
	}
	
	private static function legacy_send($from, $namefrom, $to, $nameto, $subject, $message)
	{
		$smtpServer = $GLOBALS['mail']['smtpServer'];
		$port = "25";
		$timeout = "3";
		$username = $GLOBALS['mail']['username'];
		$password = $GLOBALS['mail']['password'];
		$localhost = $GLOBALS['mail']['localhost'];
		$newLine = "\r\n";

		$smtpConnect = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
		$smtpResponse = fgets($smtpConnect, 515);
		if(empty($smtpConnect))
		{
			$output = "Failed to connect: $smtpResponse";
			return $output;
		}
		else
		{
			$logArray['connection'] = "Connected: $smtpResponse";
		}

		fputs($smtpConnect, "HELO $localhost" . $newLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$logArray['heloresponse'] = "$smtpResponse";

		if ($username)
		{
			fputs($smtpConnect,"AUTH LOGIN" . $newLine);
			$smtpResponse = fgets($smtpConnect, 515);
			$logArray['authrequest'] = "$smtpResponse";
	
			fputs($smtpConnect, base64_encode($username) . $newLine);
			$smtpResponse = fgets($smtpConnect, 515);
			$logArray['authusername'] = "$smtpResponse";
		
			fputs($smtpConnect, base64_encode($password) . $newLine);
			$smtpResponse = fgets($smtpConnect, 515);
			$logArray['authpassword'] = "$smtpResponse";
		}

		fputs($smtpConnect, "MAIL FROM: $from" . $newLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$logArray['mailfromresponse'] = "$smtpResponse";

		fputs($smtpConnect, "RCPT TO: $to" . $newLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$logArray['mailtoresponse'] = "$smtpResponse";

		fputs($smtpConnect, "DATA" . $newLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$logArray['data1response'] = "$smtpResponse";

		$headers = "MIME-Version: 1.0" . $newLine;
		$headers .= "To: " . '=?UTF-8?B?'.base64_encode($nameto).'?=' . "<$to>" . $newLine;
		$headers .= "From: " . '=?UTF-8?B?'.base64_encode($namefrom).'?=' . "<$from>" . $newLine;
		$headers .= "Content-type: text/html; charset=UTF-8" . $newLine;

		$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';

		fputs($smtpConnect, "Subject: $subject\n$headers\n\n$message\n.\n");

		$smtpResponse = fgets($smtpConnect, 515);
		$logArray['data2response'] = "$smtpResponse";

		fputs($smtpConnect,"QUIT" . $newLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$logArray['quitresponse'] = "$smtpResponse";

		return $logArray;

	}
	
	
	
}
?>