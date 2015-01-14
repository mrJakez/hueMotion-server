<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{









		$colorutils = new colorutils();
		$colors = $colorutils->mixcolors('#0000FF', '#FF0000', 5);

		$bri = 200;

//		            case self::$TYPE_MOTION:

//
//			$xy = hue::convertHexToXY($color);
//
//
//			$config = array("bri" => $bri, "xy" => $xy);
//			hue::setLampConfiguration("1", $config);
//
//			echo "<div style='background-color: $color'>$key ".print_r($xy, true)."</div>";
//			sleep(1);
////			usleep(500000);
//		}



		$red = hue::convertHexToXY('#FF0000');
		$blue = hue::convertHexToXY('#0000FF');


		$sleep = 500000;
		hue::setLampConfiguration("1", array("bri" => $bri, "xy" => hue::convertHexToXY('#FF0000')));
		usleep($sleep);
//
//		hue::setLampConfiguration("1", array("bri" => $bri, "xy" => hue::convertHexToXY('#0000FF'), "transitiontime" => 40));
//		usleep($sleep);

//		hue::setLampConfiguration("1", array("bri" => $bri, "xy" => hue::convertHexToXY('0000FF')));
//		usleep($sleep);
//
//		hue::setLampConfiguration("1", array("bri" => $bri, "xy" => hue::convertHexToXY('FF0000')));
//		usleep($sleep);
//
//		hue::setLampConfiguration("1", array("bri" => $bri, "xy" => hue::convertHexToXY('00FF00')));
//		usleep($sleep);
//
//		hue::setLampConfiguration("1", array("bri" => $bri, "xy" => hue::convertHexToXY('#0000FF')));
//		usleep($sleep);









//		/** @var $motion Model_Motion */
//		$motion = ORM::factory('Motion');
//		$motion->setLamp(1);
//		$motion->save();

		$this->response->body('YEY!');
	}
}