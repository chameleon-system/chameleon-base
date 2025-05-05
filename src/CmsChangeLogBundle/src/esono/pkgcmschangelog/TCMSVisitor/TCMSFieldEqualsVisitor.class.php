<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldEqualsVisitor implements TCMSFieldVisitorInterface
{
    /**
     * @var TCMSField
     */
    protected $oField1;
    /**
     * @var TCMSField
     */
    protected $oField2;

    public function __construct(TCMSField $oField1, TCMSField $oField2)
    {
        $this->oField1 = $oField1;
        $this->oField2 = $oField2;
    }

    /**
     * @return bool
     */
    public function check()
    {
        if (get_class($this->oField1) !== get_class($this->oField2)) {
            return false;
        }

        return $this->oField1->accept($this);
    }

    /**
     * @return bool
     */
    public function visit(TCMSField $oField)
    {
        $methods = get_class_methods(get_class($this));
        if (in_array('visit'.get_class($oField), $methods)) {
            return $this->{'visit'.get_class($oField)}($oField);
        } else {
            if ($oField instanceof TCMSMLTField) {
                return $this->visitTCMSMLTField();
            } else {
                return $this->visitTCMSField();
            }
        }
    }

    /**
     * @return bool
     */
    protected function visitTCMSField()
    {
        return $this->oField1->data === $this->oField2->data;
    }

    /**
     * @return bool
     */
    protected function visitTCMSMLTField()
    {
        if (!is_array($this->oField1->data) || !is_array($this->oField2->data)) {
            return $this->oField1->data === $this->oField2->data;
        }
        if (count($this->oField1->data) !== count($this->oField2->data)) {
            return false;
        }

        return (count($this->oField1->data) === count(array_intersect($this->oField1->data, $this->oField2->data)))
        && (count($this->oField2->data) === count(array_intersect($this->oField2->data, $this->oField1->data)));
    }

    /**
     * @return bool
     */
    protected function visitTCMSFieldDate()
    {
        if ('' === $this->oField1->data) {
            return ('' === $this->oField2->data) || ('0000-00-00' === $this->oField2->data);
        }
        if ('' === $this->oField2->data) {
            return ('' === $this->oField1->data) || ('0000-00-00' === $this->oField1->data);
        }

        return $this->oField1->data === $this->oField2->data;
    }

    /**
     * @return bool
     */
    protected function visitTCMSFieldDateTime()
    {
        if ('' === $this->oField1->data) {
            return ('' === $this->oField2->data) || ('0000-00-00 00:00:00' === $this->oField2->data);
        }
        if ('' === $this->oField2->data) {
            return ('' === $this->oField1->data) || ('0000-00-00 00:00:00' === $this->oField1->data);
        }

        return $this->oField1->data === $this->oField2->data;
    }

    /**
     * @return bool
     */
    protected function visitTCMSFieldPasswordEncrypted()
    {
        return ('' === $this->oField1->data) || ('' === $this->oField2->data) || $this->oField1->data === $this->oField2->data;
    }

    /**
     * @return bool
     */
    protected function visitTCMSFieldDownloads()
    {
        // the download field cannot be handled this way - we need to intercept the assignment operation.
        // so we exclude it from the change log by always returning true
        return true;
    }
}
