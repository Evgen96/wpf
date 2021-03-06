<?php
/*
 * This file is part of the ManageWP Worker plugin.
 *
 * (c) ManageWP LLC <contact@managewp.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MWP_Worker_Installer
{
    private $context;

    private $loaderName;

    private $initPath;

    function __construct(MWP_Context_WordPress $context, $loaderName)
    {
        $this->context    = $context;
        $this->loaderName = $loaderName;
    }

    /**
     * Executed when the plugin is activated.
     *
     * @param boolean $networkWide
     *
     * @hook
     * @link http://codex.wordpress.org/Function_Reference/register_activation_hook
     */
    public function activate($networkWide = false)
    {
        try {
            $this->registerMustUse();
            $this->context->optionSet('mwp_loader', true);
        } catch (MWP_Worker_Exception $e) {
            $this->context->optionSet('mwp_loader_error', $e->getMessage());
        }
    }

    /**
     * Executed when the plugin is deactivated.
     *
     * @hook
     * @link http://codex.wordpress.org/Function_Reference/register_deactivation_hook
     */
    public function deactivate()
    {
        $this->context->optionDelete('mwp_signer');
        $this->context->optionDelete('mwp_algorithm');
        $this->context->optionDelete('mwp_key');
        $this->context->optionDelete('mwp_loader');
        $this->context->optionDelete('mwp_loader_error');
        $this->unregisterMustUse();
    }

    /**
     * Executed when the plugin is being uninstalled.
     *
     * @hook
     * @link http://codex.wordpress.org/Function_Reference/register_uninstall_hook
     */
    public function uninstall()
    {

    }

    /**
     * Try to register as a "must use" plugin.
     *
     * @throws MWP_Worker_Exception
     * @link http://codex.wordpress.org/Must_Use_Plugins
     */
    public function registerMustUse()
    {
        $mustUsePluginDir = $this->context->getConstant('WPMU_PLUGIN_DIR');
        $loaderPath       = $mustUsePluginDir.'/'.$this->loaderName;
        $pluginBasename   = $this->context->getPluginBasename();

        $loader = <<<EOF
<?php

/**
 * This file is automatically generated by the MWP worker plugin and should be automatically deleted
 * upon disabling or uninstalling it. It should require no modifications.
 *
 * Read about WordPress "must-use" plugins here http://codex.wordpress.org/Must_Use_Plugins
 */

if (file_exists(untrailingslashit(WP_PLUGIN_DIR).'/$pluginBasename')) {
    \$mwp_wpmu = true;
    include_once untrailingslashit(WP_PLUGIN_DIR).'/$pluginBasename';
}

EOF;

        if (!is_dir($mustUsePluginDir)) {
            $dirMade = @mkdir($mustUsePluginDir);

            if (!$dirMade) {
                $error = error_get_last();
                throw new MWP_Worker_Exception(sprintf('Unable to create MWP loader directory: %s', $error['message']));
            }
        }

        $loaderWritten = @file_put_contents($loaderPath, $loader);

        if (!$loaderWritten) {
            $error = error_get_last();
            throw new MWP_Worker_Exception(sprintf('Unable to write MWP loader: %s', $error['message']));
        }
    }

    public function unregisterMustUse()
    {
        $loaderFile = $this->context->getConstant('WPMU_PLUGIN_DIR').'/'.$this->loaderName;

        if (file_exists($loaderFile)) {
            unlink($loaderFile);
        }
    }

    /**
     * @return mixed
     */
    public function getInitPath()
    {
        return $this->initPath;
    }

    /**
     * @param mixed $initFile
     */
    public function setInitPath($initFile)
    {
        $this->initPath = $initFile;
    }
}
