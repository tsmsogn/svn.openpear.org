<?php
/**
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class PEG_StringContext implements PEG_IContext
{
    protected $str, $i = 0, $len, $cache;
    
    /**
     * 与えられた文字列とその位置を保持するPEG_Contextインスタンスを生成する。
     *
     * @param string $s 文字列
     */
    function __construct($str) { 
        $this->str = $str; 
        $this->len = strlen($str);
        $this->cache = new PEG_Cache;
    }

    /**
     * @param int $i
     * @return string
     */
    function read($i)
    {
        if ($this->eos() && $i > 0) return false;
        $this->i += $i;
        return substr($this->str, $this->i - $i, $i);
    }
    
    /**
     * @param int $i
     * @return bool
     */
    function seek($i)
    {
        if ($this->len < $i) return false;
        $this->i = $i;
        return true;
    }
    
    /**
     * @return int
     */
    function tell()
    {
        return $this->i;
    }

    /**
     * @return bool
     */
    function eos()
    {
        return $this->len <= $this->i;
    }

    function get()
    {
         return $this->str;   
    }
    
    function save(PEG_IParser $parser, $start, $end, $val)
    {
        $this->cache->save($parser, $start, $end, $val);
    }
    
    function cache(PEG_IParser $parser)
    {
        return $this->cache->cache($parser, $this->tell());
    }
}