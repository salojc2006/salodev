<?php
namespace salodev\IO;

use Exception;

class StandardOutput extends ClientStream {
	/**
	 *
	 * @var Stream
	 */
	protected static $_stream = null;
	
	public function __construct(array $options = []) {
		$spec = 'php://stdout';
		$mode = 'w';
		if (self::$_stream instanceof StandardOutput) {
			throw new Exception('singleton violation');
		}
		self::$_stream = $this;
		parent::__construct(array_merge([
			'spec' => $spec,
			'mode' => $mode,
		], $options));
	}
	
	public function writeLine($content) {
		return $this->write($content . "\n");
	}
	
	public function close(): Stream {
		static::$_stream = null;
		parent::close();
	}
}