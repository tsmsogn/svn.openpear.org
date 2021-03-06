<?php

require 'IO/SWF/Editor.php';

$tagMap = IO_SWF_Tag::$tagMap;

$options = getopt("f:p:m");


if ((isset($options['f']) === false) ||
    (is_readable($options['f']) === false) ||
    (isset($options['p']) === false)) { 
    echo "Usage: php swfextract.php [-m] -f <swf_file> -p <outfile_prefix>\n";
    echo "ex) php swfextract.php -m -f test.swf -p test-\n";
    exit(1);
}

$mc_extract = isset($options['m'])?true:false;

$swffile = $options['f'];
$outfile_prefix  = $options['p'];
$swfdata = file_get_contents($swffile);

$swf = new IO_SWF_Editor();

try {
    $swf->parse($swfdata);
} catch (Exception $e) {
    echo "ERROR: $swffile parse failed\n";
    exit (1);
}

$jpegTables = false;
foreach ($swf->_tags as $tag) {
    $code = $tag->code;
    $length = strlen($tag->content);
    $tagName = $tagMap[$code]['name'];
    $cid = false;
    $ext = false;
    switch ($code) {
    case 8:// JPEGTables;
        $jpegTables = $tag->content;
        break;
    case 6: // DefineBits
    case 21: // DefineBitsJPEG2
    case 35: // DefineBitsJEPG3
        $tag->parseTagContent();
        $cid = $tag->tag->_CharacterID;
        $data = $tag->getJpegData($jpegTables);
        $ext = "jpg";
        break;
    case 20: // DefineLossless
    case 36: // DefineLossless2
        $tag->parseTagContent();
        $cid = $tag->tag->_CharacterID;
        $data = $tag->getPNGData();
        $ext = "png";
        break;
    case 14: // DefineSound
        $tag->parseTagContent();
        $cid = $tag->tag->SoundId;
        $data = $tag->getSoundData();
        $ext = "sound"; // sound default
        switch ($tag->tag->SoundFormat) {
        case 1: // ADPCM
            $ext = "adpcm";
            break;
        case 2: // MP3
            $ext = "mp3";
            break;
        case 11: // Speex
            $ext = "spx";
            break;
        case 15: // Melody (maybe)
            if (strncmp($data, 'melo', 4) === 0) {
                $ext = "mld";
            } else if (strncmp($data, 'MMMD', 4) === 0) {
                $ext = "mmf";
            } else {
                $ext = "melo";
            }
        }
        break;
    case 39: // Sprite
        if ($mc_extract === false) {
            continue; // skip
        }
        $tag->parseTagContent();
        $cid = $tag->tag->_spriteId;
        $data = $swf->getMovieClip($cid);
        $ext = "swf";
        //case 14: // DefineSound
        //break;
    }
    if ($cid !== false) {
        $outfile = "$outfile_prefix$cid.$ext";
        echo $outfile.PHP_EOL;
        file_put_contents($outfile, $data);
    }
}

exit(0);
