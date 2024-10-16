<?php

namespace console\controllers;

use Yii;
use \yii\helpers\Console;
use \yii\console\Controller;
use common\models\User;
use Config_Lite;

class PrototypeController extends Controller
{
    public $appUrl;
	private $protoUrl;
	private $tusUrl;
	private $apiUrl;

    /**
     * Command for setting up the prototype
     * Usage:
     * "yii prototype/setup --appUrl <url>"
    */
    public function actionSetup() {
        $this->protoUrl = $this->appUrl."/proto/";
        $this->tusUrl = $this->appUrl."/files/";
        $this->apiUrl = "http://fuw-admin-api/filedrop-accounts";

    	$this->stdout("Setting up the prototype\n", Console::FG_CYAN, Console::BOLD);
    	$this->stdout("with settings:\n");
    	$this->stdout("appUrl:  ". $this->ansiFormat($this->appUrl, Console::FG_BLUE, Console::BOLD)."\n");
        $this->stdout("protoUrl:  ". $this->ansiFormat($this->protoUrl, Console::FG_BLUE, Console::BOLD)."\n");
    	$this->stdout("apiUrl: ". $this->ansiFormat($this->apiUrl, Console::FG_BLUE, Console::BOLD)."\n");
    	$this->stdout("tusUrl: ". $this->ansiFormat($this->tusUrl, Console::FG_BLUE, Console::BOLD)."\n");

    	if ( !($this->protoUrl && $this->apiUrl && $this->tusUrl) ) {
    		$this->stdout("Some argument is missing\n", Console::BOLD);
    		return Controller::EXIT_CODE_ERROR;
    	}

    	// 1. Generate JWT token for interacting with the API
    	$this->stdout("Create token...");
    	$signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
    	$client_token = Yii::$app->jwt->getBuilder()
            ->setIssuer('www.gigadb.org') // Configures the issuer (iss claim)
            ->setAudience('fuw.gigadb.org') // Configures the audience (aud claim)
            ->setSubject('API Access request from client') // Configures the subject
            ->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
            ->set('email', "sfriesen@jenkins.info")
            ->set('name', "John Smith")
            ->set('role', "create")
            ->set('admin_status', "true")
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            ->setNotBefore(time() + 60) // Configures the time before which the token cannot be accepted (nbf claim)
            ->setExpiration(time() + 31104000) // Configures the expiration time of the token (exp claim) 1 year
            ->sign($signer, Yii::$app->jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken(); // Retrieves the generated token
        if( $client_token ) {
	    	$this->stdout("ok\n", Console::FG_GREEN, Console::BOLD);
    	}
    	else {
	    	$this->stdout("error\n", Console::FG_GREEN, Console::BOLD);
    	}

    	// 2. Generate prototype configuration file
    	$this->stdout("Create config...");
    	$config = new Config_Lite();
    	$configFilename = "/app/proto/appconfig.ini" ;
    	$protoConfigData = array(
    							"tusd_endpoint" => $this->tusUrl,
    							"ftpd_endpoint" => "localhost",
    							"ftpd_port" => 9021,
    							"web_endpoint" => $this->protoUrl,
    							"api_endpoint" => $this->apiUrl,
    							"db_user" => Yii::$app->db->username,
    							"db_password" => Yii::$app->db->password,
                                "db_dsn" => Yii::$app->db->dsn,
    							"dummy_jwt_token" => $client_token);

		try {
			$config->write($configFilename, $protoConfigData );
		    $this->stdout("ok\n", Console::FG_GREEN, Console::BOLD);
		} catch (Config_Lite_Exception $exception) {
			$this->stderr("error\n", Console::FG_RED, Console::BOLD);
		    $this->stderr("Failed to write file: $configFilename\n");
		    $this->stderr("Exception Message: ".$exception->getMessage()."\n", Console::FG_RED);
		    $this->stderr("Exception Stracktrace: ".$exception->getTraceAsString()."\n", Console::FG_RED);
		}

    	return Controller::EXIT_CODE_NORMAL;
    }

    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help','appUrl'];
    }

}