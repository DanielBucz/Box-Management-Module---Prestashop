<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Boksy extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'boksy';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Daniel Buczkowski';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Dodawanie boksów');
        $this->description = $this->l('Moduł wstawiający boksy w dowolnym miejscu');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '8.0');

    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');
        return parent::install() &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayProductAdditionalInfo') &&
            $this->registerHook('displayCMSDisputeInformation') &&
            $this->registerHook('displayHeaderCategory') &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }
    public function getContent()
    {
        $output = null;
        $output = $this->displayForm();
        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'admin_module_link' => $this->context->link->getAdminLink('AdminModules'),
        ));

        if (Tools::isSubmit('submitBoks')) {
            $backgroundPath = $this->uploadBackground();

            $background = $backgroundPath;
            $boks_title = Tools::getValue('title');
            $link_home = Tools::getValue('link-home');
            $link_product = Tools::getValue('link-product');
            $link_cms = Tools::getValue('link-cms');
            $link_category = Tools::getValue('link-category');
            if (!empty($boks_title) || !empty($background)) {
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'boksy`
                (`background`, `title`, `link_home`, `link_product`, `link_cms`, `link_category`) 
                VALUES 
                (\'' . pSQL($background) . '\', \'' . pSQL($boks_title) . '\', \'' . pSQL($link_home) . '\', \'' . pSQL($link_product) . '\', \'' . pSQL($link_cms) . '\', \'' . pSQL($link_category) . '\')';
    
                Db::getInstance()->execute($sql);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            } else {
                $this->displayError('Uzupełnij tytuł lub zdjęcie.');
            }gi

        } elseif (Tools::isSubmit('submitEditBoks')) {
            if (!empty($_FILES['background']['name'])) {
                $backgroundPath = $this->uploadBackground();
            } else {
                $backgroundPath = Tools::getValue('background_previous');
            }

            $background = $backgroundPath;
            $boks_title = Tools::getValue('title');
            $link_home = Tools::getValue('link-home');
            $link_product = Tools::getValue('link-product');
            $link_cms = Tools::getValue('link-cms');
            $link_category = Tools::getValue('link-category');
            $id_box = Tools::getValue('id');
           
            $sql = "UPDATE `" . _DB_PREFIX_ . "boksy`
            SET background = '$background',
                title = '$boks_title',
                link_home = '$link_home',
                link_product = '$link_product',
                link_cms = '$link_cms',
                link_category = '$link_category'
            WHERE id_boksy = '$id_box'";

            Db::getInstance()->execute($sql);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);

        } elseif (Tools::isSubmit('addBox')) {

            $output .= $this->display(__FILE__, 'views/templates/admin/add_box_form.tpl');
        } elseif (Tools::isSubmit('edit_id_box')) {
            $id_box = Tools::getValue('edit_id_box');

            $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'boksy` WHERE `' . _DB_PREFIX_ . 'boksy`.`id_boksy` = ' . $id_box);
            $test = $sql[0]['link_category'];
            $tree = $this->generateCategoryTreeArray();
            $tree_html = $this->generateCategoryHTML($tree, $test);
            $this->context->smarty->assign(array(

                'box' => $sql,
                'tree_html' => $tree_html,
            ));
            $output .= $this->display(__FILE__, 'views/templates/admin/edit_box_form.tpl');
        } elseif (Tools::isSubmit('delete_id_box')) {
            $id_box = Tools::getValue('delete_id_box');

            $delete = 'DELETE FROM `' . _DB_PREFIX_ . 'boksy` WHERE `' . _DB_PREFIX_ . 'boksy`.`id_boksy` = ' . $id_box . '';

            Db::getInstance()->execute($delete);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);

        } else {
            $output .= $this->display(__FILE__, 'views/templates/admin/configure.tpl');
        }

        return $output;
    }


    public function uploadBackground()
    {
        $backgroundPath = Tools::getValue('background');

        if (isset($_FILES['background']) && $_FILES['background']['error'] == 0) {
            $uploadDir = _PS_MODULE_DIR_ . $this->name . '/uploads/';
            $uploadFile = $uploadDir . basename($_FILES['background']['name']);

            if (move_uploaded_file($_FILES['background']['tmp_name'], $uploadFile)) {
                $backgroundPath = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/uploads/' . basename($_FILES['background']['name']);
            }
        }

        return $backgroundPath;
    }
    function getBoxes()
    {
        $boxes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'boksy');

        return $boxes;
    }
    public function generateCategoryTreeArray($parentId = 0, $level = 0)
    {
        $categories = Category::getCategories($GLOBALS['cookie']->id_lang, true, false);

        $categoryArray = array();

        foreach ($categories as $category) {
            if ($category['id_parent'] == $parentId) {

                $categoryInfo = array(
                    'id_category' => $category['id_category'],
                    'name' => $category['name'],
                    'level' => $level,
                );

                $subcategories = $this->generateCategoryTreeArray($category['id_category'], $level + 1);

                if (!empty($subcategories)) {
                    $categoryInfo['subcategories'] = $subcategories;
                }

                $categoryArray[] = $categoryInfo;
            }
        }

        return $categoryArray;
    }

    public function generateCategoryHTML($categories, $edited_box = null)
    {
        $html = '<ul class="category_tree" style="list-style: none">';
        foreach ($categories as $category) {
            $html .= '<li><input type="radio" name="link-category" id="" value="' . $category['id_category'] . '"';
            if ($category['id_category'] == $edited_box) {
                $html .= ' checked="checked">';
            } else {
                $html .= '>';
            }
            $html .= $category['name'];
            if (!empty($category['subcategories'])) {
                $html .= $this->generateCategoryHTML($category['subcategories'], $edited_box);
            }

            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    public function displayForm()
    {
        $defaultBackground = Configuration::get('BOKSY_BACKGROUND');
        $defaultTitle = Configuration::get('BOKSY_TITLE');
        $defaultLinkHome = Configuration::get('BOKSY_LINK_HOME');
        $defaultLinkProduct = Configuration::get('BOKSY_LINK_PRODUCT');
        $defaultLinkCMS = Configuration::get('BOKSY_LINK_CMS');
        $defaultIcon = Configuration::get('BOKSY_ICON');


        $products = Product::getProducts($this->context->language->id, 0, 0, 'id_product', 'ASC');
        $products_array = array();
        foreach ($products as $product) {

            $products_array[] = array(
                'id' => $product['id_product'],
                'name' => $product['name'],
                'type' => 'product'
            );
        }
        $cmsPages = CMS::listCms($this->context->language->id);
        $cmsList = array();

        foreach ($cmsPages as $cms) {
            $cmsList[] = [
                'id' => $cms['id_cms'],
                'name' => $cms['meta_title']
            ];
        }

        $categoryTree = $this->generateCategoryTreeArray();
        $categoryHTML = $this->generateCategoryHTML($categoryTree);
        $boxes = $this->getBoxes();

        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'default_background' => $defaultBackground,
            'default_title' => $defaultTitle,
            'default_link_home' => $defaultLinkHome,
            'default_link_product' => $defaultLinkProduct,
            'default_link_cms' => $defaultLinkCMS,
            'default_icon' => $defaultIcon,
            'products_array' => $products_array,
            'cmsList' => $cmsList,
            'categoryTree' => $categoryHTML,
            'boxes' => $boxes,
        ));
    }

    public function hookDisplayHome($params)
    {
        $boxes = $this->getBoxes();

        $this->context->smarty->assign(array(
            'boxes' => $boxes,
        ));

        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }
    public function hookDisplayProductAdditionalInfo($params)
    {
        $boxes = $this->getBoxes();

        $this->context->smarty->assign(array(
            'boxes' => $boxes,
        ));
        return $this->display(__FILE__, 'views/templates/hook/displayProductBlock.tpl');
    }
    public function hookDisplayCMSDisputeInformation($params)
    {
        $boxes = $this->getBoxes();

        $this->context->smarty->assign(array(
            'boxes' => $boxes,
        ));

        return $this->display(__FILE__, 'views/templates/hook/displayStaticPage.tpl');
    }
    public function hookDisplayHeaderCategory($params)
    {
        $boxes = $this->getBoxes();

        $this->context->smarty->assign(array(
            'boxes' => $boxes,
        ));

        return $this->display(__FILE__, 'views/templates/hook/displayHeaderCategory.tpl');
    }


    public function hookDisplayHeader()
    {

        if ($this->context->controller) {
            $this->context->controller->addCSS($this->_path . 'views/css/boksy.css', 'all');
        }
    }
}
