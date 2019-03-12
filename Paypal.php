<?php

namespace alkurn\paypal;

use Yii;
use Yii\base\ErrorException;
use Yii\helpers\ArrayHelper;
use yii\base\Component;
use PayPal\Api\Address;
use PayPal\Api\CreditCard;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\FundingInstrument;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;

class Paypal extends Component
{
    //region Mode (production/development)
    const MODE_SANDBOX = 'sandbox';
    const MODE_LIVE = 'live';
    //endregion
    //region Log levels
    /*
     * Logging level can be one of FINE, INFO, WARN or ERROR.
     * Logging is most verbose in the 'FINE' level and decreases as you proceed towards ERROR.
     */
    const LOG_LEVEL_FINE = 'FINE';
    const LOG_LEVEL_INFO = 'INFO';
    const LOG_LEVEL_WARN = 'WARN';
    const LOG_LEVEL_ERROR = 'ERROR';
    //endregion
    //region API settings
    public $clientId;
    public $clientSecret;
    public $isProduction = false;
    public $currency = 'USD';
    public $mode;
    public $businessEmail;
    public $config = [];

    /** @var ApiContext */
    protected $_apiContext = null;

    /**
     * @setConfig
     * _apiContext in init() method
     */
    public function init()
    {
        $this->setConfig();
    }

    /**
     * @inheritdoc
     */
    private function setConfig()
    {
        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com
        $this->_apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->clientId,
                $this->clientSecret
            )
        );

        $this->_apiContext->setConfig([
            'mode' => $this->mode,
            'log.LogEnabled' => YII_DEBUG ? 1 : 0,
            'log.FileName' => Yii::getAlias('@runtime/logs/paypal.log'),
            'log.LogLevel' => self::LOG_LEVEL_FINE,

            /*'log.LogLevel' => 'DEBUG',*/ // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            'cache.enabled' => 'true'

        ]);


        /*return $this->_apiContext;*/
    }

    public function getApiContext()
    {
        return $this->_apiContext;
    }

}