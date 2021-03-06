<?php

class Bystritsky_Action_Model_Action extends Mage_Core_Model_Abstract
{
    const AWAITED = 0;
    const ACTING = 1;
    const COMPLITED = 2;

    private $timeFields = ['create_datetime', 'start_datetime', 'end_datetime'];

    static function getOptionArray()
    {
        $options = [
            'Awaited',
            'Acting',
            'Complited'];
        return $options;
    }

    protected function _construct()
    {
        $this->_init('bystritsky_action/action');
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field = null)
    {
        $resource = $this->_getResource();
        if (!$resource) {
            throw new UnexpectedValueException('Resource instance is not available');
        }

        parent::load($id, $field);

        return $this;
    }


    public function _afterLoad()
    {
        // from GMT to local time
        foreach ($this->timeFields as $field) {
            if ($raw = $this->getData($field)) {
                $time = Mage::getModel('core/date')->timestamp(strtotime($raw));
                //$dateTime = Mage::helper('core')->formatDate($raw, true);
                $dateTime = date("Y-m-d H:i:s", $time);
                $this->setData($field, date($dateTime));
            }
        }
        return parent::_afterLoad();
    }


    public function getImageUrl()
    {
        $helper = Mage::helper('bystritsky_action');
        if ($filename = $this->getImage()) {
            return $helper->getImageUrl($helper->getFilename($filename));
        }
        return null;
    }

    public function save()
    {
        if (is_array($tmp = $this->getImage())) {
            $this->setImage($tmp['value']);
        }
        return parent::save();
    }

    protected function _beforeSave()
    {
        // time to GMT
        foreach ($this->timeFields as $field) {
            if ($raw = $this->getData($field)) {
                //$locale = Mage::app()->getLocale()->getLocaleCode();
                $time = Mage::getModel('core/date')->gmtTimestamp(strtotime($raw));
                //$dateTime = DateTime::createFromFormat($locale)
                $this->setData($field, $time);
            }
        }

        // cron job
        $currentDatetime = Mage::getModel('core/date')->gmtTimestamp();
        $startDatetime = $this->getStartDatetime();
        $endDatetime = $this->getEndDatetime();

        if ($startDatetime > $currentDatetime) {
            $this->setStatus(self::AWAITED);
        } elseif ($endDatetime && $currentDatetime > $endDatetime) {
            $this->setStatus(self::COMPLITED);
        } else {
            $this->setStatus(self::ACTING);
        }

        return parent::_beforeSave();
    }

    public function updateStatus()
    {
        /**
         * @var $collection Bystritsky_Action_Model_Action[]
         */
        print("Cron start\n");
        $collection = Mage::getModel('bystritsky_action/action')->getCollection();
        foreach ($collection as $action) {
            $action->_afterLoad();
            $action->save();
        }
        print("Cron finish\n");
    }

    public function getProductsCollection($params = null)
    {
        $products = Mage::getModel('bystritsky_action/dependency')->getCollection();
        $products->addFieldToFilter('action_id', $this->getId());
        return $products;
    }

    public function setData($key, $value = null)
    {
        parent::setData($key, $value);
        if (is_array($tmp = $this->getImage())) {
            $this->setImage($tmp['value']);
        }
        return $this;
    }

    protected function _beforeDelete()
    {
        $helper = Mage::helper('bystritsky_action');
        $this->load($this->getId());
        $helper->removeImage($this->getImage());
        return parent::_beforeDelete();
    }

    public function getProductsRelatedToAction($params = null, $fields = null)
    {
        return $this->getResource()->getProductsRelatedToAction($this->getId(), $params, $fields);
    }

}