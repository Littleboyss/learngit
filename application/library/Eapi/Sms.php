<?php

/**
 * 短信消息E_API
 *
 * @author YQL
 * @datetime 2017-08-16
 */
class Eapi_Sms extends Eapi_Base
{
    /**
     * 发短信 --- 统一出口
     */
    private function sendSms($toPhone, $message, $type)
    {
        return $this->_eapiModel->sendSms($toPhone, $message, $type);
    }
}
