<?php
/*
 -------------------------------------------------------------------------
 MyDashboard plugin for GLPI
 Copyright (C) 2015 by the MyDashboard Development Team.
 -------------------------------------------------------------------------

 LICENSE

 This file is part of MyDashboard.

 MyDashboard is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 MyDashboard is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with MyDashboard. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------  
 */

/**
 * Class PluginMydashboardAlert
 */
class PluginMydashboardAlert extends CommonDBTM
{

   /**
    * @param CommonGLPI $item
    * @param int $withtemplate
    * @return string|translated
    */
   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
   {
      if ($item->getType() == 'Reminder') {
         return _n('Alert', 'Alerts', 2, 'mydashboard');
      }
      return '';
   }

   /**
    * @param CommonGLPI $item
    * @param int $tabnum
    * @param int $withtemplate
    * @return bool
    */
   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
   {
      $alert = new self();
      if ($item->getType() == 'Reminder') {
         $alert->showForm($item);
      }
      return true;
   }

   /**
    * @return array
    */
   function getWidgetsForItem()
   {
      return array(
         $this->getType() => _n('Alert', 'Alerts', 2, 'mydashboard')
      );
   }

   /**
    * @param $widgetId
    * @return PluginMydashboardHtml
    */
   function getWidgetContentForItem($widgetId)
   {
      switch ($widgetId) {
         case $this->getType() :
            $widget = new PluginMydashboardHtml();
            $widget->setWidgetHtmlContent($this->getList());
            $widget->setWidgetTitle(_n('Alert', 'Alerts', 2, 'mydashboard'));
            $widget->toggleWidgetRefresh();
            //$widget->toggleWidgetMaximize();
            return $widget;
            break;
      }
   }

   /**
    * @param int $public
    * @return string
    */
   function getList($public = 0)
   {
      global $DB;

      $now = date('Y-m-d H:i:s');

      $restrict_user = '1';
      // Only personal on central so do not keep it
//      if ($_SESSION['glpiactiveprofile']['interface'] == 'central') {
//         $restrict_user = "`glpi_reminders`.`users_id` <> '".Session::getLoginUserID()."'";
//      }

      $restrict_visibility = "AND (`glpi_reminders`.`begin_view_date` IS NULL
                                    OR `glpi_reminders`.`begin_view_date` < '$now')
                              AND (`glpi_reminders`.`end_view_date` IS NULL
                                   OR `glpi_reminders`.`end_view_date` > '$now') ";

      $query = "SELECT `glpi_reminders`.`id`,
                       `glpi_reminders`.`name`,
                       `glpi_reminders`.`text`,
                       `glpi_reminders`.`date`,
                       `glpi_reminders`.`begin_view_date`,
                       `glpi_reminders`.`end_view_date`,
                       `" . $this->getTable() . "`.`impact`,
                       `" . $this->getTable() . "`.`is_public`
                   FROM `glpi_reminders` "
         . Reminder::addVisibilityJoins()
         . "LEFT JOIN `" . $this->getTable() . "`"
         . "ON `glpi_reminders`.`id` = `" . $this->getTable() . "`.`reminders_id`"
         . "WHERE $restrict_user
                         $restrict_visibility ";

      if ($public == 0) {
         $query .= "AND " . Reminder::addVisibilityRestrict() . "";
      } else {
         $query .= "AND `" . $this->getTable() . "`.`is_public`";
      }
      $query .= "AND `" . $this->getTable() . "`.`impact` IS NOT NULL   
                   ORDER BY `glpi_reminders`.`name`";

      $cloud = array();
      $cloudy = array();
      $storm = array();
      $wl = "";
      $result = $DB->query($query);
      $nb = $DB->numrows($result);

      if ($nb) {
         while ($row = $DB->fetch_array($result)) {

            if ($nb < 2 && $row['impact'] < 3) {
               $cloudy[] = $row;
            }
            if ($row['impact'] <= 3) {
               $cloud[] = $row;
            } else {
               $storm[] = $row;
            }
         }

         if (!empty($storm)) {
            //display storm
            $wl .= $this->displayContent('storm', array_merge($storm, $cloud), $public);
         } elseif (!empty($cloudy)) {
            //display cloudy
            $wl .= $this->displayContent('cloudy', $cloudy, $public);
         } elseif (!empty($cloud)) {
            //display cloud
            $wl .= $this->displayContent('cloud', $cloud, $public);
         } else {
            //display sun
            $wl .= $this->displayContent('sun');
         }
      }
      if (!$nb && $public == 0) {
         $wl .= $this->displayContent('sun');
      }


//      foreach($datas as $data){
//         $wl .= "<div class='bubble' style='display:inline; background-color:".$status[$data['type']]."'>".$data['title']."</div>";
//      }
      return $wl;
   }

   /**
    * @param $type
    * @param array $list
    * @param int $public
    * @return string
    */
   private function displayContent($type, $list = array(), $public = 0)
   {
      global $CFG_GLPI;

      $div = $this->getCSS();
      $div .= "<div class='weather_block'>";
      $div .= "<div class='weather_title center'><h3>" . __("Monitoring", "mydashboard") . "</h3></div>";
      $div .= "<div class='weather_img center'><img src='" . $CFG_GLPI['root_doc'] . "/plugins/mydashboard/pics/{$type}.png' width='85%'/></div>";
      $div .= "<div class='weather_msg'>"
         . $this->getMessage($list, $public)
         . "</div>";
      $div .= "</div>";
      return $div;
   }

   /**
    * @param $list
    * @param $public
    * @return string
    */
   private function getMessage($list, $public)
   {
      $l = "";

      if (!empty($list)) {
         foreach ($list as $listitem) {

            $class = (Html::convDate(date("Y-m-d")) == Html::convDate($listitem['date'])) ? 'alert_new' : '';
            $class .= ' alert_impact' . $listitem['impact'];
            $classfont = ' alert_fontimpact' . $listitem['impact'];
            $rand = mt_rand();
            $name = (Session::haveRight("reminder_public", READ)) ?
               "<a  href='" . Reminder::getFormURL() . "?id=" . $listitem['id'] . "'>" . $listitem['name'] . "</a>"
               : $listitem['name'];

            $l .= "<div id='alert$rand' class='alert_alert'>";
            $l .= "<span class='alert_impact $class'></span>";
            if (isset($listitem['begin_view_date'])
               && isset($listitem['end_view_date'])
            ) {
               $l .= "<span class='alert_date'>" . Html::convDateTime($listitem['begin_view_date']) . " - " . Html::convDateTime($listitem['end_view_date']) . "</span><br>";
            }


            $l .= "<span class='$classfont center'>" . $name . "</span>";

            //if ($public == 0) {
            $l .= Html::showToolTip(
               Html::resume_text(html_entity_decode($listitem['text']), 80),
               array('display' => false,
                  'applyto' => 'alert' . $rand)
            );
            //}
            $l .= "</div>";
         }
      } else {
         $l .= "<div>" . __("No problem", "mydashboard") . "</div>";
      }
      $l .= "<br>";
      return $l;
   }

   /**
    * @param Reminder $item
    */
   private function showForm(Reminder $item)
   {
      $reminders_id = $item->getID();

      $this->getFromDBByQuery("WHERE `reminders_id` = '" . $reminders_id . "'");

      if (isset($this->fields['id'])) {
         $id = $this->fields['id'];
         $impact = $this->fields['impact'];
         $is_public = $this->fields['is_public'];
      } else {
         $id = -1;
         $impact = 0;
         $is_public = 0;
      }

      $impacts = array();
      $impacts[0] = __("No impact", "mydashboard");
      for ($i = 1; $i <= 5; $i++) {
         $impacts[$i] = CommonITILObject::getImpactName($i);
      }
      echo "<form action='" . $this->getFormURL() . "' method='post' >";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='2'>" . _n('Alert', 'Alerts', 2, 'mydashboard') . "</th></tr>";
      echo "<tr class='tab_bg_2'><td>" . __("Alert level", "mydashboard") . "</td><td>";
      Dropdown::showFromArray('impact', $impacts, array(
            'value' => $impact
         )
      );
      echo "</td>";
      echo "<tr class='tab_bg_2'><td>" . __("Public") . "</td><td>";
      Dropdown::showYesNo('is_public', $is_public);

      echo "</td>";
      echo "<tr class='tab_bg_1 center'><td colspan='2'>";
      echo "<input type='submit' name='update' value=\"" . _sx('button', 'Save') . "\" class='submit'>";
      echo "<input type='hidden' name='id' value=" . $id . ">";
      echo "<input type='hidden' name='reminders_id' value=" . $reminders_id . ">";
      echo "</td></tr></table>";
      Html::closeForm();
   }

   /**
    * @return string
    */
   private function getCSS()
   {
      $css = "<style  type='text/css' media='screen'>
               #display-login {
                  width: 100%;
                  /*background-color: #006573;*/
                  text-align:center;
               }
              .alert_alert {
                  /*margin: 0 30%;*/
                  
                  margin: 0 auto;
                  /*border:1px solid #DDD;*/
                  color:#000;
              }
              .alert_date {
                  text-align:center;
              }
              //.alert_alert:hover {
              //    background-color: #EEE;
              //}

              .alert_impact {
                  width: 14px;
                  height: 14px;
                  border-radius:7px;
                  display: inline-block;
                  float:center;
                  margin:1px 5px;
              }

              .alert_impact1 {
                  background-color: #DFEC4B;
              }
              .alert_fontimpact1 {
                  color: #DFEC4B;
              }
              .alert_impact2 {
                  background-color: #EED655;
              }
              .alert_fontimpact2 {
                  color: #EED655;
              }
              .alert_impact3 {
                  background-color: #DBBD5D;
              }
              .alert_fontimpact3 {
                  color: #DBBD5D;
              }
              .alert_impact4 {
                  background-color: #CE9C5C;
              }
              .alert_fontimpact4 {
                  color: #CE9C5C;
              }
              .alert_impact5 {
                  background-color: #B55;
                  -webkit-animation: blink 0.5s linear infinite;
                  -moz-animation: blink 0.5s linear infinite;
                  animation: blink 0.5s linear infinite;
              }
              .alert_fontimpact5 {
                  color: #B55;
              }

              @keyframes blink {  
                  0% { opacity:0 }
                  50% { opacity:1 }
                  100% { opacity:0 }
              }
              @-webkit-keyframes blink {
                  0% { opacity:0 }
                  50% { opacity:1 }
                  100% { opacity:0 }
              }
               .weather_block {
                  text-align: center;
                  margin:0 auto;
                  color:#000;
                  font-size: 12px;
                  border-radius: 5px 10px 0 5px;
                  border-color: #CCC;
                  border-style: dashed;
                  background-color: #FFF;
                  width: 80%;
               }
              .weather_img {
                  /*background-color: deepskyblue;*/
                  width: 128px;
                  margin: 20px auto 20px auto;
                  border-radius: 5px;
                  /*box-shadow: deepskyblue 0px 0px 10px 10px;*/
              }

              .weather_msg {
                  text-align: center;
              }
              </style>";
      return $css;
   }
}
