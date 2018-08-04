<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/31/17
 * Time: 16:42
 */

namespace Bridge\Core\Controllers\Actions;

use naffiq\bridge\BridgeModule;
use yii\base\Action;
use yii\httpclient\Client;

/**
 * Class DashboardAction
 * @package Bridge\Core\Controllers\Actions
 */
class DashboardAction extends Action
{
    public function runWithParams($params)
    {
        $repoData = \Yii::$app->cache->getOrSet('yii2-bridge--repo-data', function () {
            $client = new Client();

            try {
                return $client->get($this->getModule()->repoDataUrl, null, [
                    'User-Agent' => 'Yii2 Http Client'
                ])->send()->getData();
            } catch (\Exception $e) {
                return [];
            }

        }, 3600);

        return $this->controller->render('dashboard', [
            'repoData' => $repoData,
            'currentVersion' => $this->getModule()->version
        ]);
    }

    /**
     * @return BridgeModule|mixed
     */
    protected function getModule()
    {
        return $this->controller->module;
    }
}