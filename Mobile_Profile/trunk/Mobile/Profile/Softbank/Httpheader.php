<?php
require_once 'Mobile/Profile/Softbank/Abstract.php';
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Softbank_Httpheader extends Mobile_Profile_Softbank_Abstract
{
    public function collect()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=http';

            $_Model = 'Mobile_Profile_Filter_Softbank_Model';

            $profile = new Diggin_Scraper();
            $profile->process('td[1]', "model => TEXT, $_Model")
                    ->process('td[2]', "device => TEXT")
                    ->process('td[3]', "display => TEXT")
                    ->process('td[4]', "color => TEXT")
                    ->process('td[5]', "sound => TEXT")
                    ->process('td[6]', "smaf => TEXT")
                    ->process('td[7]', "display-info => TEXT")
                    ->process('td[8]', "unique-id => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        foreach ($scraper->profile as $profile) {
            $row = array();

            $row = $profile;

            $display = explode('*', $profile['display']);
            $row['display'] = array(
                'width'  => $display[0],
                'height' => $display[1],
            );

            preg_match('/(G|C)(\d+)/', $profile['color'], $match);
            $row['color'] = array(
                'is_color' => $match[1] === 'C',
                'depth'    => $match[2],
            );

            if ($profile['unique-id'] === '×') {
                $row['unique-id'] = null;
            }

            $result[] = $row;
        }

        return $result;
    }
}
