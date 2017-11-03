<?php
/**
 * Created by PhpStorm.
 * User: guertz
 * Date: 11/2/17
 * Time: 7:58 PM
 */

namespace App;

use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAccountService
{
    public function createOrGetUser(SocialiteUser $providerUser)
    {
        $account = SocialAccount::whereProvider('facebook')
                                ->whereProviderUserId($providerUser->getId())
                                ->first();

        if ($account) {
            return $account->user;
        } else {

            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => 'facebook'
            ]);

            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                    'first_name' => $providerUser->user['first_name'],
                    'last_name' => $providerUser->user['last_name'],
                ]);
            }

            $account->user()->associate($user);
            $account->save();

            return $user;

        }

    }
}