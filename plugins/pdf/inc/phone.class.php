<?php
/**
 * @version $Id: phone.class.php 433 2016-02-25 19:02:58Z yllen $
 -------------------------------------------------------------------------
 LICENSE

 This file is part of PDF plugin for GLPI.

 PDF is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 PDF is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with Reports. If not, see <http://www.gnu.org/licenses/>.

 @package   pdf
 @authors   Nelly Mahu-Lasson, Remi Collet
 @copyright Copyright (c) 2009-2016 PDF plugin team
 @license   AGPL License 3.0 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://forge.glpi-project.org/projects/pdf
 @link      http://www.glpi-project.org/
 @since     2009
 --------------------------------------------------------------------------
*/


class PluginPdfPhone extends PluginPdfCommon {


   static $rightname = "plugin_pdf";


   function __construct(CommonGLPI $obj=NULL) {
      $this->obj = ($obj ? $obj : new Phone());
   }


   function defineAllTabs($options=array()) {

      $onglets = parent::defineAllTabs($options);
      unset($onglets['Item_Problem$1']); // TODO add method to print linked Problems
      unset($onglets['Change_Item$1']); // TODO add method to print linked Changes
      unset($onglets['Item_Devices$1']); // TODO add method to print linked Devices
      return $onglets;
   }


   static function pdfMain(PluginPdfSimplePDF $pdf, Phone $item) {

      PluginPdfCommon::mainTitle($pdf, $item);

      PluginPdfCommon::mainLine($pdf, $item, 'name-status');
      PluginPdfCommon::mainLine($pdf, $item, 'location-type');
      PluginPdfCommon::mainLine($pdf, $item, 'tech-manufacturer');
      PluginPdfCommon::mainLine($pdf, $item, 'group-model');
      PluginPdfCommon::mainLine($pdf, $item, 'contactnum-serial');
      PluginPdfCommon::mainLine($pdf, $item, 'contact-otherserial');
      PluginPdfCommon::mainLine($pdf, $item, 'user-management');


      $pdf->displayLine(
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Group').'</i></b>',
                          Html::clean(Dropdown::getDropdownName('glpi_groups',
                                                                $item->fields['groups_id']))),
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Power supply').'</i></b>',
                          Dropdown::getYesNo($item->fields['phonepowersupplies_id'])));

      $pdf->displayLine(
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Brand').'</i></b>', $item->fields['brand']),
         '<b><i>'.sprintf(__('%1$s: %2$s'), _x('quantity', 'Number of lines').'</i></b>',
                          $item->fields['number_line']));

      $pdf->displayLine(
         '<b><i>'.sprintf(__('%1$s: %2$s'), _n('Firmware', 'Firmwares', 1).'</i></b>',
                          $item->fields['firmware']));

      $opts = array('have_headset' => __('Headset'),
                    'have_hp'      => __('Speaker'));
      foreach ($opts as $key => $val) {
         if (!$item->fields[$key]) {
            unset($opts[$key]);
         }
      }

      $pdf->setColumnsSize(100);
      $pdf->displayLine('<b><i>'.sprintf(__('%1$s: %2$s'), __('Flags').'</i></b>',
                        (count($opts) ? implode(', ',$opts) : __('None'))));

      PluginPdfCommon::mainLine($pdf, $item, 'comment');

      $pdf->displaySpace();
   }


   static function displayTabContentForPDF(PluginPdfSimplePDF $pdf, CommonGLPI $item, $tab) {

      switch ($tab) {
          case 'Computer_Item$1' :
            PluginPdfComputer_Item::pdfForItem($pdf, $item);
            break;

         default :
            return false;
      }
      return true;
   }
}