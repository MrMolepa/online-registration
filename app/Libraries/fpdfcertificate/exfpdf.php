<?php


namespace App\Libraries\fpdfcertificate;

require_once('FPDF.php');
require_once('fpdi2/src/autoload.php');

use setasign\Fpdi\Fpdi;

include 'formatedstring.php';


/*********************************************************************
 * exFPDF  extend FPDF v1.81                                                    *
 *                                                                    *
 * Version: 2.2                                                       *
 * Date:    12-10-2017                                                *
 * Author:  Dan Machado                                               *
 * Require  FPDF v1.81, formatedstring v1.0                                                *
 **********************************************************************/

class exFPDF extends FPDI
{

    public function PageBreak()
    {
        return $this->PageBreakTrigger;
    }

    var $angle = 0;

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    function Code39($x, $y, $code_name, $code, $ext = true, $cks = false, $w = 0.35, $h = 8, $wide = true)
    {

        //Display code
        $this->SetFont('Arial', '', 12);
        $this->Text($x, $y - 3, $code_name);
        $this->Text($x, $y + $h + 6, $code);

        if ($ext) {
            //Extended encoding
            $code = $this->encode_code39_ext($code);
        } else {
            //Convert to upper case
            $code = strtoupper($code);
            //Check validity
            if (!preg_match('|^[0-9A-Z. $/+%-]*$|', $code))
                $this->Error('Invalid barcode value: ' . $code);
        }

        //Compute checksum
        if ($cks)
            $code .= $this->checksum_code39($code);

        //Add start and stop characters
        $code = '*' . $code . '*';

        //Conversion tables
        $narrow_encoding = array(
            '0' => '101001101101',
            '1' => '110100101011',
            '2' => '101100101011',
            '3' => '110110010101',
            '4' => '101001101011',
            '5' => '110100110101',
            '6' => '101100110101',
            '7' => '101001011011',
            '8' => '110100101101',
            '9' => '101100101101',
            'A' => '110101001011',
            'B' => '101101001011',
            'C' => '110110100101',
            'D' => '101011001011',
            'E' => '110101100101',
            'F' => '101101100101',
            'G' => '101010011011',
            'H' => '110101001101',
            'I' => '101101001101',
            'J' => '101011001101',
            'K' => '110101010011',
            'L' => '101101010011',
            'M' => '110110101001',
            'N' => '101011010011',
            'O' => '110101101001',
            'P' => '101101101001',
            'Q' => '101010110011',
            'R' => '110101011001',
            'S' => '101101011001',
            'T' => '101011011001',
            'U' => '110010101011',
            'V' => '100110101011',
            'W' => '110011010101',
            'X' => '100101101011',
            'Y' => '110010110101',
            'Z' => '100110110101',
            '-' => '100101011011',
            '.' => '110010101101',
            ' ' => '100110101101',
            '*' => '100101101101',
            '$' => '100100100101',
            '/' => '100100101001',
            '+' => '100101001001',
            '%' => '101001001001'
        );

        $wide_encoding = array(
            '0' => '101000111011101',
            '1' => '111010001010111',
            '2' => '101110001010111',
            '3' => '111011100010101',
            '4' => '101000111010111',
            '5' => '111010001110101',
            '6' => '101110001110101',
            '7' => '101000101110111',
            '8' => '111010001011101',
            '9' => '101110001011101',
            'A' => '111010100010111',
            'B' => '101110100010111',
            'C' => '111011101000101',
            'D' => '101011100010111',
            'E' => '111010111000101',
            'F' => '101110111000101',
            'G' => '101010001110111',
            'H' => '111010100011101',
            'I' => '101110100011101',
            'J' => '101011100011101',
            'K' => '111010101000111',
            'L' => '101110101000111',
            'M' => '111011101010001',
            'N' => '101011101000111',
            'O' => '111010111010001',
            'P' => '101110111010001',
            'Q' => '101010111000111',
            'R' => '111010101110001',
            'S' => '101110101110001',
            'T' => '101011101110001',
            'U' => '111000101010111',
            'V' => '100011101010111',
            'W' => '111000111010101',
            'X' => '100010111010111',
            'Y' => '111000101110101',
            'Z' => '100011101110101',
            '-' => '100010101110111',
            '.' => '111000101011101',
            ' ' => '100011101011101',
            '*' => '100010111011101',
            '$' => '100010001000101',
            '/' => '100010001010001',
            '+' => '100010100010001',
            '%' => '101000100010001'
        );

        $encoding = $wide ? $wide_encoding : $narrow_encoding;

        //Inter-character spacing
        $gap = ($w > 0.29) ? '00' : '0';

        //Convert to bars
        $encode = '';
        for ($i = 0; $i < strlen($code); $i++)
            $encode .= $encoding[$code[$i]] . $gap;

        //Draw bars
        $this->draw_code39($encode, $x, $y, $w, $h);
    }

    function checksum_code39($code)
    {

        //Compute the modulo 43 checksum

        $chars = array(
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            '-',
            '.',
            ' ',
            '$',
            '/',
            '+',
            '%'
        );
        $sum = 0;
        for ($i = 0; $i < strlen($code); $i++) {
            $a = array_keys($chars, $code[$i]);
            $sum += $a[0];
        }
        $r = $sum % 43;
        return $chars[$r];
    }

    function encode_code39_ext($code)
    {

        //Encode characters in extended mode

        $encode = array(
            chr(0) => '%U',
            chr(1) => '$A',
            chr(2) => '$B',
            chr(3) => '$C',
            chr(4) => '$D',
            chr(5) => '$E',
            chr(6) => '$F',
            chr(7) => '$G',
            chr(8) => '$H',
            chr(9) => '$I',
            chr(10) => '$J',
            chr(11) => '�K',
            chr(12) => '$L',
            chr(13) => '$M',
            chr(14) => '$N',
            chr(15) => '$O',
            chr(16) => '$P',
            chr(17) => '$Q',
            chr(18) => '$R',
            chr(19) => '$S',
            chr(20) => '$T',
            chr(21) => '$U',
            chr(22) => '$V',
            chr(23) => '$W',
            chr(24) => '$X',
            chr(25) => '$Y',
            chr(26) => '$Z',
            chr(27) => '%A',
            chr(28) => '%B',
            chr(29) => '%C',
            chr(30) => '%D',
            chr(31) => '%E',
            chr(32) => ' ',
            chr(33) => '/A',
            chr(34) => '/B',
            chr(35) => '/C',
            chr(36) => '/D',
            chr(37) => '/E',
            chr(38) => '/F',
            chr(39) => '/G',
            chr(40) => '/H',
            chr(41) => '/I',
            chr(42) => '/J',
            chr(43) => '/K',
            chr(44) => '/L',
            chr(45) => '-',
            chr(46) => '.',
            chr(47) => '/O',
            chr(48) => '0',
            chr(49) => '1',
            chr(50) => '2',
            chr(51) => '3',
            chr(52) => '4',
            chr(53) => '5',
            chr(54) => '6',
            chr(55) => '7',
            chr(56) => '8',
            chr(57) => '9',
            chr(58) => '/Z',
            chr(59) => '%F',
            chr(60) => '%G',
            chr(61) => '%H',
            chr(62) => '%I',
            chr(63) => '%J',
            chr(64) => '%V',
            chr(65) => 'A',
            chr(66) => 'B',
            chr(67) => 'C',
            chr(68) => 'D',
            chr(69) => 'E',
            chr(70) => 'F',
            chr(71) => 'G',
            chr(72) => 'H',
            chr(73) => 'I',
            chr(74) => 'J',
            chr(75) => 'K',
            chr(76) => 'L',
            chr(77) => 'M',
            chr(78) => 'N',
            chr(79) => 'O',
            chr(80) => 'P',
            chr(81) => 'Q',
            chr(82) => 'R',
            chr(83) => 'S',
            chr(84) => 'T',
            chr(85) => 'U',
            chr(86) => 'V',
            chr(87) => 'W',
            chr(88) => 'X',
            chr(89) => 'Y',
            chr(90) => 'Z',
            chr(91) => '%K',
            chr(92) => '%L',
            chr(93) => '%M',
            chr(94) => '%N',
            chr(95) => '%O',
            chr(96) => '%W',
            chr(97) => '+A',
            chr(98) => '+B',
            chr(99) => '+C',
            chr(100) => '+D',
            chr(101) => '+E',
            chr(102) => '+F',
            chr(103) => '+G',
            chr(104) => '+H',
            chr(105) => '+I',
            chr(106) => '+J',
            chr(107) => '+K',
            chr(108) => '+L',
            chr(109) => '+M',
            chr(110) => '+N',
            chr(111) => '+O',
            chr(112) => '+P',
            chr(113) => '+Q',
            chr(114) => '+R',
            chr(115) => '+S',
            chr(116) => '+T',
            chr(117) => '+U',
            chr(118) => '+V',
            chr(119) => '+W',
            chr(120) => '+X',
            chr(121) => '+Y',
            chr(122) => '+Z',
            chr(123) => '%P',
            chr(124) => '%Q',
            chr(125) => '%R',
            chr(126) => '%S',
            chr(127) => '%T'
        );

        $code_ext = '';
        for ($i = 0; $i < strlen($code); $i++) {
            if (ord($code[$i]) > 127)
                $this->Error('Invalid character: ' . $code[$i]);
            $code_ext .= $encode[$code[$i]];
        }
        return $code_ext;
    }

    function draw_code39($code, $x, $y, $w, $h)
    {

        //Draw bars

        for ($i = 0; $i < strlen($code); $i++) {
            if ($code[$i] == '1')
                $this->Rect($x + $i * $w, $y, $w, $h, 'F');
        }
    }






    public function current_font($c)
    {
        if ($c == 'family') {
            return $this->FontFamily;
        } elseif ($c == 'style') {
            return $this->FontStyle;
        } elseif ($c == 'size') {
            return $this->FontSizePt;
        }
    }

    public function get_color($c)
    {
        if ($c == 'fill') {
            return $this->FillColor;
        } elseif ($c == 'text') {
            return $this->TextColor;
        }
    }

    public function get_page_width()
    {
        return $this->w;
    }

    public function get_margin($c)
    {
        if ($c == 'l') {
            return $this->lMargin;
        } elseif ($c == 'r') {
            return $this->rMargin;
        } elseif ($c == 't') {
            return $this->tMargin;
        }
    }

    public function get_linewidth()
    {
        return $this->LineWidth;
    }

    public function get_orientation()
    {
        return $this->CurOrientation;
    }
    static private $hex = array(
        '0' => 0,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'A' => 10,
        'B' => 11,
        'C' => 12,
        'D' => 13,
        'E' => 14,
        'F' => 15
    );

    public function is_rgb($str)
    {
        $a = true;
        $tmp = explode(',', trim($str, ','));
        foreach ($tmp as $color) {
            if (!is_numeric($color) || $color < 0 || $color > 255) {
                $a = false;
                break;
            }
        }
        return $a;
    }

    public function is_hex($str)
    {
        $a = true;
        $str = strtoupper($str);
        $n = strlen($str);
        if (($n == 7 || $n == 4) && $str[0] == '#') {
            for ($i = 1; $i < $n; $i++) {
                if (!isset(self::$hex[$str[$i]])) {
                    $a = false;
                    break;
                }
            }
        } else {
            $a = false;
        }
        return $a;
    }

    public function hextodec($str)
    {
        $result = array();
        $str = strtoupper(substr($str, 1));
        $n = strlen($str);
        for ($i = 0; $i < 3; $i++) {
            if ($n == 6) {
                $result[$i] = self::$hex[$str[2 * $i]] * 16 + self::$hex[$str[2 * $i + 1]];
            } else {
                $result[$i] = self::$hex[$str[$i]] * 16 + self::$hex[$str[$i]];
            }
        }
        return $result;
    }
    static private $options = array('F' => '', 'T' => '', 'D' => '');

    public function resetColor($str, $p = 'F')
    {
        if (isset(self::$options[$p]) && self::$options[$p] != $str) {
            self::$options[$p] = $str;
            $array = array();
            if ($this->is_hex($str)) {
                $array = $this->hextodec($str);
            } elseif ($this->is_rgb($str)) {
                $array = explode(',', trim($str, ','));
                for ($i = 0; $i < 3; $i++) {
                    if (!isset($array[$i])) {
                        $array[$i] = 0;
                    }
                }
            } else {
                $array = array(null, null, null);
                $i = 0;
                $tmp = explode(' ', $str);
                foreach ($tmp as $c) {
                    if (is_numeric($c)) {
                        $array[$i] = $c * 256;
                        $i++;
                    }
                }
            }
            if ($p == 'T') {
                $this->SetTextColor($array[0], $array[1], $array[2]);
            } elseif ($p == 'D') {
                $this->SetDrawColor($array[0], $array[1], $array[2]);
            } elseif ($p == 'F') {
                $this->SetFillColor($array[0], $array[1], $array[2]);
            }
        }
    }
    static private $font_def = '';

    public function resetFont($font_family, $font_style, $font_size)
    {
        if (self::$font_def != $font_family . '-' . $font_style . '-' . $font_size) {
            self::$font_def = $font_family . '-' . $font_style . '-' . $font_size;
            $this->SetFont($font_family, $font_style, $font_size);
        }
    }

    public function resetStaticData()
    {
        self::$font_def = '';
        self::$options = array('F' => '', 'T' => '', 'D' => '');
    }
    /***********************************************************************
     *
     * Based on FPDF method SetFont
     *
     ************************************************************************/

    private function &FontData($family, $style, $size)
    {
        if ($family == '')
            $family = $this->FontFamily;
        else
            $family = strtolower($family);
        $style = strtoupper($style);
        if (strpos($style, 'U') !== false) {
            $this->underline = true;
            $style = str_replace('U', '', $style);
        }
        if ($style == 'IB')
            $style = 'BI';
        $fontkey = $family . $style;
        if (!isset($this->fonts[$fontkey])) {
            if ($family == 'arial')
                $family = 'helvetica';
            if (in_array($family, $this->CoreFonts)) {
                if ($family == 'symbol' || $family == 'zapfdingbats')
                    $style = '';
                $fontkey = $family . $style;
                if (!isset($this->fonts[$fontkey]))
                    $this->AddFont($family, $style);
            } else
                $this->Error('Undefined font: ' . $family . ' ' . $style);
        }
        $result['FontSize'] = $size / $this->k;
        $result['CurrentFont'] = &$this->fonts[$fontkey];
        return $result;
    }


    private function setLines(&$fstring, $p, $q)
    {
        $parced_str = &$fstring->parced_str;
        $lines = &$fstring->lines;
        $linesmap = &$fstring->linesmap;
        $cfty = $fstring->get_current_style($p);
        $ffs = $cfty['font-family'] . $cfty['style'];
        if (!isset($fstring->used_fonts[$ffs])) {
            $fstring->used_fonts[$ffs] = &$this->FontData($cfty['font-family'], $cfty['style'], $cfty['font-size']);
        }
        $cw = &$fstring->used_fonts[$ffs]['CurrentFont']['cw'];
        $wmax = $fstring->width * 1000 * $this->k;
        $j = count($lines) - 1;
        $k = strlen($lines[$j]);
        if (!isset($linesmap[$j][0])) {
            $linesmap[$j] = array($p, $p, 0);
        }
        $sl = $cw[' '] * $cfty['font-size'];
        $x = $a = $linesmap[$j][2];
        if ($k > 0) {
            $x += $sl;
            $lines[$j] .= ' ';
            $linesmap[$j][2] += $sl;
        }
        $u = $p;
        $t = '';
        $l = $p + $q;
        $ftmp = '';
        for ($i = $p; $i < $l; $i++) {
            if ($ftmp != $ffs) {
                $cfty = $fstring->get_current_style($i);
                $ffs = $cfty['font-family'] . $cfty['style'];
                if (!isset($fstring->used_fonts[$ffs])) {
                    $fstring->used_fonts[$ffs] = &$this->FontData($cfty['font-family'], $cfty['style'], $cfty['font-size']);
                }
                $cw = &$fstring->used_fonts[$ffs]['CurrentFont']['cw'];
                $ftmp = $ffs;
            }
            $x += $cw[$parced_str[$i]] * $cfty['font-size'];
            if ($x > $wmax) {
                if ($a > 0) {
                    $t = substr($parced_str, $p, $i - $p);
                    $lines[$j] = substr($lines[$j], 0, $k);
                    $linesmap[$j][1] = $p - 1;
                    $linesmap[$j][2] = $a;
                    $x -= ($a + $sl);
                    $a = 0;
                    $u = $p;
                } else {
                    $x = $cw[$parced_str[$i]] * $cfty['font-size'];
                    $t = '';
                    $u = $i;
                }
                $j++;
                $lines[$j] = $t;
                $linesmap[$j] = array();
                $linesmap[$j][0] = $u;
                $linesmap[$j][2] = 0;
            }
            $lines[$j] .= $parced_str[$i];
            $linesmap[$j][1] = $i;
            $linesmap[$j][2] = $x;
        }
        return;
    }

    public function &extMultiCell($font_family, $font_style, $font_size, $font_color, $w, $txt)
    {
        $result = array();
        if ($w == 0) {
            return $result;
        }
        $this->current_font = array('font-family' => $font_family, 'style' => $font_style, 'font-size' => $font_size, 'font-color' => $font_color);
        $fstring = new formatedString($txt, $w, $this->current_font);
        $word = '';
        $p = 0;
        $i = 0;
        $n = strlen($fstring->parced_str);
        while ($i < $n) {
            $word .= $fstring->parced_str[$i];
            if ($fstring->parced_str[$i] == "\n" || $fstring->parced_str[$i] == ' ' || $i == $n - 1) {
                $word = trim($word);
                $this->setLines($fstring, $p, strlen($word));
                $p = $i + 1;
                $word = '';
                if ($fstring->parced_str[$i] == "\n" && $i < $n - 1) {
                    $z = 0;
                    $j = count($fstring->lines);
                    $fstring->lines[$j] = '';
                    $fstring->linesmap[$j] = array();
                }
            }
            $i++;
        }
        if ($n == 0) {
            return $result;
        }
        $n = count($fstring->lines);
        for ($i = 0; $i < $n; $i++) {
            $result[$i] = $fstring->break_by_style($i);
        }
        return $result;
    }

    private function GetMixStringWidth($line)
    {
        $w = 0;
        foreach ($line['chunks'] as $i => $chunk) {
            $t = 0;
            $cf = &$this->FontData($line['style'][$i]['font-family'], $line['style'][$i]['style'], $line['style'][$i]['font-size']);
            $cw = &$cf['CurrentFont']['cw'];
            $s = implode('', explode(' ', $chunk));
            $l = strlen($s);
            for ($j = 0; $j < $l; $j++) {
                $t += $cw[$s[$j]];
            }
            $w += $t * $line['style'][$i]['font-size'];
        }
        return $w;
    }

    public function CellBlock($w, $lh, &$lines, $align = 'J')
    {
        if ($w == 0) {
            return;
        }
        $ctmp = '';
        $ftmp = '';
        foreach ($lines as $i => $line) {
            $k = $this->k;
            if ($this->y + $lh * $line['height'] > $this->PageBreakTrigger) {
                break;
            }
            $dx = 0;
            $dw = 0;
            if ($line['width'] != 0) {
                if ($align == 'R') {
                    $dx = $w - $line['width'] / ($this->k * 1000);
                } elseif ($align == 'C') {
                    $dx = ($w - $line['width'] / ($this->k * 1000)) / 2;
                }
                if ($align == 'J') {
                    $tmp = explode(' ', implode('', $line['chunks']));
                    $ns = count($tmp);
                    if ($ns > 1) {
                        $sx = implode('', $tmp);
                        $delta = $this->GetMixStringWidth($line) / ($this->k * 1000);
                        $dw = ($w - $delta) * (1 / ($ns - 1));
                    }
                }
            }
            $xx = $this->x + $dx;
            foreach ($line['chunks'] as $tj => $txt) {
                $this->resetFont($line['style'][$tj]['font-family'], $line['style'][$tj]['style'], $line['style'][$tj]['font-size']);
                $this->resetColor($line['style'][$tj]['font-color'], 'T');
                $y = $this->y + 0.5 * $lh * $line['height'] + 0.3 * $line['height'] / $this->k;
                if ($dw) {
                    $tmp = explode(' ', $txt);
                    foreach ($tmp as $e => $tt) {
                        if ($e > 0) {
                            $xx += $dw;
                            if ($tt == '') {
                                continue;
                            }
                        }
                        $this->Text($xx, $y, $tt);
                        if ($line['style'][$tj]['href']) {
                            $yr = $this->y + 0.5 * ($lh * $line['height'] - $line['height'] / $this->k);
                            $this->Link($xx, $yr, $this->GetStringWidth($txt), $line['height'] / $this->k, $line['style'][$tj]['href']);
                        }
                        $xx += $this->GetStringWidth($tt);
                    }
                } else {
                    $this->Text($xx, $y, $txt);
                    if ($line['style'][$tj]['href']) {
                        $yr = $this->y + 0.5 * ($lh * $line['height'] - $line['height'] / $this->k);
                        $this->Link($xx, $yr, $this->GetStringWidth($txt), $line['height'] / $this->k, $line['style'][$tj]['href']);
                    }
                    $xx += $this->GetStringWidth($txt);
                }
            }
            unset($lines[$i]);
            $this->y += $lh * $line['height'];
        }
    }





    protected $widths;
    protected $aligns;
    protected $colPaddings;

    // function SetWidths($w)
    // {
    //     // Set the array of column widths
    //     $this->widths = $w;
    // }

    function SetWidths($w, $paddingLeft = [])
    {
        $this->widths = $w;

        // If a single value is passed → apply to all
        if (!is_array($paddingLeft)) {
            $this->colPaddings = array_fill(0, count($w), $paddingLeft);
        } else {
            $this->colPaddings = $paddingLeft;
        }
    }

    function SetAligns($a)
    {
        // Set the array of column alignments
        $this->aligns = $a;
    }


    function Row($data)
    {
        // Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $pad = $this->colPaddings[$i] ?? 0;
            $nb = max($nb, $this->NbLines($this->widths[$i] - $pad, $data[$i]));
        }
        $h = 5 * $nb;

        // Issue a page break if needed
        $this->CheckPageBreak($h);

        // Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w   = $this->widths[$i];
            $a   = $this->aligns[$i] ?? 'L';
            $pad = $this->colPaddings[$i] ?? 0;

            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();

            // Optional border
            // $this->Rect($x, $y, $w, $h);

            // Apply left padding
            $this->SetXY($x + $pad, $y);

            // Print the text (width reduced by padding)
            $this->MultiCell($w - $pad, 4, $data[$i], 0, $a);

            // Move cursor to the right of the cell
            $this->SetXY($x + $w, $y);
        }

        // Go to the next line
        $this->Ln($h + 2);
    }


    // function Row($data)
    // {
    //     // Calculate the height of the row
    //     $nb = 0;
    //     for ($i = 0; $i < count($data); $i++)
    //         $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    //     $h = 5 * $nb;
    //     // Issue a page break first if needed
    //     $this->CheckPageBreak($h);
    //     // Draw the cells of the row
    //     for ($i = 0; $i < count($data); $i++) {
    //         $w = $this->widths[$i];
    //         $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
    //         // Save the current position
    //         $x = $this->GetX();
    //         $y = $this->GetY();
    //         // Draw the border
    //         // $this->Rect($x,$y,$w,$h);
    //         // Print the text
    //         $this->MultiCell($w, 6, $data[$i], 0, $a);
    //         // Put the position to the right of the cell
    //         $this->SetXY($x + $w, $y);
    //     }
    //     // Go to the next line
    //     $this->Ln($h + 2);
    // }

    function CheckPageBreak($h)
    {
        // If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        // Compute the number of lines a MultiCell of width w will take
        if (!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}
