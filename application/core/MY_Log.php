<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage MY_Log
 * @category Libraries
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

class MY_Log extends CI_Log
{
	/**
	 * log levels humanized
	 * 
	 * @var array
	 */
	protected $levels = array(
		'Disables logging, Error logging TURNED OFF',
		'Error Messages (including PHP errors)',
		'Debug Messages',
		'Informational Messages',
		'All Messages'

	);

	/**
	 * Write log file
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	$level 	The error level: 'error', 'debug' or 'info'
	 * @param	string	$msg 	The error message
	 * @return	bool
	 */
	public function write_log($level, $msg)
	{
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if ((!isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold)) && ! isset($this->_threshold_array[$this->_levels[$level]]))
		{
			return FALSE;
		}

		$filepath = $this->_log_path.'log-'.date('Y-m-d').'.'.$this->_file_ext;
		$message = '';

		if (!file_exists($filepath))
		{
			$newfile = TRUE;

			// Only add protection to php files
			if ($this->_file_ext === 'php')
			{
				$message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
			}
		}

		if (!$fp = @fopen($filepath, 'ab'))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		// Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
		if (strpos($this->_date_fmt, 'u') !== FALSE)
		{
			$microtime_full = microtime(TRUE);
			$microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
			$date = new DateTime(date('Y-m-d H:i:s.'.$microtime_short, $microtime_full));
			$date = $date->format($this->_date_fmt);
		}
		else
		{
			$date = date($this->_date_fmt);
		}

		$message .= $this->_format_line($level, $date, $msg);

		for ($written = 0, $length = self::strlen($message); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, self::substr($message, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		if (isset($newfile) && $newfile === TRUE)
		{
			chmod($filepath, $this->_file_permissions);
		}

		return is_int($result);
	}
	
	/**
	 * Format the log line.
	 *
	 * This is for extensibility of log formatting
	 * If you want to change the log format, extend the CI_Log class and override this method
	 *
	 * @param	string	$level 		The error level
	 * @param	string	$date 		Formatted date string
	 * @param	string	$message 	The log message
	 * @return	string	Formatted 	log line with a new line character '\n' at the end
	 */
	protected function _format_line($level, $date, $message)
	{
		$format = array(
			'text' => "[ ".$date." ] \t [ ".$level." ] \t ".$message."\n",
			'markdown' => "\n[ ".$date." ] \t [ **".$level."** ] \t ".$message."\n\n",
			'json' => array(
				'level' => $level,
				'date' => $date,
				'message' => $message
			)
		);

		if (in_array($this->_file_ext, ['md','markdown']))
		{
			return $format['markdown'];
		}
		elseif (in_array($this->_file_ext, ['text','txt','plaintext']))
		{
			return $format['text'];
		}
		elseif (in_array($this->_file_ext, ['json']))
		{
			return json_encode($format['json']);
		}
		else
		{
			return $format['text'];
		}
	}

	/**
	 * Log levels
	 * 
	 * @return array
	 */
	public function levels()
	{
		return $this->levels;
	}

	/**
	 * Current log level
	 * 
	 * @return string
	 */
	public function current_level()
	{
		if (in_array(config_item('log_threshold'), array_keys($this->levels)))
		{
			return $this->levels[config_item('log_threshold')];
		}
		else
		{
			log_message('error','invalid log_threshold in MY_Log');
			return 'invalid_level';
		}
	}

	/**
	 * Log path
	 * 
	 * @return string
	 */
	public function path()
	{
		return (!empty(config_item('log_path')))?config_item('log_path'):APPPATH.'logs';
	}

	/**
	 * Log file extension
	 * 
	 * @return string
	 */
	public function file_extension()
	{
		return (!empty(config_item('log_file_extension')))?config_item('log_file_extension'):'php';	
	}

	/**
	 * Log files
	 * 
	 * @return array
	 */
	public function files()
	{
		return array_values(array_filter((empty(config_item('log_path')))?get_filenames(APPPATH.'logs'):get_filenames(config_item('log_path')),function($log){
			return ($log !== 'index.html')?$log:false;
		}));
	}
}

/* End of file MY_Log.php */
/* Location: ./application/core/MY_Log.php */