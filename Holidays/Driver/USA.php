<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors:   Kevin English <kevin@x5dev.com>                           |
// +----------------------------------------------------------------------+
//


/**
 * class that calculates observed U.S. holidays
 *
 * @category    Date
 * @package     Date_Holidays
 * @subpackage  Driver
 * @version     $Id$
 * @author      Kevin English <kevin@x5dev.com>
 */
class Date_Holidays_Driver_USA extends Date_Holidays_Driver 
{
   /**
    * Constructor
    *
    * Use the Date_Holidays::factory() method to construct an object of a certain driver
    *
    * @access   protected
    */
    function Date_Holidays_Driver_USA()
    {
    }
    
   /**
    * Build the internal arrays that contain data about the calculated holidays
    *
    * @access   protected
    * @return   boolean true on success, otherwise a PEAR_ErrorStack object
    * @throws   object PEAR_ErrorStack
    */
    function _buildHolidays()
    {
       /**
        * New Year's Day
        */
        $newYearsDay = $this->_calcNearestWorkDay('01','01');
        $this->_addHoliday('newYearsDay', $newYearsDay, 'New Year\'s Day');
    

        $thirdMondayInJanuaryDate  = &$this->_calcNthMondayInMonth(1,3);
        $this->_addHoliday('mlkDay', $thirdMondayInJanuaryDate, 'Dr. Martin Luther King Jr\'s Birthday');

       /**
        * President's Day
        */
        $thirdMondayInFebruaryDate  = &$this->_calcNthMondayInMonth(2,3);
        $this->_addHoliday('presidentsDay', $thirdMondayInFebruaryDate, 'President\'s Day');
       /**
        * Memorial Day 
        */
        $lastMondayInMayDate = &$this->_calcLastMondayInMonth(5);
        $this->_addHoliday('memorialDay',$lastMondayInMayDate,'Memorial Day');
       /**
        * 4th of July
        */

        $independenceDay = $this->_calcNearestWorkDay('07','04');
        $this->_addHoliday('independenceDay',$independenceDay,'Independence Day');

       /**
        * Labor Day
        */
        $laborDay = $this->_calcNthMondayInMonth(9,1);
        $this->_addHoliday('laborDay',$laborDay,'Labor Day');
       /**
        * Columbus Day
        */
        $columbusDay = $this->_calcNthMondayInMonth(10,2);
        $this->_addHoliday('columbusDay',$columbusDay,'Columbus Day');
       /**
        * Veteran's  Day
        */

        $columbusDay = $this->_calcNthMondayInMonth(11,2);
        $this->_addHoliday('veteransDay',$columbusDay,'Veteran\'s Day');
       /**
        * Thanksgiving  Day
        */

        $tday= $this->_calcNthThursdayInMonth(11,4);
        $this->_addHoliday('thanksgivingDay',$tday,'Thanksgiving Day');
 
       /**
        * Christmas  Day
        */

        $tday= $this->_calcNearestWorkDay('12','25');
        $this->_addHoliday('christmasDay',$tday,'Christmas Day');
        
        return true;
    }

   /**
    * Calculate Nth monday in a month
    * 
    * @access   private
    * @param    int $month      month
    * @param    int $position   position
    * @return   object Date date
    */
    function _calcNthMondayInMonth($month, $position) {
        if ($position  ==1) { 
          $startday='01';
        } elseif ($position==2) {
          $startday='08';
        } elseif ($position==3) {
          $startday='15';
        } elseif ($position==4) {
          $startday='22';
        } elseif ($position==5) {
          $startday='29';
        } 
        $month=sprintf("%02d",$month);

        $date   = &new Date($this->_year . '-' . $month . '-' . $startday);
        while ($date->getDayOfWeek() != 1) {
            $date  = &$date->getNextDay();
        }
        return $date;
    }

   /**
    * Calculate Nth thursday in a month
    * 
    * @access   private
    * @param    int $month      month
    * @param    int $position   position
    * @return   object Date date
    */
    function _calcNthThursdayInMonth($month, $position) {
        if ($position  ==1) {
          $startday='01';
        } elseif ($position==2) {
          $startday='08';
        } elseif ($position==3) {
          $startday='15';
        } elseif ($position==4) {
          $startday='22';
        } elseif ($position==5) {
          $startday='29';
        }
        $month=sprintf("%02d",$month);
                                                                                                                                             
        $date   = &new Date($this->_year . '-' . $month . '-' . $startday);
        while ($date->getDayOfWeek() != 4) {
            $date  = &$date->getNextDay();
        }
        return $date;
    }

   /**
    * Calculate last monday in a month
    * 
    * @access   private
    * @param    int $month  month
    * @return   object Date date
    */
    function _calcLastMondayInMonth($month) {
        $month =sprintf("%02d",$month); 
        $date   = &new Date($this->_year . '-' . $month . '-01');
        $daysInMonth=$date->getDaysInMonth();
        $date   = &new Date($this->_year . '-' . $month . '-' . $daysInMonth );
        while ($date->getDayOfWeek() != 1) {
            $date = &$date->getPrevDay();
        }
       
        return $date;
    }

   /**
    * Calculate nearest workday for a certain day 
    * 
    * @access   private
    * @param    int $month  month
    * @param    int $day    day
    * @return   object Date date
    */
    function _calcNearestWorkDay($month,$day) {
        $month =sprintf("%02d",$month); 
        $day  =sprintf("%02d",$day);       
      $date   = &new Date($this->_year . '-' . $month . '-' . $day); 

      // When one of these holidays falls on a Saturday, the previous day is also a holiday
      // When New Year's Day, Independence Day, or Christmas Day falls on a Sunday, the next day is also a holiday.
      if ($date->getDayOfWeek() == 0 ) { 
        // bump it up one
         $date   = &$date->getNextDay();
      } 
      if ($date->getDayOfWeek() == 6 ) { 
        // push it back one
         $date   = &$date->getPrevDay();
      } 

      return $date; 
    } 

}
?>
