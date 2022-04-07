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

class AdminAwardImgController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'award_img';
        $this->className = 'AwardImg';
        $this->_select .= ' fvl.value';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON (fvl.`id_feature_value` = a.`id_feature_value`)';
        $this->_where = ' AND fvl.id_lang=1';
        parent::__construct();

        $this->fields_list = [
            'id_award_img' => [
                'title' => $this->l('ID'),
                'align' => 'left',
                'class' => 'fixed-width-xs',
            ],
            'value' => [
                'title' => $this->l('Award Name'),
                'type' => 'text',
                'filter_key' => 'fvl!value',
                'orderby' => true,
            ],
            'image' => array(
                'title' => $this->trans('Image', array(), 'Admin.Global'),
                'align' => 'center',
                'image' => 'award_img/',
                'orderby' => false,
                'search' => false,
            ),
            'active' => [
                'title' => $this->l('Active'),
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'active' => 'status',
                'align' => 'text-center',
                'filter_key' => 'a!active',
                'orderby' => false,
            ],
        ];
        $this->fieldImageSettings = [
            'name' => 'award_image',
            'dir' => 'award_img/',
        ];
        $this->addRowAction('edit');
        $this->addRowAction('');
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            ],
        ];
    }

    // public function initPageHeaderToolbar() {
    //     if (empty($this->display)) {
    //         $this->page_header_toolbar_btn['new_award_img'] = array(
    //             'href' => self::$currentIndex . '&adddealer&token=' . $this->token,
    //             'desc' => $this->l('Add new dealer'),
    //             'icon' => 'process-icon-new',
    //         );
    //     }

    //     parent::initPageHeaderToolbar();
    // }

    public function renderForm() {
        $awards = AwardImg::getUnpairAward($this->object->id_feature_value);
        
        foreach ($awards as $key => $value) {
            $awards_option[] = [
                'id' => $key,
                'name' => $value,
            ];
        }
        
        $this->fields_form = [
            'legend' => [
                'icon' => 'icon-pencil',
                'title' => $this->l('Connect to an image'),
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->l('Award Name'),
                    'name' => 'id_feature_value',
                    'col' => 8,
                    'options' => [
                        'query' => $awards_option,
                        'id' => 'id',
                        'name' => 'name',
                    ]
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Image'),
                    'name' => 'award_image',
                    'id' => 'award_image',
                    'required' => true,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'id' => 'active',
                    'values' => [
                        ['value' => 1],
                        ['value' => 0],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ],
        ];

        return parent::renderForm();
    }

    public function getFieldsValue($obj) {
        if ($this->display == 'add') {
            $this->fields_value['active'] = 1;
        } elseif ($this->display == 'edit') {
            // $brands = DealerList::getEntryByDealer($this->object->id);
            
            // foreach ($brands as $brand) {
            //     $this->fields_value['brands_' . $brand['id_dealer_brand']] = 1;
            // }
        }
        return parent::getFieldsValue($obj);
    }

    protected function afterImageUpload()
    {
        if (file_exists(_PS_TMP_IMG_DIR_ . $this->table . '_mini_' . $this->object->id . '_' . $this->context->shop->id . '.jpg')) {
            unlink(_PS_TMP_IMG_DIR_ . $this->table . '_mini_' . $this->object->id . '_' . $this->context->shop->id . '.jpg');
        }

        return true;
    }
}
