<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2015-12-14 20:26:27
 * @@Modify Date: 2018-07-19 19:32:50
 * @@Function:
 */

namespace Magiccart\Comments\Helper;

// use \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function getConfig($cfg=null)
    {
        return $this->scopeConfig->getValue(
            $cfg,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve translated & formated date
     * @param  string $format
     * @param  string $dateOrTime
     * @return string
     */
    public static function getTranslatedDate($format, $dateOrTime)
    {
        if(!$format){
            
            $now    = new \DateTime();
            $ends   = new \DateTime($dateOrTime);
            $left   = $now->diff($ends);
            $html   = '';
            $year   = $left->format('%y');
            if($year){
                $html .= sprintf(__('%s year(s) ago'), $year); 
            } else {
                $month   = $left->format('%m');
                if($month){
                    $html .= sprintf(__('%s month(s) ago'), $month); 
                } else {
                    $day    = $left->format('%a');
                    if($day){ 
                        $html .= sprintf(__('%s day(s) ago'), $day); 
                    } else {
                        $hour   = $left->format('%h');
                        if($hour){
                            $html .= sprintf(__('%s hour(s) ago'), $hour);
                        } else {
                            $minute = $left->format('%i');
                            if($minute) {
                                $html .= sprintf(__('%s minute(s) ago'), $minute);
                            } else {
                                $sec    = $left->format('%s');
                                $html .= sprintf(__('%s sec(s) ago'), $sec);
                            }
                        }
                    }                    
                }
            }

            return $html;
        }
        $time = is_numeric($dateOrTime) ? $dateOrTime : strtotime($dateOrTime);
        $month = ['F' => '%1', 'M' => '%2'];

        foreach ($month as $from => $to) {
            $format = str_replace($from, $to, $format);
        }

        $date = date($format, $time);

        foreach ($month as $to => $from) {
            $date = str_replace($from, __(date($to, $time)), $date);
        }

        return $date;
    }

}
