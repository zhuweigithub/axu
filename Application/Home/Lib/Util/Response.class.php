<?php
namespace Home\Lib\Util;
class Response
{
	/**  推送消息
	 * @param $records
	 * @param bool $error
	 * @return $this
	 */
	public function send($records, $error = false)
	{
		if ($records) {
			$records = $this->arrayKeysToSnake($records);
		}
		if ($error) {
			$message = ['status' => 'OK', 'results' => $records];
		} else {
			$message = ['status' => 'ERROR', 'errorCode' => $records['errorCode'], 'errorMessage' => $records['errorMessage']];
		}
		return json_encode($message);
	}
	protected function arrayKeysToSnake($snakeArray)
	{
		if (is_array($snakeArray)) {
			foreach ($snakeArray as $k => $v) {
				if (is_array($v)) {
					$v = $this->arrayKeysToSnake($v);
				}
				$snakeArray[$this->snakeToCamel($k)] = $v;
				if ($this->snakeToCamel($k) != $k) {
					unset($snakeArray[$k]);
				}
			}
		}
		return $snakeArray;
	}
	protected function snakeToCamel($val)
	{
		return str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $val))));
	}

}