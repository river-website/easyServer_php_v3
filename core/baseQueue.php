<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/14
 * Time: 10:41
 */
namespace easyServer\core;

class baseQueue
{
    # ��������
    private $list = [];
    # ���ʶ��е���
    private $queueLock = null;
    # �жϿյ���
    private $emptyLock = null;

    # ��ʼ��,��������Ϊ��
    public function __construct($lock = false)
    {
        if ($lock) {
            $this->queueLock = new baseMutex();
            $this->emptyLock = new baseMutex();
            $this->setEmpty();
        }
    }

    private function setEmpty()
    {
        if (!empty($this->emptyLock))
            $this->emptyLock->lock();
    }

    private function setNotEmpty()
    {
        if (!empty($this->emptyLock))
            $this->emptyLock->unLock();
    }

    private function waitNotEmpty()
    {
        if (!empty($this->emptyLock))
            $this->emptyLock->lock();
    }

    private function acquireQueue()
    {
        if (!empty($this->queueLock))
            $this->queueLock->lock();
    }

    private function releaseQueue()
    {
        if (!empty($this->queueLock))
            $this->queueLock->unLock();
    }

    public function push($item)
    {
        $this->acquireQueue();
        if (count($this->list) == 0)
            $this->setNotEmpty();
        array_push($this->list,$item);
        $this->releaseQueue();
    }

    public function pop()
    {
        $this->waitNotEmpty();
        $this->acquireQueue();
        $ret = array_shift($this->list);
        if (count($this->list) > 0)
            $this->setNotEmpty();
        $this->releaseQueue();
        return $ret;
    }
}