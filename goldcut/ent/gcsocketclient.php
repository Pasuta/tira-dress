<?php 
class GcSocketClient
{
	private $connection;
	private $authed = null;
	
	function __construct($host, $port)
	{
		Log::debug("SOCKET OPEN {$host}:{$port}", 'net');
		$ip = gethostbyname($host);
		//Log::debug("DNS RESOLVED IP: {$ip}", 'net');
		$this->connection = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($this->connection === false) 
		{
			throw new Exception("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
		}
		$timeout = array('sec' => 2,'usec' => 500);
		socket_set_option($this->connection, SOL_SOCKET, SO_RCVTIMEO, $timeout);
		$result = socket_connect($this->connection, $ip, $port);
		if ($result === false) 
		{
			Log::debug("SOCKET TIMEOUT", 'net');
			throw new Exception("socket_connect() failed. Reason: ($result) " . socket_strerror(socket_last_error($this->connection)));
		}
	}
	
	function auth($credentials)
	{
		$out = (int) $this->sendAndRecieve($credentials);
        var_dump($out);
		if ($out == 200)
		{
			$this->authed = true;
		}
		else if ($out == 401)
		{
			$this->authed = false;
			throw new Exception("Auth incorrect");
		}
		else
		{
			throw new Exception("Auth request in incorrect format ($out)");
		}
	}
	
	function sendAndRecieve($in, $maxbytes=2048, $silent=false)
	{
		//if (is_string($in))
		if ($this->authed === false) throw new Exception('Auth first');
		Log::debug('TCP>> '.$in, 'net');
		//$in .= "\r\n";
        $in .= "\0";
		$byteswritten = socket_write($this->connection, $in, strlen($in));
        if ($byteswritten === false) {
            if ($silent === false) println("SOWRITE " . socket_strerror(socket_last_error()), 1, TERM_RED);
        }
		$out = socket_read($this->connection, $maxbytes); //  возвращает данные в виде строки в случае успеха, или FALSE в случае ошибки (включая случай, когда удалённый хост закрыл соединение).
        if ($out === false) {
            if ($silent === false) println("SOREAD " . socket_strerror(socket_last_error()), 1, TERM_RED);
        }
		else
        {
            Log::debug('TCP<< '.$out, 'net');
            $dataarray = explode("\0",$out);
            $obj = json_decode($dataarray[0], true);
            check_json_decode_result($dataarray[0]);
            return $obj;
        }
        return false;
	}

    function close()
    {
        Log::debug('CLOSE SOCKET MANUAL', 'net');
        socket_close($this->connection);
    }
	
	function __destruct()
	{
        Log::debug('CLOSE SOCKET AUTO', 'net');
		socket_close($this->connection);
    }
}	
?>