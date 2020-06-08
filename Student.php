<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class StudentCore extends ObjectModel
{
    /** @var string Name */
    public $name;
    public $birthday;
    public $status;
    public $avg_rating;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'student',
        'primary' => 'id_student',
        'multilang' => true,
        'fields' => array(
            'birthday' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'status' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'avg_rating' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],

            /* Lang fields */
            'name' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
        ),
    );

    public static function getStudents($id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('student', 's');
        $sql->innerJoin('student_lang', 'sl', 's.id_student = sl.id_student AND sl.id_lang = '.(int)$id_lang);

        return Db::getInstance()->executeS($sql);
    }

    public static function getBestStudent($id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = 'SELECT * FROM ' . _DB_PREFIX_.'student s
        LEFT JOIN `' . _DB_PREFIX_ . 'student_lang` sl
			ON (s.`id_student` = sl.`id_student`
			AND `id_lang` = ' . (int) $id_lang . ')
        WHERE s.`avg_rating` = (
            SELECT MAX(s1.`avg_rating`) as max_rating
            FROM ' . _DB_PREFIX_.'student s1
        )';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getBestRating()
    {
        $sql = 'SELECT MAX(s1.`avg_rating`)
        FROM ' . _DB_PREFIX_.'student';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
