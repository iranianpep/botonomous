<?php

namespace Botonomous;

use Botonomous\client\ApiClient;
use Botonomous\utility\RequestUtility;
use Botonomous\utility\SecurityUtility;
use Botonomous\utility\SessionUtility;

/**
 * Class OAuth.
 */
class OAuth
{
    const AUTHORIZATION_URL = 'https://slack.com/oauth/authorize';
    const SESSION_STATE_KEY = 'state';

    private $clientId;
    private $clientSecret;
    private $scopes;
    private $redirectUri;
    private $state;
    private $teamId;
    private $apiClient;
    private $sessionUtility;
    private $requestUtility;

    /**
     * @var string configuration_url will be the URL that you can point your user to if they'd like to edit
     *             or remove this integration in Slack
     */
    private $configurationUrl;

    /**
     * @var string The team_name field will be the name of the team that installed your app
     */
    private $teamName;
    private $accessToken;

    /**
     * @var string the channel will be the channel name that they have chosen to post to
     */
    private $channel;

    /**
     * @var string you will need to use bot_user_id and bot_access_token whenever you are acting on behalf of
     *             that bot user for that team context.
     *             Use the top-level access_token value for other integration points.
     */
    private $botUserId;
    private $botAccessToken;

    private $config;

    /**
     * OAuth constructor.
     *
     * @param       $clientId
     * @param       $clientSecret
     * @param array $scopes
     */
    public function __construct($clientId = '', $clientSecret = '', array $scopes = [])
    {
        $this->setClientId($clientId);
        $this->setClientSecret($clientSecret);
        $this->setScopes($scopes);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getClientId(): string
    {
        if (empty($this->clientId)) {
            $this->setClientId($this->getConfig()->get('clientId'));
        }

        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getScopes(): array
    {
        if (empty($this->scopes)) {
            $this->setScopes($this->getConfig()->get('scopes'));
        }

        return $this->scopes;
    }

    /**
     * @param array $scopes
     */
    public function setScopes(array $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri(string $redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getState(): string
    {
        if (!isset($this->state)) {
            $this->setState((new SecurityUtility())->generateToken());
        }

        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state)
    {
        $this->getSessionUtility()->set(self::SESSION_STATE_KEY, $state);
        $this->state = $state;
    }

    /**
     * @param $state
     *
     * @return bool
     */
    public function verifyState($state): bool
    {
        if (empty($state)) {
            return false;
        }

        return $state === $this->getSessionUtility()->get(self::SESSION_STATE_KEY) ? true : false;
    }

    /**
     * @return string
     */
    public function getTeamId(): string
    {
        return $this->teamId;
    }

    /**
     * @param string $teamId
     */
    public function setTeamId(string $teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @param string $height
     * @param string $weight
     * @param string $cssClass
     *
     * @return string
     * @throws \Exception
     */
    public function generateAddButton($height = '40', $weight = '139', $cssClass = ''): string
    {
        $authorizationUrl = self::AUTHORIZATION_URL;
        $scope = implode(',', $this->getScopes());
        $clientId = $this->getClientId();

        $stateQueryString = '';
        if (!empty($this->getState())) {
            $state = $this->getState();
            $stateQueryString = "&state={$state}";
        }

        $href = "{$authorizationUrl}?scope={$scope}&client_id={$clientId}{$stateQueryString}";

        $html = "<a href='{$href}'>
<img alt='Add to Slack' class='{$cssClass}' height='{$height}' width='{$weight}'
src='https://platform.slack-edge.com/img/add_to_slack.png'
srcset='https://platform.slack-edge.com/img/add_to_slack.png 1x,
https://platform.slack-edge.com/img/add_to_slack@2x.png 2x' /></a>";

        return $html;
    }

    /**
     * @param      $code
     * @param bool $verifyState State is checked against the value in the session
     * @param null $state
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getAccessToken($code, $verifyState = true, $state = null)
    {
        if (!isset($this->accessToken)) {
            if ($verifyState === true && $this->verifyState($state) !== true) {
                throw new BotonomousException("State: '{$state}' is not valid");
            }

            try {
                $this->handleRequestAccessTokenResponse($this->requestAccessToken($code));
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $this->accessToken;
    }

    /**
     * @param $response
     *
     * @throws \Exception
     */
    private function handleRequestAccessTokenResponse($response)
    {
        if ($response['ok'] !== true) {
            throw new BotonomousException($response['error']);
        }

        $this->setAccessToken($response['access_token']);
        $this->setTeamId($response['team_id']);
        $this->setBotUserId($response['bot']['bot_user_id']);
        $this->setBotAccessToken($response['bot']['bot_access_token']);

        $channel = '';
        if (isset($response['incoming_webhook']['channel'])) {
            $channel = $response['incoming_webhook']['channel'];
        }

        $this->setChannel($channel);
    }

    /**
     * @param $code
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function requestAccessToken($code)
    {
        if (empty($code)) {
            throw new BotonomousException('Code must be provided to get the access token');
        }

        try {
            return $this->getApiClient()->oauthAccess([
                'client_id'     => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'code'          => $code,
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $accessToken
     */
    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getClientSecret(): string
    {
        if (empty($this->clientSecret)) {
            $this->setClientSecret($this->getConfig()->get('clientSecret'));
        }

        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function getBotUserId(): string
    {
        return $this->botUserId;
    }

    /**
     * @param string $botUserId
     */
    public function setBotUserId(string $botUserId)
    {
        $this->botUserId = $botUserId;
    }

    /**
     * @return string
     */
    public function getBotAccessToken(): string
    {
        return $this->botAccessToken;
    }

    /**
     * @param string $botAccessToken
     */
    public function setBotAccessToken(string $botAccessToken)
    {
        $this->botAccessToken = $botAccessToken;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function getTeamName(): string
    {
        return $this->teamName;
    }

    /**
     * @param string $teamName
     */
    public function setTeamName(string $teamName)
    {
        $this->teamName = $teamName;
    }

    /**
     * @return string
     */
    public function getConfigurationUrl(): string
    {
        return $this->configurationUrl;
    }

    /**
     * @param string $configurationUrl
     */
    public function setConfigurationUrl(string $configurationUrl)
    {
        $this->configurationUrl = $configurationUrl;
    }

    /**
     * @return ApiClient
     */
    public function getApiClient(): ApiClient
    {
        if (!isset($this->apiClient)) {
            $this->setApiClient(new ApiClient());
        }

        return $this->apiClient;
    }

    /**
     * @param ApiClient $apiClient
     */
    public function setApiClient(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @param SessionUtility $sessionUtility
     */
    public function setSessionUtility(SessionUtility $sessionUtility)
    {
        $this->sessionUtility = $sessionUtility;
    }

    /**
     * @return SessionUtility|null
     */
    public function getSessionUtility(): SessionUtility
    {
        if (!isset($this->sessionUtility)) {
            $this->setSessionUtility(new SessionUtility());
        }

        return $this->sessionUtility;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        if (!isset($this->config)) {
            $this->setConfig(new Config());
        }

        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param null $code
     * @param null $state
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function doOauth($code = null, $state = null)
    {
        $getRequest = $this->getRequestUtility()->getGet();

        // get code from GET request if $code is null
        $code = $code === null && isset($getRequest['code']) ? $getRequest['code'] : $code;

        // get state from GET request if $state is null
        $state = $state === null && isset($getRequest['state']) ? $getRequest['state'] : $state;

        try {
            $this->processAccessToken($this->getAccessToken($code, true, $state));
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * @param $accessToken
     *
     * @throws \Exception
     */
    private function processAccessToken($accessToken)
    {
        if (empty($accessToken)) {
            throw new BotonomousException('Access token is not provided');
        }

        // do whatever you want with the access token
    }

    /**
     * @return RequestUtility
     */
    public function getRequestUtility(): RequestUtility
    {
        if (!isset($this->requestUtility)) {
            $this->setRequestUtility((new RequestUtility()));
        }

        return $this->requestUtility;
    }

    /**
     * @param RequestUtility $requestUtility
     */
    public function setRequestUtility(RequestUtility $requestUtility)
    {
        $this->requestUtility = $requestUtility;
    }
}
