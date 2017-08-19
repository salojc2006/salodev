<?php
namespace salodev;

class Worker {
	static private $_stopped = true;
	static private $_tasks = array();
	static public function Start($usleep = 1000, $exceptionCatcherCallback = null) {
		self::$_stopped = false;
		while (count(self::$_tasks)) {
			usleep($usleep);
			foreach(self::$_tasks as $taskIndex => $taskInfo) {
				if (self::$_stopped) {
					break 2;
				}
				try {
					$taskInfo['callback']($taskIndex);
				} catch(\Exception $e) {
					if ($taskInfo['persistent']!==true) {
						self::removeTask($taskIndex);
					}
					if (!is_callable($exceptionCatcherCallback)) {
						throw $e;
					}
					$exceptionCatcherCallback($e, $taskInfo);
				}
				if ($taskInfo['persistent']!==true) {
					self::removeTask($taskIndex);
				}
			}
		};
	}
	static public function Stop() {
		self::$_stopped = true;
	}
	static public function AddTask($callback, $persistent = true, $taskName = 'no name') {
		self::$_tasks[] = array('callback' =>$callback, 'persistent' => $persistent, 'taskName' =>$taskName);
		end(self::$_tasks);
		return key(self::$_tasks); // returns index id.
	}
	static public function RemoveTask($taskIndex) {
		if (!isset(self::$_tasks[$taskIndex])) {
			return;
		}
		$task = self::$_tasks[$taskIndex];
		unset(self::$_tasks[$taskIndex]);
	}
	static public function Clear() {
		self::$_tasks = array();
	}
	static public function IsRunning() {
		return !self::$_stopped;
	}
	static public function GetCountTasks() {
		return count(self::$_tasks);
	}
	static public function GetTasksList() {
		return self::$_tasks;
	}
}