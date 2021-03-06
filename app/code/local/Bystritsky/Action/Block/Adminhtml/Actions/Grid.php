<?php

class Bystritsky_Action_Block_Adminhtml_Actions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('actionsGrid');
        $this->setVarNameFilter('actions_filter');

    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('bystritsky_action/action')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('bystritsky_action');

        $this->addColumn('id', [
            'header' => $helper->__('ID'),
            'index' => 'id',
            'width' => '20',
            'type' => 'range'
        ]);

        $this->addColumn('name', [
            'header' => $helper->__('Name'),
            'index' => 'name',
            'type' => 'text',
        ]);

        $this->addColumn('short_description', [
            'header' => $helper->__('Short Description'),
            'index' => 'short_description',
            'type' => 'text',
        ]);

        $this->addColumn('is_active', [
            'header' => $helper->__('Active'),
            'index' => 'is_active',
            'type' => 'options',
            'options' => ['no', 'yes'],
            'width' => '25'
        ]);

        $this->addColumn('status', [
            'header' => $helper->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('bystritsky_action/action')->getOptionArray(),
            'width' => '75px'
        ]);

        $this->addColumn('image', [
            'header' => $helper->__('Image'),
            'index' => 'image',
            'type' => 'image',
            'renderer' => 'Bystritsky_Action_Block_Adminhtml_Actions_Grid_Renderer_Image'
        ]);

        $this->addColumn('create_datetime', [
            'header' => $helper->__('Create Datetime'),
            'index' => 'create_datetime',
            'type' => 'datetime',
        ]);

        $this->addColumn('start datetime', [
            'header' => $helper->__('Start Datetime'),
            'index' => 'start_datetime',
            'type' => 'datetime',
        ]);

        $this->addColumn('end datetime', [
            'header' => $helper->__('End Datetime'),
            'index' => 'end_datetime',
            'type' => 'datetime',
        ]);

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('actions');

        $this->getMassactionBlock()->addItem('delete', [
            'label' => $this->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
        ]);
        return $this;
    }

    public function getRowUrl($model)
    {
        return $this->getUrl('*/*/edit', [
            'id' => $model->getId(),
        ]);
    }

}