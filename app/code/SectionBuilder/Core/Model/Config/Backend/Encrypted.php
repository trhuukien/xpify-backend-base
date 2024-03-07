<?php
namespace SectionBuilder\Core\Model\Config\Backend;

class Encrypted extends \Magento\Config\Model\Config\Backend\Encrypted
{
    /**
     * Skip encrypt value before saving.
     *
     * @return void
     */
    public function beforeSave()
    {
        $this->_dataSaveAllowed = false;
        $value = $this->getValue();
        // don't save value, if an obscured value was received. This indicates that data was not changed.
        if (!preg_match('/^\*+$/', $value) && !empty($value)) {
            $this->_dataSaveAllowed = true;
            //$encrypted = $this->_encryptor->encrypt($value);
            $this->setValue($value);
        } elseif (empty($value)) {
            $this->_dataSaveAllowed = true;
        }
    }
}
