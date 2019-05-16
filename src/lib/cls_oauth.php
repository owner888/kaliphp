<?php
/**
 * KaliPHP is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KaliPHP
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       http://kaliphp.com
 */

namespace kaliphp\lib;

/**
 * OAUTH 2.0
 *
 * @version $Id$
 *
 */
class cls_oauth
{
    public $config = array(
        'client_table'          => 'oauth_clients',
        'access_token_table'    => 'oauth_access_tokens',
        'refresh_token_table'   => 'oauth_refresh_tokens',
        'code_table'            => 'oauth_authorization_codes',
        'user_table'            => 'oauth_users',
        'jwt_table'             => 'oauth_jwt',
        'jti_table'             => 'oauth_jti',
        'scope_table'           => 'oauth_scopes',
        'public_key_table'      => 'oauth_public_keys',
    );

    /**
     * 验证用户凭证
     * @param string $client_id
     * @param null|string $client_secret
     * @return bool
     */
    public static function check_client_credentials($client_id, $client_secret = null)
    {
        $db_client_secret = db::select('client_secret')
            ->from('#PB#_oauth_clients')
            ->where('client_id', $client_id)
            ->as_field()
            ->execute();

        return $db_client_secret && $db_client_secret == $client_secret;
    }

    /**
     * @param string $client_id
     * @return bool
     */
    public static function is_public_client($client_id)
    {
        $client_secret = db::select('client_secret')
            ->from('#PB#_oauth_clients')
            ->where('client_id', $client_id)
            ->as_field()
            ->execute();

        return empty($client_secret);
    }

    public static function get_client($client_id)
    {
        $row = db::select()
            ->from('#PB#_oauth_clients')
            ->where('client_id', $client_id)
            ->as_row()
            ->execute();

        return $row;
    }

    public static function set_client($client_id, $client_secret = null, $redirect_uri = null, $grant_types = null, $scope = null, $user_id = null)
    {
        $data = array(
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri'  => $redirect_uri,
            'grant_types'   => $grant_types,
            'scope'         => $scope,
            'user_id'       => $user_id,
        );

        if (self::get_client($client_id)) 
        {
            unset($data['client_id']);
            return db::update('#PB#_oauth_clients')->set($data)->where('client_id', $client_id)->execute();
        }
        else 
        {
            return db::insert('#PB#_oauth_clients')->set($data)->execute();
        }

    }

    public static function del_client($client_id)
    {
        db::delete('#PB#_oauth_clients')->where('client_id', $client_id)->execute();
    }

    /**
     * 检查限制的授权类型
     * @param $client_id
     * @param $grant_type
     * @return bool
     */
    public static function check_restricted_grant_type($client_id, $grant_type)
    {
        $details = self::get_client($client_id);
        if (isset($details['grant_types'])) 
        {
            $grant_types = explode(',', $details['grant_types']);
            return in_array($grant_type, (array) $grant_types);
        }

        // 如果授权方式不存在，不需要验证
        return true;
    }

    public static function get_access_token($access_token)
    {
        $row = db::select()
            ->from('#PB#_oauth_access_tokens')
            ->where('access_token', $access_token)
            ->as_row()
            ->execute();
        if (isset($row['expires'])) 
        {
            $row['expires'] = strtotime($row['expires']);
        }
        return $row;
    }

    public static function set_access_token($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        //var_dump($access_token, $user_id, $client_id);    
        //exit;
        $data = array(
            'access_token'  => $access_token,
            'client_id'     => $client_id,
            'expires'       => $expires,
            'scope'         => $scope,
            'user_id'       => $user_id,
            'openid'        => self::get_openid($client_id, $user_id),
        );

        if (self::get_access_token($access_token)) 
        {
            unset($data['access_token']);
            return db::update('#PB#_oauth_access_tokens')
                ->set($data)
                ->where('access_token', $access_token)
                ->execute();
        }
        else 
        {
            return db::insert('#PB#_oauth_access_tokens')
                ->set($data)
                ->execute();
        }

    }

    public static function del_access_token($access_token)
    {
        db::delete('#PB#_oauth_access_tokens')->where('access_token', $access_token)->execute();
    }

    public static function get_authorization_code($code)
    {
        $row = db::select()
            ->from('#PB#_oauth_authorization_codes')
            ->where('authorization_code', $code)
            ->as_row()
            ->execute();
        if (isset($row['expires'])) 
        {
            $row['expires'] = strtotime($row['expires']);
        }
        return $row;
    }

    public static function set_authorization_code($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        $data = array(
            'authorization_code'    => $code,
            'client_id'             => $client_id,
            'user_id'               => $user_id,
            'redirect_uri'          => $redirect_uri,
            'expires'               => $expires,
            'scope'                 => $scope,
        );

        if ( $id_token != null ) 
        {
            $data['id_token'] = $id_token;
        }

        if (self::get_authorization_code($code)) 
        {
            unset($data['authorization_code']);
            db::update('#PB#_oauth_authorization_codes')
                ->set($data)
                ->where('authorization_code', $code)
                ->execute();
        }
        else 
        {
            db::insert('#PB#_oauth_authorization_codes')
                ->set($data)
                ->execute();
        }

    }

    public static function del_authorization_code($code)
    {
        db::delete('#PB#_oauth_authorization_codes')
            ->where('authorization_code', $code)
            ->execute();
    }

    public static function get_refresh_token($refresh_token)
    {
        $row = db::select()
            ->from('#PB#_oauth_refresh_tokens')
            ->where('refresh_token', $refresh_token)
            ->as_row()
            ->execute();
        if (isset($row['expires'])) 
        {
            $row['expires'] = strtotime($row['expires']);
        }
        return $row;
    }

    public static function set_refresh_token($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        $data = array(
            'refresh_token' => $refresh_token,
            'client_id'     => $client_id,
            'user_id'       => $user_id,
            'openid'        => self::get_openid($client_id, $user_id),
            'expires'       => $expires,
            'scope'         => $scope,
        );

        if (self::get_refresh_token($refresh_token)) 
        {
            unset($data['refresh_token']);
            return db::update('#PB#_oauth_refresh_tokens')
                ->set($data)
                ->where('refresh_token', $refresh_token)
                ->execute();
        }
        else 
        {
            return db::insert('#PB#_oauth_refresh_tokens')
                ->set($data)
                ->execute();
        }
    }

    public static function del_refresh_token($refresh_token)
    {
        db::delete('#PB#_oauth_refresh_tokens')
            ->where('refresh_token', $refresh_token)
            ->execute();
    }
    
    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function check_user($username, $password)
    {
        if ($user = self::get_user($username)) 
        {
            return self::check_password($user, $password);
        }

        return false;
    }

    /**
     * @param string $user
     * @param string $password
     * @return bool
     */
    protected static function check_password($user, $password)
    {
        return $user['password'] == self::hash_password($password);
    }

    public static function hash_password($password)
    {
        return md5($password);
    }

    /**
     * 获取客户授权范围
     *
     * @param mixed $client_id
     * @return bool|null
     */
    public static function get_user_scope($username)
    {
        if (!$user = self::get_user($username)) 
        {
            return false;
        }

        if (isset($user['scope'])) 
        {
            return $user['scope'];
        }

        return null;
    }

    /**
     * @param string $username
     * @return array|bool
     */
    public static function get_user($username)
    {
        $row = db::select()
            ->from('#PB#_user')
            ->where('username', $username)
            ->as_row()
            ->execute();

        return $row;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @return bool
     */
    public static function set_user($username, $password, $first_name = null, $last_name = null)
    {
        // 不要保存明文密码
        $password = self::hash_password($password);

        $data = array(
            'username'      => $username,
            'password'      => $password,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
        );

        if (self::get_user($username)) 
        {
            unset($data['username']);
            //return db::update('#PB#_oauth_users')
            return db::update('#PB#_user')
                ->set($data)
                ->where('username', $username)
                ->execute();
        }
        else 
        {
            //return db::insert('#PB#_oauth_users')
            return db::insert('#PB#_user')
                ->set($data)
                ->execute();
        }
    }

    /**
     * 检查请求的授权是否存在已有授权里面
     *
     * @param string $required_scope  - A space-separated string of scopes.
     * @param string $available_scope - A space-separated string of scopes.
     * @return bool                   - TRUE if everything in required scope is contained in available scope and FALSE
     *                                  if it isn't.
     */
    public static function check_scope($required_scope, $available_scope)
    {
        $required_scope = explode(',', trim($required_scope));
        $available_scope = explode(',', trim($available_scope));
        return (count(array_diff($required_scope, $available_scope)) == 0);
    }

    /**
     * 应用是否存在授权
     *
     * @param string $scope
     * @return bool
     */
    public static function client_scope_exists($scope, $client_id)
    {
        $available_scope = self::get_client_scope($client_id);
        return self::check_scope($scope, $available_scope);
    }

    /**
     * 用户是否存在授权
     *
     * @param string $scope
     * @return bool
     */
    public static function user_scope_exists($scope, $user_id)
    {
        $available_scope = self::get_user_scope($user_id);
        return self::check_scope($scope, $available_scope);
    }

    /**
     * 客户是否存在授权
     *
     * @param string $scope
     * @return bool
     */
    public static function scope_exists($scope)
    {
        $rows = db::select('scope')
            ->from('#PB#_oauth_scopes')
            ->execute();

        $reserved_scope = array();
        foreach ($rows as $row) 
        {
            $reserved_scope[] = $row['scope'];
        }
        $available_scope = implode(",", $reserved_scope);

        return self::check_scope($scope, $available_scope);
    }

    /**
     * 获取所有默认授权
     *
     * @param mixed $client_id
     * @return null|string
     */
    public static function get_default_scope($client_id = null)
    {
        $rows = db::select('scope')
            ->from('#PB#_oauth_scopes')
            ->where('is_default', 1)
            ->execute();

        foreach ($rows as $row) 
        {
            $default_scope = array_map(function ($row) {
                return $row['scope'];
            }, $rows);

            return implode(',', $default_scope);
        }

        return null;
    }

    /**
     * 获取客户授权范围
     *
     * @param mixed $client_id
     * @return bool|null
     */
    public static function get_client_scope($client_id)
    {
        if (!$client = self::get_client($client_id)) 
        {
            return false;
        }

        if (isset($client['scope'])) 
        {
            return $client['scope'];
        }

        return null;
    }

    /**
     * @param mixed $client_id
     * @param $subject
     * @return string
     */
    public function get_client_key($client_id, $subject)
    {
        $public_key = db::select('public_key')
            ->from('#PB#_oauth_jwt')
            ->where('client_id', $client_id)
            ->where('subject', $subject)
            ->as_field()
            ->execute();

        return $public_key;
    }

    /**
     * @param mixed $client_id
     * @param $subject
     * @param $audience
     * @param $expires
     * @param $jti
     * @return array|null
     */
    public static function get_jti($client_id, $subject, $audience, $expires, $jti)
    {
        $row = db::select()
            ->from('#PB#_oauth_jti')
            ->where('issuer', $client_id)
            ->where('subject', $subject)
            ->where('audience', $audience)
            ->where('expires', $expires)
            ->where('jti', $jti)
            ->as_row()
            ->execute();

        return $row;

    }

    /**
     * @param mixed $client_id
     * @param $subject
     * @param $audience
     * @param $expires
     * @param $jti
     * @return bool
     */
    public static function set_jti($client_id, $subject, $audience, $expires, $jti)
    {
        $data = array(
            'issuer'    => $client_id,  // 发行人
            'subject'   => $subject,    // 主题
            'audience'  => $audience,   // 观众
            'expires'   => $expires,
            'jti'       => $jti,
        );

        return db::insert('#PB#_oauth_clients')
            ->set($data)
            ->execute();
    }

    /**
     * 获得公钥
     * @param mixed $client_id
     * @return mixed
     */
    public static function get_public_key($client_id = null)
    {
        $public_key = db::select('public_key')
            ->from('#pb#_oauth_public_keys')
            ->where('client_id', $client_id)
            ->as_field()
            ->execute();

        return $public_key;
    }

    /**
     * 获得私钥
     * @param mixed $client_id
     * @return mixed
     */
    public static function get_private_key($client_id = null)
    {
        $private_key = db::select('private_key')
            ->from('#pb#_oauth_public_keys')
            ->where('client_id', $client_id)
            ->as_field()
            ->execute();

        return $private_key;
    }

    /**
     * 获得加密算法
     * @param mixed $client_id
     * @return string
     */
    public static function get_encryption_algorithm($client_id = null)
    {
        $encryption_algorithm = db::select('encryption_algorithm')
            ->from('#pb#_oauth_public_keys')
            ->where('client_id', $client_id)
            ->as_field()
            ->execute();

        if ( !empty($encryption_algorithm) ) 
        {
            return $encryption_algorithm;
        }

        return 'RS256';
    }

    public static function get_openid($client_id, $user_id)
    {
        return md5($client_id.'-'.$user_id);
    }

    /**
     * DDL to create OAuth2 database and tables for PDO storage
     *
     * @see https://github.com/dsquier/oauth2-server-php-mysql
     *
     * @param string $dbName
     * @return string
     */
    public static function get_build_sql($dbName = 'oauth2_server_php')
    {
        $sql = "
            CREATE TABLE {$this->config['client_table']} (
              client_id             VARCHAR(32)   NOT NULL,
              client_secret         VARCHAR(32),
              redirect_uri          VARCHAR(2000),
              grant_types           VARCHAR(80),
              scope                 VARCHAR(4000),
              user_id               VARCHAR(80),
              PRIMARY KEY (client_id)
            );

            CREATE TABLE {$this->config['access_token_table']} (
              access_token         VARCHAR(32)    NOT NULL,
              client_id            VARCHAR(80)    NOT NULL,
              user_id              VARCHAR(80),
              expires              TIMESTAMP      NOT NULL,
              scope                VARCHAR(4000),
              PRIMARY KEY (access_token)
            );

            CREATE TABLE {$this->config['code_table']} (
              authorization_code  VARCHAR(32)    NOT NULL,
              client_id           VARCHAR(32)    NOT NULL,
              user_id             VARCHAR(80),
              redirect_uri        VARCHAR(2000),
              expires             TIMESTAMP      NOT NULL,
              scope               VARCHAR(4000),
              id_token            VARCHAR(1000),
              PRIMARY KEY (authorization_code)
            );

            CREATE TABLE {$this->config['refresh_token_table']} (
              refresh_token       VARCHAR(32)    NOT NULL,
              client_id           VARCHAR(32)    NOT NULL,
              user_id             VARCHAR(80),
              expires             TIMESTAMP      NOT NULL,
              scope               VARCHAR(4000),
              PRIMARY KEY (refresh_token)
            );

            CREATE TABLE {$this->config['user_table']} (
              username            VARCHAR(80),
              password            VARCHAR(32),
              first_name          VARCHAR(80),
              last_name           VARCHAR(80),
              email               VARCHAR(80),
              email_verified      BOOLEAN,
              scope               VARCHAR(4000)
            );

            CREATE TABLE {$this->config['scope_table']} (
              scope               VARCHAR(80)  NOT NULL,
              is_default          BOOLEAN,
              PRIMARY KEY (scope)
            );

            CREATE TABLE {$this->config['jwt_table']} (
              client_id           VARCHAR(80)   NOT NULL,
              subject             VARCHAR(80),
              public_key          VARCHAR(2000) NOT NULL
            );

            CREATE TABLE {$this->config['jti_table']} (
              issuer              VARCHAR(80)   NOT NULL,
              subject             VARCHAR(80),
              audiance            VARCHAR(80),
              expires             TIMESTAMP     NOT NULL,
              jti                 VARCHAR(2000) NOT NULL
            );

            CREATE TABLE {$this->config['public_key_table']} (
              client_id            VARCHAR(32),
              public_key           VARCHAR(2000),
              private_key          VARCHAR(2000),
              encryption_algorithm VARCHAR(100) DEFAULT 'RS256'
            )
        ";

        return $sql;
    }
}
