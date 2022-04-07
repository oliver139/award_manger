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

class AwardImg extends ObjectModel
{
    public $id_feature_value;

    /** @var $active Status*/
    public $active;

    /** @var $date_add */
    public $date_add;

    /** @var $date_upd */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'award_img',
        'primary' => 'id_award_img',
        'fields' => [
            'id_feature_value' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'size' => 255],
            'active'    => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add'  => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd'  => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public static function getAllRecord($delete_value = null) {
        $sql = new DbQuery();
        $sql->select('id_award_img, id_feature_value');
        $sql->from(self::$definition['table'], 'ai');
        $result = Db::getInstance()->executeS($sql);

        if (!empty($result)) {
            $record = [];
            foreach ($result as $r) {
                if (isset($delete_value) && $r['id_feature_value'] == $delete_value) {
                    continue;
                }
                $record[$r['id_feature_value']] = $r['id_award_img'];
            }
            return $record;
        } else {
            return false;
        }

    }

    // Not finish
    public static function getUnpairAward($selected_value = null) {
        $record = self::getAllRecord($selected_value);

        $award_fid = Configuration::get('AWARD_IMG_FEATURE_ID');
        $awards = FeatureValue::getFeatureValuesWithLang(1, $award_fid, true);
        
        $award_options = [];
        foreach ($awards as $a) {
            $award_options[$a['id_feature_value']] = $a['value'] . ' - [' . $a['id_feature_value'] . ']';
        }

        if ($record) {
            $award_options = array_diff_key($award_options, $record);
        }

        return $award_options;
    }

    public static function deleteByFeatureValue($id_feature_value) {
        return Db::getInstance()->delete(self::$definition['table'], 'id_feature_value = ' . $id_feature_value);
    }
}
