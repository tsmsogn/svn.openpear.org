<?php

/*
 * 2011/7/11- (c) yoya@awm.jp
 */

require_once 'IO/Bit.php';
require_once dirname(__FILE__).'/../Type.php';
require_once dirname(__FILE__).'/CLIPEVENTFLAGS.php';
require_once dirname(__FILE__).'/CLIPACTIONRECORD.php';

class IO_SWF_Type_CLIPACTIONS extends IO_SWF_Type {
    static function parse(&$reader, $opts = array()) {
    	$clipactions = array();
        $clipactions['Reserved'] = $reader->getUI16LE(); // must be 0
        $clipactions['AllEventFlags'] = IO_SWF_Type_CLIPEVENTFLAGS::parse($reader, $opts);
        $clipActionRecords = array();
        while (true) {
            if ($opts['Version'] <= 5) {
                if ($reader->getUI16LE() == 0) {
                    break;
                }
                $reader->incrementOffset(-2, 0); // 2 bytes back
            } else {
                if ($reader->getUI32LE() == 0) {
                    break;
                }
                $reader->incrementOffset(-4, 0); // 4 bytes back
            }
            $clipActionRecords []= IO_SWF_Type_CLIPACTIONRECORD::parse($reader, $opts);
        }
        $clipactions['ClipActionRecords'] = $clipActionRecords;
    	return $clipactions;
    }
    static function build(&$writer, $clipactions, $opts = array()) {
    	$writer->putUI16LE($clipactions['Reserved']); // must be 0
        IO_SWF_Type_CLIPEVENTFLAGS::build($writer, $clipactions['AllEventFlags'], $opts);
        foreach ($clipactions['ClipActionRecords'] as $clipActionRecord) {
            IO_SWF_Type_CLIPACTIONRECORD::build($reader, $clipActionRecord, $opts);
        }
        if ($opts['Version'] <= 5) {
            $writer->putUI16LE(0); // ClipActionEndFlag
        } else {
            $writer->putUI32LE(0); // ClipActionEndFlag
        }
    }
    static function string($clipactions, $opts = array()) {
        $text = '';
        $text .= IO_SWF_Type_CLIPACTIONRECORD::string($clipactions, $opts);
        $text .= "\n";
        foreach ($clipactions['ClipActionRecords'] as $clipActionRecord) {
            $text .= "\t".IO_SWF_Type_CLIPACTIONRECORD::string($clipActionRecord, $opts)."\n";
        }
    	return $text;
    }
}
