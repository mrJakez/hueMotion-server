<?php


class hue
{
    static function setLampConfiguration($lamp, $config)
    {
        $request = Request::factory("http://10.10.10.62/api/hueMotionApp/lights/$lamp/state");
        $request->method(Request::PUT);
        $request->headers('Content-Type', 'application/json');
        $request->body(json_encode($config));
        $response = $request->execute();
    }


    static function convertHexToXY($hex)
    {
        $colorutils = new colorutils();
        $rgb = $colorutils->hex2rgb($hex);
        return self::convertRGBToXY($rgb);
    }

    static function convertRGBToXY($rgb)
    {
        $red = $rgb[0];
        $green = $rgb[1];
        $blue = $rgb[2];

        $red = ($red > 0.04045) ? pow(($red + 0.055) / (1.0 + 0.055), 2.4) : ($red / 12.92);
        $green = ($green > 0.04045) ? pow(($green + 0.055) / (1.0 + 0.055), 2.4) : ($green / 12.92);
        $blue = ($blue > 0.04045) ? pow(($blue + 0.055) / (1.0 + 0.055), 2.4) : ($blue / 12.92);

        $X = $red * 0.649926 + $green * 0.103455 + $blue * 0.197109;
        $Y = $red * 0.234327 + $green * 0.743075 + $blue * 0.022598;
        $Z = $red * 0.0000000 + $green * 0.053077 + $blue * 1.035763;


        if ($X + $Y + $Z > 0) {
            $x = $X / ($X + $Y + $Z);
        } else {
            return FALSE;
            $x = $X;
        }

        if ($X + $Y + $Z > 0) {
            $y = $Y / ($X + $Y + $Z);
        } else {
            return FALSE;

            $y = $Y ;
        }

        return array($x, $y);
    }
}