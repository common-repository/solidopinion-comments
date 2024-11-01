<?php
class SMTPMailer
{
    private $config;

    public function __construct()
    {
		$this->config['smtp_username'] = 'solidopinion.mailer@gmail.com';
		$this->config['smtp_port']	 = 465;
		$this->config['smtp_host']	 = 'ssl://smtp.gmail.com';
		$this->config['smtp_password'] = 'mailersolidopinion3';
		$this->config['smtp_debug']	 = true;
		$this->config['smtp_charset']  = 'utf-8';
		$this->config['smtp_from']	 = 'Solidopinion Mailer';
    }
	
	public function smtpmail($mail_to, $subject, $message, $attachment, $content) {
	
		$config = $this->config;
		$uid = md5(uniqid(time())); 
		$SEND =	"Date: ".date("D, d M Y H:i:s") . " UT\r\n";
		$SEND .='Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
		$SEND .= "From: \"".$config['smtp_from']."\" <".$config['smtp_username'].">\r\n";
		$SEND .= "To: $mail_to\r\n";
		$SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
		$SEND .= "MIME-Version: 1.0\r\n";
		
		if (!!$attachment) {
			$SEND .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n"; 
			$SEND .= "This is a multi-part message in MIME format.\r\n"; 
			$SEND .= "--".$uid."\r\n"; 
		}
		$SEND .= "Content-Type: text/html; charset=\"".$config['smtp_charset']."\"\r\n";
		$SEND .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$SEND .= $message."\r\n\r\n";
		
		if (!!$attachment) {
			$SEND .= "--".$uid."\r\n"; 
			$SEND .= "Content-Type: application/octet-stream; name=\"".$attachment.".zip\"\r\n"; 
			$SEND .= "Content-Transfer-Encoding: base64\r\n"; 
			$SEND .= "Content-Disposition: attachment; filename=\"".$attachment.".zip\"\r\n\r\n"; 
			$SEND .= $content."\r\n\r\n"; 
			$SEND .= "--".$uid."--"; 
		}
		
		 if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
			if ($config['smtp_debug']) echo $errno."<br>".$errstr;
			return false;
		 }

		if (!$this->server_parse($socket, "220", __LINE__)) return false;

		fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
		if (!$this->server_parse($socket, "250", __LINE__)) {
			if ($config['smtp_debug']) echo '<p>Не могу отправить HELO!</p>';
			fclose($socket);
			return false;
		}
		fputs($socket, "AUTH LOGIN\r\n");
		if (!$this->server_parse($socket, "334", __LINE__)) {
			if ($config['smtp_debug']) echo '<p>Не могу найти ответ на запрос авторизаци.</p>';
			fclose($socket);
			return false;
		}
		fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
		if (!$this->server_parse($socket, "334", __LINE__)) {
			if ($config['smtp_debug']) echo '<p>Логин авторизации не был принят сервером!</p>';
			fclose($socket);
			return false;
		}
		fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
		if (!$this->server_parse($socket, "235", __LINE__)) {
			if ($config['smtp_debug']) echo '<p>Пароль не был принят сервером как верный! Ошибка авторизации!</p>';
			fclose($socket);
			return false;
		}
		fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
		if (!$this->server_parse($socket, "250", __LINE__)) {
			if ($config['smtp_debug']) echo '<p>Не могу отправить комманду MAIL FROM: </p>';
			fclose($socket);
			return false;
		}
		fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

		if (!$this->server_parse($socket, "250", __LINE__)) {
			if ($config['smtp_debug']) echo '<p>Не могу отправить комманду RCPT TO: </p>';
			fclose($socket);
			return false;
		}
		fputs($socket, "DATA\r\n");

		if (!$this->server_parse($socket, "354", __LINE__)) {
			if ($config['smtp_debug']) echo '<p>Не могу отправить комманду DATA</p>';
			fclose($socket);
			return false;
		}
		fputs($socket, $SEND."\r\n.\r\n");

		if (!$this->server_parse($socket, "250", __LINE__)) {
			if ($config['smtp_debug']) echo '<p>Не смог отправить тело письма. Письмо не было отправленно!</p>';
			fclose($socket);
			return false;
		}
		fputs($socket, "QUIT\r\n");
		fclose($socket);
		return TRUE;
	}

	private function server_parse($socket, $response, $line = __LINE__) {
		$config = $this->config;
		while (@substr($server_response, 3, 1) != ' ') {
			if (!($server_response = fgets($socket, 256))) {
				if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
				return false;
			}
		}
		if (!(substr($server_response, 0, 3) == $response)) {
			if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
			return false;
		}
		return true;
	}
}