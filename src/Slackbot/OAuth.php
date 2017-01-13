<?php

namespace Slackbot;

use Slackbot\client\ApiClient;

/**
 * Class OAuth.
 */
class OAuth
{
    const AUTHORIZATION_URL = 'https://slack.com/oauth/authorize';

    private $clientId;
    private $clientSecret;
    private $scopes;
    private $redirectUri;
    private $state;
    private $teamId;
    private $accessToken;

    /**
     * OAuth constructor.
     *
     * @param       $clientId
     * @param array $scopes
     */
    public function __construct($clientId, array $scopes)
    {
        $this->setClientId($clientId);
        $this->setScopes($scopes);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param array $scopes
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param int $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @param string $height
     * @param string $weight
     * @param string $cssClass
     *
     * @return string
     */
    public function generateAddButton($height = '40', $weight = '139', $cssClass = '')
    {
        $authorizationUrl = self::AUTHORIZATION_URL;
        $scope = implode(',', $this->getScopes());
        $clientId = $this->getClientId();

        $cssClass = '';

        if (!empty($cssClass)) {
            $cssClass = "class={$cssClass}";
        }

        $stateQueryString = '';

        if (!empty($this->getState())) {
            $state = $this->getState();
            $stateQueryString = "&state={$state}";
        }

        $html = "<a href='{$authorizationUrl}?scope={$scope}&client_id={$clientId}{$stateQueryString}'>
<img {$cssClass} alt='Add to Slack' height='{$height}' width='{$weight}'
src='https://platform.slack-edge.com/img/add_to_slack.png'
srcset='https://platform.slack-edge.com/img/add_to_slack.png 1x,
https://platform.slack-edge.com/img/add_to_slack@2x.png 2x' /></a>";

        return $html;
    }

    /**
     * @param null $code
     *
     * @return mixed
     * @throws \Exception
     */
    public function getAccessToken($code = null)
    {
        if (!isset($this->accessToken)) {
            $this->setAccessToken($this->requestAccessToken($code));
        }

        return $this->accessToken;
    }

    /**
     * @param $code
     *
     * @return mixed
     * @throws \Exception
     */
    private function requestAccessToken($code)
    {
        if (empty($code)) {
            throw new \Exception('Code must be provided to get the access token');
        }

        return (new ApiClient())->oauthAccess([
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'code' => $code
        ]);
    }

    /**
     * @param $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }
}
