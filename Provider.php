<?php

namespace SocialiteProviders\Figma;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'FIGMA';

    protected $scopes = [
        'current_user:read',
        'file_comments:read',
        'file_comments:write',
        'file_content:read',
        'file_dev_resources:read',
        'file_dev_resources:write',
        'file_metadata:read',
        'file_variables:read',
        'file_variables:write',
        'team_library_content:read',
        'projects:read',
        ];

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://www.figma.com/oauth', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://api.figma.com/v1/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.figma.com/v1/me', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'email'    => $user['email'],
            'nickname' => $user['handle'],
            'name'     => $user['handle'],
            'avatar'   => $user['img_url'],
        ]);
    }
}
