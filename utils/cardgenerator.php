<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/user.php';
require_once 'qr.php';

class CardGenerator {
    const HEIGHT = 1920;
    const WIDTH = self::HEIGHT*(54/86);

    const CARDFONT = "/usr/share/fonts/truetype/msttcorefonts/arial.ttf";

    public static function generateCard(User $user) {
        $event = EventHandler::getCurrentEvent();

    	$image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
    	//Colors are allocated like this apparently
    	$white = imagecolorallocate($image, 255, 255, 255);
    	$black = imagecolorallocate($image, 0, 0, 0);
    	imagefill($image, 0, 0, $white);

        //Fetch and blit infected logo, after negating it
        $logo = imagecreatefromjpeg(Settings::getValue("api_path") . "/content/static/logo_infected_600x211.jpg");

        self::blitImage($image, $logo, 0.2, 0.050, 0.6);

        //Fetch and blit avatar
        $avatar = $user->getAvatar();
        $avatarImage = imagecreatefromjpeg(Settings::getValue("api_path") . $avatar->getHd());

        self::blitImage($image, $avatarImage, 0.1, 0.15+0.050, 0.8);

        $printedHeight = self::blitText($image, $user->getFullName(), self::HEIGHT/25, $black, self::xToNormalized(0.05), self::yToNormalized(0.650), 20);

        $specialRole = "Medlem";

        if($user->hasCustomTitle()) {
            $specialRole = $user->getCustomTitle();
        } else {
            if ($user->hasSpecialRole()) {
                $specialRole = $user->getRole();
            } else if($user->isTeamMember()) {
                $specialRole = 'Medlem av ' . ucwords($user->getGroup()->getName()) . ':' . ucwords($user->getTeam()->getName());
            }

            if($user->getGroup()->getId() == 50) {
                if($user->isGroupLeader()) {
                    $specialRole = 'Arrangementsansvarlig';
                }
                else if( $user->isTeamLeader()) {
                    $specialRole =  'Ansvarlig ' . ucwords($user->getTeam()->getName());
                }
                else if($user->isTeamMember()) {
                    $specialRole =  ucwords($user->getTeam()->getName());
                }
            } else if($user->getGroup()->getId() == 52 ) {
                if($user->isGroupLeader()) {
                    $specialRole = "Sikkerhetsansvarlig";
                }
            }
        }
        self::blitText($image, $specialRole, self::HEIGHT/35, $black, self::xToNormalized(0.05), self::yToNormalized(0.60+0.05) + $printedHeight, 28);
        //self::blitText($image, $user->getAge(), self::HEIGHT/50, $black, self::xToNormalized(0.05), self::yToNormalized(0.75), 35);

        $validStr = $event->getSeason() . " " . date("Y", $event->getStartTime());

        self::blitText($image, $validStr , self::HEIGHT/35, $black, self::xToNormalized(0.05), self::yToNormalized(0.85), 35);


        //Draw QR code

        $qrcodepath =  Settings::getValue("api_path") . "/content/qrcache/" . QR::getCode('infected-user:' . $user->getId());

        $qrImage = imagecreatefrompng($qrcodepath);
        self::blitImage($image, $qrImage, 0.75, 0.75, 0.20, 0.15, 0.15, 0.7, 0.7);

        //Draw crew


        $group = $user->getGroup();
        $team = $user->getTeam();

        $crewColor = imagecolorallocate($image, 0x00, 0x00, 0x00);

        if($group != null) {
            switch ($group->getId()) {
                case 49: // Game
                    $crewColor = imagecolorallocate($image, 0x00, 0x96, 0x88); //Treal
                    break;
                case 50: // Core
                    $crewColor = imagecolorallocate($image, 0xCD, 0xDC, 0x39); //Lime
                    break;
                case 51: // Kafe og Backstage
                    $crewColor = imagecolorallocate($image, 0x21, 0x96, 0xF3); //Cyan
                    break;
                case 52: // Security
                    $crewColor = imagecolorallocate($image, 0x00, 0x00, 0x00); //Black
                    break;
                case 53: // Tech
                    $crewColor = imagecolorallocate($image, 0xFF, 0x57, 0x22); // Deep Orange
                    break;
                case 54: // Event
                    $crewColor = imagecolorallocate($image, 0xE9, 0x1E, 0x63); //Pink
                    break;
            }
        }
        imagefilledrectangle($image,
            self::xToNormalized(0),
            self::yToNormalized(0.9),
            self::xToNormalized(1),
            self::yToNormalized(1),
            $crewColor);

        $crewStr = $group->getName();

        $crewStr = ucwords($crewStr);

        $dimensions = imagettfbbox(self::HEIGHT/35, 0, self::CARDFONT, $crewStr);
        self::blitTextOneline($image, $crewStr, self::HEIGHT/35, $white, self::xToNormalized(0.5)-$dimensions[2]/2, self::yToNormalized(0.95)+20);

        return $image;
    }

    private static function blitImage($image, $source, $nx, $ny, $nw, $sx_n = 0, $sy_n = 0, $sw_n = 1, $sh_n = 1) {
        imagecopyresized($image, $source,
            self::xToNormalized($nx),
            self::yToNormalized($ny),
            imagesx($source)*$sx_n,
            imagesy($source)*$sy_n,
            self::xToNormalized($nw),
            (imagesy($source)/imagesx($source))*self::xToNormalized($nw),
            imagesx($source)*$sw_n,
            imagesy($source)*$sh_n);
    }

    private static function blitText($image, $text, $fontsize, $color, $x, $y, $wrap_limit = 100) {
        $dimensions = imagettfbbox($fontsize, 0, self::CARDFONT, $text);
        //print_r($dimensions);
        $writeText = explode("\n", wordwrap($text, $wrap_limit));
        $delta_y = 0;
        foreach($writeText as $line) {
            imagettftext($image, $fontsize, 0, $x, $y + $delta_y, $color, self::CARDFONT, $line);
            $delta_y =  $delta_y +$fontsize /*$dimensions[3]*5*/;
        }
        return  $delta_y;
    }

    private static function blitTextOneline($image, $text, $fontsize, $color, $x, $y) {
        imagettftext($image, $fontsize, 0, $x, $y, $color, self::CARDFONT, $text);
    }

    private static function xToNormalized($x) {
    	return $x*self::WIDTH;
    }
    private static function yToNormalized($y) {
    	return $y*self::HEIGHT;
    }
}

?>
