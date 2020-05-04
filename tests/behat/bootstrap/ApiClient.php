<?php

namespace hiqdev\billing\hiapi\tests\behat\bootstrap;

use Dotenv\Dotenv;
use hiqdev\hiart\guzzle\Request;
use hipanel\hiart\Connection;
use hiqdev\hiart\RequestInterface;
use Yii;
use yii\di\Container;
use yii\web\Application;
use hiqdev\php\billing\tests\support\tools\SimpleFactory;

class ApiClient
{
    private Connection $connection;

    private Application $application;

    public function __construct()
    {
        $this->bootstrap();
        $application = $this->application ?? $this->mockApplication();
        $this->connection = new Connection($application);
        $this->connection->requestClass = Request::class;
        $this->connection->baseUri = $_ENV['HIART_BASEURI'];
    }

    private function bootstrap(): void
    {
        $dir = dirname(__DIR__, 6);
        $pathToYii = $dir . '/vendor/yiisoft/yii2/Yii.php';
        require_once $pathToYii;
        (new Dotenv($dir))->load();
        if (empty($_ENV['HIART_BASEURI'])) {
            throw new \Exception('HIART_BASEURI must be set in environment');
        }
    }

    public function make(string $command, array $payload, string $performer): array
    {
        $res = $this->buildRequest($command, $payload, $performer ?? $this->reseller)->send()->getData();
        if (!is_array($res)) {
            throw new \Exception('API return not array: ' . $res);
        }
        if (!empty($res['_error'])) {
            // var_dump(__FILE__ . ':' . __LINE__ . ' ' . __METHOD__, $command, $payload, $performer, $res);
            throw new \Exception('API return error: ' . $res['_error']);
        }

        return $res;
    }

    private function buildRequest(string $command, array $body = [], ?string $performer = null): RequestInterface
    {
        $auth_login = $performer;
        $auth_password = 'random';

        return $this->connection->callWithDisabledAuth(
            function () use ($command, $body, $auth_login, $auth_password) {
                $request = $this->connection
                    ->createCommand()
                    ->db
                    ->getQueryBuilder()
                    ->perform(
                        $command,
                        null,
                        array_merge($body, compact('auth_login', 'auth_password'))
                    );
                $request->build();

                return $request;
            }
        );
    }

    private function mockApplication(): Application
    {
        Yii::$container = new Container();
        return new Application(
            [
                'id'         => 'behat-test-application',
                'basePath'   => __DIR__,
                'vendorPath' => __DIR__ . '../../../../../',
            ]
        );
    }
}
