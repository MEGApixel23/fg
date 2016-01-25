<?php

namespace api\v1\extensions;

use Yii;
use api\v1\models\queries\UserActiveQuery;

/**
 * Class ApiAuthController
 * @package api\v1\extensions
 *
 * @property $_user api\v1\models\User
 */
class ApiAuthController extends ApiBaseController
{
    protected $_user;

    public function beforeAction($action)
    {
        $token = call_user_func(function() {
            $headers = Yii::$app->request->headers;
            return isset($headers['token']) ? $headers['token'] : null;
        });
        $user = null;

        if ($token) {
            $userQuery = UserActiveQuery::findByToken($token);
            $user = $userQuery ? $userQuery->limit(1)->one() : null;

            $this->_user = $user;
        }

        if (!$user) {
            Yii::$app->response->format = 'json';
            Yii::$app->response->data = [
                'status' => false,
                'error' => 'WRONG_TOKEN',
                'error_code' => 'WRONG_TOKEN'
            ];

            return false;
        }

        return parent::beforeAction($action);
    }
}