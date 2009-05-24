<?php

  /**
   * LoggerIterator - 既存のイテレータにログ取得機能を追加するイテレータ
   *
   * @package  LoggerIterator
   * @author  Yoshio HANAWA <y@hnw.jp>
   * @copyright  2009 Yoshio HANAWA
   * @license  http://creativecommons.org/licenses/BSD/    New BSD Licence
   * @link  http://openpear.org/package/LoggerIterator
   */

class LoggerIterator Implements OuterIterator
{
  private $it;

  public function __construct($it)
  {
    $this->it = $it;
  }
  public function getInnerIterator()
  {
    return $this->it;
  }
  public function current() { return $this->__call("current"); }
  public function key()     { return $this->__call("key"); }
  public function next()    { return $this->__call("next"); }
  public function rewind()  { return $this->__call("rewind"); }
  public function valid()   { return $this->__call("valid"); }

  public function __call($func, $params = array())
  {
    $ret = call_user_func_array(array($this->it, $func), $params);
    printf("%s: %s::%s(%s) = ",
           __CLASS__,
           get_class($this->it), $func,
           join(",", $params));
    var_dump($ret);
    return $ret;
  }
}
