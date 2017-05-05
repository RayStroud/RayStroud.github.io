<?php
	//if button is pressed
	if( isset($_POST['sendMessage']) )
	{
		//get vars
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$message = trim($_POST['message']);
		$ip = $_SERVER['REMOTE_ADDR'];
		//echo | $name | $email | $message | $ip";

		//set validation var
		$bSendMail = true;

		//maintain list of known spammers
		$spams = array 
		(
			'static.16.86.46.78.clients.your-server.de', 
			'87.101.244.8', 
			'144.229.34.5', 
			'89.248.168.70',
			'reserve.cableplus.com.cn',
			'94.102.60.182',
			'194.8.75.145',
			'194.8.75.50',
			'194.8.75.62',
			'194.170.32.252',
			//'127.0.0.1'
		);
		
		//check ip for known spammers
		foreach ($spams as $spammer) 
		{
			$pattern = "/$spammer/i";
			if (preg_match ($pattern, $ip)) 
			{
				$bSendMail = false;
			}
		}

		//protect against email injections
		if($bSendMail)
		{
			//list of possible email injections
			$emailInjections = array(
				"Content-Type:",
				"MIME-Version:",
				"Content-Transfer-Encoding:",
				"bcc:",
				"cc:"
			);

			//loop through all POST vars to check for possible email injections
			foreach($_POST as $k => $v)
			{
				foreach($emailInjections as $v2)
				{
					if(strpos($v, $v2) !== false)
					{
						//header("HTTP/1.0 403 Forbidden");
						$bSendMail = false;
					}
				}
			}
		}

		//make email message
		if($bSendMail)
		{
			$mailTo 		= 'ray.stroud+ca@gmail.com';
			$mailSubject 	= 'RayStroud.ca Contact:' . $name;
			
			$mailMessage  = "Name: $name \r\n";
			$mailMessage .= "Email: $email \r\n";
			$mailMessage .= "IP: $ip \r\n";
			$mailMessage .= "-------------------------------------- \r\n\r\n";
			$mailMessage .= $message;
			
			$mailHeaders = "MIME-Version: 1.0\r\n";
			$mailHeaders .= "Content-type: text/plain; charset=iso-8859-1\r\n";
			$mailHeaders .= "X-Priority: 1\r\n";
			$mailHeaders .= "X-MSMail-Priority: Normal\r\n";
			$mailHeaders .= "X-Mailer: php\r\n";
			$mailHeaders .= "From: $name <$email>\r\n";

			$bSuccess = mail($mailTo, $mailSubject, $mailMessage, $mailHeaders);
		}

		if(isset($bSuccess))
		{
			header('Location: msgsent.htm');
		}
		else
		{
			header('Location: msgerror.htm');			
		}
	}
?>