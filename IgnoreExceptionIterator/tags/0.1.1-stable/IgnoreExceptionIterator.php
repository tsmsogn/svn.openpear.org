<?php

  /**
   * IgnoreExceptionIterator - 既存のイテレータの例外を無視するイテレータ
   *
   * @package  IgnoreExceptionIterator
   * @author  Yoshio HANAWA <y@hnw.jp>
   * @copyright  2009 Yoshio HANAWA
   * @license  http://creativecommons.org/licenses/BSD/    New BSD Licence
   * @link  http://openpear.org/package/IgnoreExceptionIterator
   */

class IgnoreExceptionIterator implements OuterIterator
{
  protected $it;
  protected $already_caught_exception = false;

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

  public function __call($method, $params = array())
  {
    if (!$this->already_caught_exception) {
      try {
        $ret = call_user_func_array(array($this->it, $method), $params);
      } catch (Exception $e) {
        // ignore exception
        $this->already_caught_exception = true;
      }
    }
    if ($this->already_caught_exception) {
      $ret = ($method === "valid") ? false :  null;
    }
    return $ret;
  }
}
