<?php

/**
 * Plugin Update Checker Library - GitHub Edition
 * Simplified version for GitHub releases without licensing
 * 
 * @version 5.4
 * @author Yahnis Elsts
 * @copyright 2024
 * Released under the MIT license.
 */

if (!class_exists('Puc_v4_Factory', false)):

    class Puc_v4_Factory
    {
        public static function buildUpdateChecker($gitHubUrl, $fullPath, $slug = '')
        {
            return new Puc_v4_Plugin_UpdateChecker($gitHubUrl, $fullPath, $slug);
        }
    }

    class Puc_v4_Plugin_UpdateChecker
    {
        protected $gitHubUrl;
        protected $pluginFile;
        protected $slug;
        protected $optionName;
        protected $githubUser;
        protected $githubRepo;

        public function __construct($gitHubUrl, $pluginFile, $slug = '')
        {
            $this->gitHubUrl = $gitHubUrl;
            $this->pluginFile = $pluginFile;
            $this->slug = !empty($slug) ? $slug : basename($pluginFile, '.php');
            $this->optionName = 'puc_external_updates_' . $this->slug;

            // Parse GitHub URL
            if (preg_match('#github\.com/([^/]+)/([^/]+)/?#', $gitHubUrl, $matches)) {
                $this->githubUser = $matches[1];
                $this->githubRepo = $matches[2];
            }

            add_filter('pre_set_site_transient_update_plugins', array($this, 'checkForUpdates'));
            add_filter('plugins_api', array($this, 'injectInfo'), 20, 3);
        }

        public function checkForUpdates($transient)
        {
            if (empty($transient->checked)) {
                return $transient;
            }

            $pluginData = get_plugin_data($this->pluginFile);
            $remoteVersion = $this->getRemoteVersion();

            if (version_compare($pluginData['Version'], $remoteVersion, '<')) {
                $transient->response[$this->getBasename()] = (object) array(
                    'slug' => $this->slug,
                    'plugin' => $this->getBasename(),
                    'new_version' => $remoteVersion,
                    'url' => $pluginData['PluginURI'],
                    'package' => $this->getDownloadUrl()
                );
            }

            return $transient;
        }

        public function injectInfo($result, $action, $args)
        {
            if ($action !== 'plugin_information' || $args->slug !== $this->slug) {
                return $result;
            }

            $pluginData = get_plugin_data($this->pluginFile);

            return (object) array(
                'name' => $pluginData['Name'],
                'slug' => $this->slug,
                'version' => $this->getRemoteVersion(),
                'author' => $pluginData['Author'],
                'homepage' => $pluginData['PluginURI'],
                'short_description' => $pluginData['Description'],
                'sections' => array(
                    'description' => $pluginData['Description']
                )
            );
        }

        protected function getRemoteVersion()
        {
            $remote_version = get_transient($this->optionName . '_version');
            if ($remote_version === false) {
                $apiUrl = "https://api.github.com/repos/{$this->githubUser}/{$this->githubRepo}/releases/latest";
                $response = wp_remote_get($apiUrl, array('timeout' => 15));
                if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                    $data = json_decode(wp_remote_retrieve_body($response), true);
                    $remote_version = isset($data['tag_name']) ? ltrim($data['tag_name'], 'v') : '1.0.0';
                    set_transient($this->optionName . '_version', $remote_version, HOUR_IN_SECONDS);
                } else {
                    $remote_version = '1.0.0';
                }
            }
            return $remote_version;
        }

        protected function getDownloadUrl()
        {
            $apiUrl = "https://api.github.com/repos/{$this->githubUser}/{$this->githubRepo}/releases/latest";
            $response = wp_remote_get($apiUrl, array('timeout' => 15));
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                return isset($data['zipball_url']) ? $data['zipball_url'] : '';
            }
            return '';
        }

        protected function getBasename()
        {
            return plugin_basename($this->pluginFile);
        }

        public function addQueryArgFilter($callback)
        {
            // For compatibility - does nothing in this simplified version
            return $this;
        }
    }

endif;

// For backward compatibility
if (!class_exists('YahnisElsts\PluginUpdateChecker\v5\PucFactory', false)) {
    class_alias('Puc_v4_Factory', 'YahnisElsts\PluginUpdateChecker\v5\PucFactory');
}
