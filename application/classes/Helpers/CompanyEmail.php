<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Helper class for retrieving company email addresses
 * This centralizes email configuration and makes it easy to switch between
 * development (masked) and production (real) email addresses
 * 
 * Future Enhancement: This can be refactored to read from database instead of config file
 */
class Helpers_CompanyEmail {
    
    /**
     * Get email address for a company
     * 
     * @param int $company_id Company identifier (1=Mobilink, 3=Ufone, 4=Zong, 6=Telenor, 7=Warid, 8=SCOM, 11=PTCL, 12=International, 13=NADRA)
     * @param int $request_type Optional request type for companies with type-specific emails
     * @return array Array with 'email' and 'name' keys
     */
    public static function get_email($company_id, $request_type = null) {
        // Load the email configuration
        $email_config = Kohana::$config->load('company_emails');
        
        if (!isset($email_config[$company_id])) {
            // Return a default/fallback email
            return ['email' => '', 'name' => ''];
        }
        
        $config = $email_config[$company_id];
        
        // Check if this company has type-specific emails
        if (isset($config['types']) && $request_type !== null) {
            // Find the appropriate type configuration
            foreach ($config['types'] as $key => $details) {
                if ($key === 'default') {
                    continue;
                }
                
                // Check if request_type matches any in the type_ids array
                if (isset($details['type_ids']) && in_array($request_type, $details['type_ids'])) {
                    return $details;
                }
            }
            
            // If no match found, return default
            if (isset($config['types']['default'])) {
                return $config['types']['default'];
            }
        }
        
        // Simple email case (no types)
        if (isset($config['email'])) {
            return $config;
        }
        
        // Fallback
        return ['email' => '', 'name' => ''];
    }
    
    /**
     * Get just the email address (convenience method)
     * 
     * @param int $company_id Company identifier
     * @param int $request_type Optional request type
     * @return string Email address
     */
    public static function get_email_address($company_id, $request_type = null) {
        $config = self::get_email($company_id, $request_type);
        return $config['email'] ?? '';
    }
    
    /**
     * Check if we're in development mode
     * 
     * @return bool True if in development mode
     */
    public static function is_development() {
        return Kohana::$environment === Kohana::DEVELOPMENT;
    }
}
