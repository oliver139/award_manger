<?php
/**
 * 2022 Oliver
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the GNU General Public License, version 3 (GPL-3.0).
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 *  @author     Oliver <oliver139.working@gmail.com>
 *  @copyright  2022 Oliver
 *  @license    https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

if (!defined('_PS_VERSION_')) exit;

require_once(dirname(__FILE__) . '/vendor/autoload.php');

class Award_Img_Manager extends Module
{
    private $_html = '';
    private $_postErrors = array();
 
    public function __construct() {
        $this->name                   = 'award_img_manager';
        $this->tab                    = 'front_office_features';
        $this->version                = '1.0';
        $this->author                 = 'Oliver';
        $this->bootstrap              = true;
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName            = $this->l('Award Image Manager');
        $this->description            = $this->l('This module allows you to manage awards\' image');
        $this->confirmUninstall       = $this->l('Are you sure to uninstall this module?');
    }

    public function install(){
        // Configuration::updateValue('GENKI_FPS_ACCOUNT_TYPE', '1');
        
        return parent::install() &&
            $this->createImageDir() &&
            $this->hooksRegistration() &&
            $this->installDB() &&
            $this->addTab();
    }

    public function uninstall() {
        return parent::uninstall() && 
            $this->uninstallDB() &&
            $this->removeTab();
    }

    /**
     * Create custom table for saving all the FPS records
     * 
     * @return bool
     */
    private function installDB() {
        $award_table = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'award_img` (
            `id_award_img` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_feature_value` INT(10) UNSIGNED NOT NULL,
            `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'1\',
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_award_img`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
        
        return Db::getInstance()->execute($award_table);
    }

    /**
     * Drop the custom table, not in use currently
     * 
     * @return bool
     */
    private function uninstallDB() {
        $award_table = 'DROP TABLE `'._DB_PREFIX_.'award_img`';
        
        return Db::getInstance()->execute($award_table);
    }

    /**
     * Register Hooks
     * 
     * @return bool Result
     */
    public function hooksRegistration() {
        $hooks = [
            'actionFeatureValueDelete',
        ];

        return $this->registerHook($hooks);
    }

    private function createImageDir() {
        $res = true;

        if (!file_exists(_PS_IMG_DIR_ . 'award_img/')) {
            $res &= mkdir(_PS_IMG_DIR_ . 'award_img/', 0770);
        }

        return $res;
    }

    private function addTab() {
        $res = true;
        $tabparent = "AdminAwardImgManager";
        $id_parent = Tab::getIdFromClassName($tabparent);
        if(!$id_parent){
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = "AdminAwardImgManager";
            $tab->name = [];
            foreach (Language::getLanguages() as $lang){
                $tab->name[$lang["id_lang"]] = $this->l('Award Image Manager');
            }
            $tab->id_parent = 0;
            $tab->module = $this->name;
            $res &= $tab->add();
            $id_parent = $tab->id;
        }
        $subtabs = [
            [
                'class'=>'AdminAwardImg',
                'name'=>'Wine Award'
            ],
        ];
        foreach($subtabs as $subtab){
            $idtab = Tab::getIdFromClassName($subtab['class']);
            if(!$idtab){
                $tab = new Tab();
                $tab->active = 1;
                $tab->class_name = $subtab['class'];
                $tab->name = array();
                foreach (Language::getLanguages() as $lang){
                    $tab->name[$lang["id_lang"]] = $subtab['name'];
                }
                $tab->id_parent = $id_parent;
                $tab->module = $this->name;
                $res &= $tab->add();
            }
        }
        return $res;
    }

    private function removeTab()
    {
        $id_tabs = ["AdminAwardImg","AdminAwardImgManager"];
        foreach($id_tabs as $id_tab){
            $idtab = Tab::getIdFromClassName($id_tab);
            $tab = new Tab((int)$idtab);
            $parentTabID = $tab->id_parent;
            $tab->delete();
            $tabCount = Tab::getNbTabs((int)$parentTabID);
            if ($tabCount == 0){
                $parentTab = new Tab((int)$parentTabID);
                $parentTab->delete();
            }
        }
        return true;
    }
}