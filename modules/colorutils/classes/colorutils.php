<?php


class colorutils
{
    /**
     * Mixes two colors in a given amount of steps
     * @param $color1 start color
     * @param $color2 destination color
     * @param $steps how many color steps you want
     * @return array - hexvalue strings of colors
     */
    function mixcolors($color1, $color2, $steps) {
        $rgb1 = $this->hex2rgb($color1);
        $rgb2 = $this->hex2rgb($color2);

        $out = array();
        for($i=0; $i<$steps; $i++) {

            $red = $rgb1[0] + (($rgb2[0] - $rgb1[0])/$steps)*$i;
            $green = $rgb1[1] + (($rgb2[1] - $rgb1[1])/$steps)*$i;
            $blue =$rgb1[2] + (($rgb2[2] - $rgb1[2])/$steps)*$i;

            $out[] = $this->rgbToHex(array($red, $green, $blue));
        }

        return $out;
    }


    public function hex2rgb($hex) {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }


    public function rgbToHex($rgb) {
        //String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
        $hex = "#";
        $hex.= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex.= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex.= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        return $hex;
    }
}