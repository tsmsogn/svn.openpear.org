<?php

/*
 * 2011/4/15- (c) yoya@awm.jp
 */

require_once 'IO/Bit.php';
require_once dirname(__FILE__).'/../Type.php';
require_once dirname(__FILE__).'/../Exception.php';
require_once dirname(__FILE__).'/../Type/MATRIX.php';
require_once dirname(__FILE__).'/../Type/RGB.php';
require_once dirname(__FILE__).'/../Type/RGBA.php';

class IO_SWF_Type_FILLSTYLE extends IO_SWF_Type {
    static function parse(&$reader, $opts = array()) {
        $tagCode = $opts['tagCode'];
        $isMorph = ($tagCode == 46) || ($tagCode == 84);

        $fillStyle = array();
        $fillStyleType = $reader->getUI8();
        $fillStyle['FillStyleType'] = $fillStyleType;
        switch ($fillStyleType) {
          case 0x00: // solid fill
            if ($isMorph === false) {
                if ($tagCode < 32 ) { // 32:DefineShape3
                    $fillStyle['Color'] = IO_SWF_Type_RGB::parse($reader);
                } else {
                    $fillStyle['Color'] = IO_SWF_Type_RGBA::parse($reader);
                }
            } else {
                    $fillStyle['StartColor'] = IO_SWF_Type_RGBA::parse($reader);
                    $fillStyle['EndColor'] = IO_SWF_Type_RGBA::parse($reader);
            }
            break;
          case 0x10: // linear gradient fill
          case 0x12: // radial gradient fill
            if ($isMorph === false) {
                $fillStyle['GradientMatrix'] = IO_SWF_Type_MATRIX::parse($reader);
            } else {
                $fillStyle['StartGradientMatrix'] = IO_SWF_Type_MATRIX::parse($reader);
                $fillStyle['EndGradientMatrix'] = IO_SWF_Type_MATRIX::parse($reader);
            }
            $reader->byteAlign();
            if ($isMorph === false) {
                $fillStyle['SpreadMode'] = $reader->getUIBits(2);
                $fillStyle['InterpolationMode'] = $reader->getUIBits(2);
                $numGradients = $reader->getUIBits(4);
            } else {
                $numGradients = $reader->getUI8();
            }
            $fillStyle['GradientRecords'] = array();
            for ($j = 0 ; $j < $numGradients ; $j++) {
                $gradientRecord = array();
                if ($isMorph === false) {
                    $gradientRecord['Ratio'] = $reader->getUI8();
                    if ($tagCode < 32 ) { // 32:DefineShape3
                        $gradientRecord['Color'] = IO_SWF_Type_RGB::parse($reader);
                    } else {
                        $gradientRecord['Color'] = IO_SWF_Type_RGBA::parse($reader);
                    }
                } else { // Morph
                    $gradientRecord['StartRatio'] = $reader->getUI8();
                    $gradientRecord['EndRatio'] = $reader->getUI8();
                    $gradientRecord['StartColor'] = IO_SWF_Type_RGBA::parse($reader);
                    $gradientRecord['EndColor'] = IO_SWF_Type_RGBA::parse($reader);
                }
                $fillStyle['GradientRecords'] []= $gradientRecord;
            }
            break;
          // case 0x13: // focal gradient fill // 8 and later
          // break;
          case 0x40: // repeating bitmap fill
          case 0x41: // clipped bitmap fill
          case 0x42: // non-smoothed repeating bitmap fill
          case 0x43: // non-smoothed clipped bitmap fill
                $fillStyle['BitmapId'] = $reader->getUI16LE();
            if ($isMorph === false) {
                $fillStyle['BitmapMatrix'] = IO_SWF_Type_MATRIX::parse($reader);
            } else {
                $fillStyle['StartBitmapMatrix'] = IO_SWF_Type_MATRIX::parse($reader);
                $fillStyle['EndBitmapMatrix'] = IO_SWF_Type_MATRIX::parse($reader);
            }
            break;
          default:
            throw new IO_SWF_Exception("Unknown FillStyleType=$fillStyleType tagCode=$tagCode");
        }
        return $fillStyle;
    }
    static function build(&$writer, $fillStyle, $opts = array()) {
        $tagCode = $opts['tagCode'];
        $isMorph = ($tagCode == 46) || ($tagCode == 84);

        $fillStyleType = $fillStyle['FillStyleType'];
        $writer->putUI8($fillStyleType);
        switch ($fillStyleType) {
          case 0x00: // solid fill
            if ($tagCode < 32 ) { // 32:DefineShape3
                IO_SWF_Type_RGB::build($writer, $fillStyle['Color']);
            } else {
                IO_SWF_Type_RGBA::build($writer, $fillStyle['Color']);
            }
            break;
          case 0x10: // linear gradient fill
          case 0x12: // radial gradient fill
            IO_SWF_Type_MATRIX::build($writer, $fillStyle['GradientMatrix']);
            $writer->byteAlign();
            $writer->putUIBits($fillStyle['SpreadMode'], 2);
            $writer->putUIBits($fillStyle['InterpolationMode'], 2);
            $numGradients = count($fillStyle['GradientRecords']);
            $writer->putUIBits($numGradients , 4);
            foreach ($fillStyle['GradientRecords'] as $gradientRecord) {
                $writer->putUI8($gradientRecord['Ratio']);
                if ($tagCode < 32 ) { // 32:DefineShape3
                    IO_SWF_Type_RGB::build($writer, $gradientRecord['Color']);
                } else {
                    IO_SWF_Type_RGBA::build($writer, $gradientRecord['Color']);
                }
            }
          break;
          // case 0x13: // focal gradient fill // 8 and later
          // break;
          case 0x40: // repeating bitmap fill
          case 0x41: // clipped bitmap fill
          case 0x42: // non-smoothed repeating bitmap fill
          case 0x43: // non-smoothed clipped bitmap fill
            $writer->putUI16LE($fillStyle['BitmapId']);
            IO_SWF_Type_MATRIX::build($writer, $fillStyle['BitmapMatrix']);
            break;
          default:
            throw new IO_SWF_Exception("Unknown FillStyleType=$fillStyleType tagCode=$tagCode");
        }
        return true;
    }
    static function string($fillStyle, $opts = array()) {
        $tagCode = $opts['tagCode'];
        $isMorph = ($tagCode == 46) || ($tagCode == 84);

        $text = '';
        $fillStyleType = $fillStyle['FillStyleType'];
        switch ($fillStyleType) {
          case 0x00: // solid fill
            $color = $fillStyle['Color'];
            if ($tagCode < 32 ) { // 32:DefineShape3
                $color_str = IO_SWF_Type_RGB::string($color);
            } else {
                $color_str = IO_SWF_Type_RGBA::string($color);
            }
            $text .= "\tsolid fill: $color_str\n";
            break;
          case 0x10: // linear gradient fill
          case 0x12: // radial gradient fill
            if ($fillStyleType == 0x10) {
                $text .= "\tlinear gradient fill\n";
            } else {
                $text .= "\tradial gradient fill\n";
            }
            $opts = array('indent' => 2);
            if ($isMorph === false) {
                $matrix_str = IO_SWF_Type_MATRIX::string($fillStyle['GradientMatrix'], $opts);
                $text .= $matrix_str . "\n";
                $spreadMode = $fillStyle['SpreadMode'];
                $interpolationMode = $fillStyle['InterpolationMode'];
            } else {
                $matrix_str = IO_SWF_Type_MATRIX::string($fillStyle['StartGradientMatrix'], $opts);
                $matrix_str = IO_SWF_Type_MATRIX::string($fillStyle['EndGradientMatrix'], $opts);
                $text .= $matrix_str . "\n";
            }

            foreach ($fillStyle['GradientRecords'] as $gradientRecord) {
                if ($isMorph === false) {
                    $ratio = $gradientRecord['Ratio'];
                    $color = $gradientRecord['Color'];
                    if ($tagCode < 32 ) { // 32:DefineShape3
                        $color_str = IO_SWF_Type_RGB::string($color);
                    } else {
                        $color_str = IO_SWF_Type_RGBA::string($color);
                    }
                    $text .= "\t\tRatio: $ratio Color:$color_str\n";
                } else {
                    $startRatio = $gradientRecord['StartRatio'];
                    $endRatio   = $gradientRecord['EndRatio'];
                    $startColorStr = IO_SWF_Type_RGBA::string($gradientRecord['StartColor']);
                    $endColorStr = IO_SWF_Type_RGBA::string($gradientRecord['EndColor']);
                    $text .= "\t\tRatio: $startRatio => $endRatio Color:$startColorStr => endColorStr\n";
                }
            }
            break;
          case 0x40: // repeating bitmap fill
          case 0x41: // clipped bitmap fill
          case 0x42: // non-smoothed repeating bitmap fill
          case 0x43: // non-smoothed clipped bitmap fill
            $text .= "\tBigmap($fillStyleType): ";
            $text .= "  BitmapId: ".$fillStyle['BitmapId']."\n";
            if ($isMorph === false) {
                $text .= "\tBitmapMatrix:\n";
                $opts = array('indent' => 2);
                $matrix_str = IO_SWF_Type_MATRIX::string($fillStyle['BitmapMatrix'], $opts);
                $text .= $matrix_str . "\n";
            } else {
                $opts = array('indent' => 2);
                $text .= "\tStartBitmapMatrix:\n";
                $matrix_str = IO_SWF_Type_MATRIX::string($fillStyle['StartBitmapMatrix'], $opts);
                $text .= $matrix_str . "\n";
                $text .= "\tEndBitmapMatrix:\n";
                $matrix_str = IO_SWF_Type_MATRIX::string($fillStyle['EndBitmapMatrix'], $opts);
                $text .= $matrix_str . "\n";
            }
            break;
          default:
            $text .= "Unknown FillStyleType($fillStyleType)\n";
        }
        return $text;
    }
}
