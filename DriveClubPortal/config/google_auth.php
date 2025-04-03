<?php
/**
 * Google OAuth Configuration
 * 
 * This file contains the configuration for Google OAuth authentication.
 */

// Google OAuth API credentials
define('GOOGLE_CLIENT_ID', ''); // Fill with your Google client ID
define('GOOGLE_CLIENT_SECRET', ''); // Fill with your Google client secret
define('GOOGLE_REDIRECT_URI', 'http://localhost/public/auth_google_callback.php');

/**
 * Get Google OAuth client configuration
 * 
 * @return array Client configuration array
 */
function getGoogleClientConfig() {
    return [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'scope' => [
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ]
    ];
}

/**
 * Create Google OAuth client
 * 
 * @return Google_Client Google client instance
 */
function createGoogleClient() {
    $client = new Google_Client();
    $config = getGoogleClientConfig();
    
    $client->setClientId($config['client_id']);
    $client->setClientSecret($config['client_secret']);
    $client->setRedirectUri($config['redirect_uri']);
    $client->setScopes($config['scope']);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    
    return $client;
}

/**
 * Get Google authentication URL
 * 
 * @return string URL for Google authentication
 */
function getGoogleAuthUrl() {
    $client = createGoogleClient();
    return $client->createAuthUrl();
}

/**
 * Process Google OAuth callback
 * 
 * @param string $authCode Authorization code from Google
 * @return array|false User data array or false on failure
 */
function processGoogleCallback($authCode) {
    try {
        $client = createGoogleClient();
        $token = $client->fetchAccessTokenWithAuthCode($authCode);
        
        if (isset($token['error'])) {
            error_log('Google Auth error: ' . $token['error']);
            return false;
        }
        
        $client->setAccessToken($token);
        
        // Get user profile
        $google_oauth = new Google_Service_Oauth2($client);
        $userInfo = $google_oauth->userinfo->get();
        
        // Extract user data
        $userData = [
            'email' => $userInfo->getEmail(),
            'name' => $userInfo->getGivenName(),
            'lastname' => $userInfo->getFamilyName(),
            'picture' => $userInfo->getPicture(),
            'google_id' => $userInfo->getId()
        ];
        
        return $userData;
    } catch (Exception $e) {
        error_log('Google Auth exception: ' . $e->getMessage());
        return false;
    }
}

/**
 * Check if user exists by Google ID
 * 
 * @param string $googleId Google ID to check
 * @return array|null User data if exists, null otherwise
 */
function getUserByGoogleId($googleId) {
    // First check if we need to add the google_id column to the users table
    ensureGoogleIdColumn();
    
    return fetchOne(
        "SELECT * FROM usuarios WHERE google_id = ?",
        [$googleId],
        's'
    );
}

/**
 * Creates or updates a user with Google account data
 * 
 * @param array $userData Google user data
 * @return int|false User ID on success or false on failure
 */
function createOrUpdateGoogleUser($userData) {
    // First check if we need to add the google_id column to the users table
    ensureGoogleIdColumn();
    
    // Check if user exists by Google ID
    $existingUser = getUserByGoogleId($userData['google_id']);
    
    if ($existingUser) {
        // Update existing user
        $updateData = [
            'nombre' => $userData['name'],
            'apellidos' => $userData['lastname'],
            'avatar' => $userData['picture'],
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];
        
        $result = updateData(
            'usuarios',
            $updateData,
            'id = ?',
            [$existingUser['id']]
        );
        
        return $result ? $existingUser['id'] : false;
    } else {
        // Check if user exists by email
        $existingUserByEmail = fetchOne(
            "SELECT * FROM usuarios WHERE email = ?",
            [$userData['email']],
            's'
        );
        
        if ($existingUserByEmail) {
            // Update existing user with Google ID
            $updateData = [
                'google_id' => $userData['google_id'],
                'avatar' => $userData['picture'],
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];
            
            $result = updateData(
                'usuarios',
                $updateData,
                'id = ?',
                [$existingUserByEmail['id']]
            );
            
            return $result ? $existingUserByEmail['id'] : false;
        } else {
            // Create new user
            $password = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);
            
            $insertData = [
                'email' => $userData['email'],
                'password' => $password,
                'nombre' => $userData['name'],
                'apellidos' => $userData['lastname'],
                'avatar' => $userData['picture'],
                'google_id' => $userData['google_id'],
                'activo' => 1,
                'rol_id' => 2, // Regular user role
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];
            
            return insertData('usuarios', $insertData);
        }
    }
}

/**
 * Ensure the google_id column exists in the usuarios table
 */
function ensureGoogleIdColumn() {
    $conn = getDbConnection();
    
    if (!$conn) {
        return;
    }
    
    // Check if column exists
    $columnExists = false;
    $result = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'google_id'");
    
    if ($result && $result->num_rows > 0) {
        $columnExists = true;
    }
    
    // Add column if it doesn't exist
    if (!$columnExists) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN google_id VARCHAR(100) NULL AFTER avatar");
    }
    
    $conn->close();
}
