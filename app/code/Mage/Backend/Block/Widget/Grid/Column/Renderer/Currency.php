<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend grid item renderer currency
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Backend_Block_Widget_Grid_Column_Renderer_Currency
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 100;

    /**
     * Currency objects cache
     */
    protected static $_currencies = array();

    /**
     * Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Locale
     *
     * @var Mage_Core_Model_Locale
     */
    protected $_locale;

    /**
     * @var Mage_Directory_Model_Currency_DefaultLocator
     */
    protected $_currencyLocator;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Model_Locale $locale
     * @param Mage_Directory_Model_Currency_DefaultLocator $currencyLocator
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_App $app,
        Mage_Core_Model_Locale $locale,
        Mage_Directory_Model_Currency_DefaultLocator $currencyLocator,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_app = $app;
        $this->_locale = $locale;
        $this->_currencyLocator = $currencyLocator;
    }


    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = (string)$row->getData($this->getColumn()->getIndex())) {
            $currency_code = $this->_getCurrencyCode($row);
            $data = floatval($data) * $this->_getRate($row);
            $sign = (bool)(int)$this->getColumn()->getShowNumberSign() && ($data > 0) ? '+' : '';
            $data = sprintf("%f", $data);
            $data = $this->_locale->currency($currency_code)->toCurrency($data);
            return $sign . $data;
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * Returns currency code, false on error
     *
     * @param $row
     * @return string
     */
    protected function _getCurrencyCode($row)
    {
        if ($code = $this->getColumn()->getCurrencyCode()) {
            return $code;
        }
        if ($code = $row->getData($this->getColumn()->getCurrency())) {
            return $code;
        }

        return $this->_currencyLocator->getDefaultCurrency($this->_request);
    }

    /**
     * Get rate for current row, 1 by default
     *
     * @param $row
     * @return float|int
     */
    protected function _getRate($row)
    {
        if ($rate = $this->getColumn()->getRate()) {
            return floatval($rate);
        }
        if ($rate = $row->getData($this->getColumn()->getRateField())) {
            return floatval($rate);
        }
        return $this->_app->getStore()->getBaseCurrency()->getRate($this->_getCurrencyCode($row));
    }

    /**
     * Returns HTML for CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return parent::renderCss() . ' a-right';
    }
}
