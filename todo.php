<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class TodoPlugin
 * @package Grav\Plugin
 */
class TodoPlugin extends Plugin
{
    protected $route = 'todo';

    /** @var array
     *  Required for Grav 1.7 support:
     *  https://learn.getgrav.org/16/advanced/grav-development/grav-17-upgrade-guide#blueprints
     */
    public $features = [
        'blueprints' => 0, // Use priority 0
    ];

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                ['autoload', 100000], // TODO: Remove when plugin requires Grav >=1.7
                ['onPluginsInitialized', 0]
            ]
        ];
    }

    /**
    * Composer autoload
    * @return ClassLoader
    */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        /** @var Uri $uri */
        $uri = $this->grav['uri'];

        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            $this->enable([
                'onTwigTemplatePaths' => ['onTwigAdminTemplatePaths', 0],
                'onAdminMenu' => ['onAdminMenu', 0],
                'onGetPageBlueprints' => ['onGetPageBlueprints', 0],
                'onGetPageTemplates' => ['onGetPageTemplates', 0],
            ]);

            if (strpos($uri->path(), $this->config->get('plugins.admin.route') . '/' . $this->route) === false) {
                return;
            }

            // NOTE: This is required for grav 1.7
            $this->grav['admin']->enablePages();

            return;
        }

        // Enable the main events we are interested in
        $this->enable([
            // Put your main events here
        ]);
    }

    /**
     * Add plugin templates path
     */
    public function onTwigAdminTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/admin/templates';
    }

    /**
     * Add navigation item to the admin plugin
     */
    public function onAdminMenu()
    {
        $this->grav['twig']->plugins_hooked_nav['TODO'] = ['route' => $this->route, 'icon' => 'fa-check'];
    }

    /**
     * Add blueprint directory.
     */
    public function onGetPageBlueprints(Event $event): void
    {
        $types = $event->types;
        $types->scanBlueprints('plugin://' . $this->name . '/blueprints');
    }

    /**
     * Add templates directory.
     */
    public function onGetPageTemplates(Event $event): void
    {
        $types = $event->types;
        $types->scanTemplates('plugin://' . $this->name . '/templates');
    }
}
