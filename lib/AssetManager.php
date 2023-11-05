<?php

namespace Femundfilou\AssetManager;

/**
 * Class AssetManager
 *
 * Manages the assets like CSS and JS for a web application.
 * Implemented as a singleton to ensure only one instance manages all assets.
 */
class AssetManager
{
    private static ?AssetManager $instance = null;
    private array $preload = [];
    private array $css = [];
    private array $js = [];

    /**
     * The constructor is private to prevent creating multiple instances.
     */
    private function __construct()
    {
    }

    /**
     * Prevent the instance from being cloned (which would create a second instance of it).
     */
    private function __clone()
    {
    }

    /**
     * Prevent from being unserialized (which could create a second instance of it).
     *
     * @throws \Exception if a serialization attempt is performed.
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    /**
     * Retrieves the single instance of the AssetManager.
     *
     * @return AssetManager The single instance of the AssetManager.
     */
    public static function getInstance(): AssetManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Registers a new asset file.
     *
     * @param string $type The type of asset ('css' or 'js').
     * @param string $filePath The path to the asset file.
     *
     * @throws \InvalidArgumentException if the asset type is not 'css' or 'js'.
     */
    public function add(string $type, string $filePath, array|string|null $options = null): void
    {
        if (!in_array($type, ['css', 'js'], true)) {
            throw new \InvalidArgumentException("Invalid asset type: {$type}");
        }

        if (!in_array($filePath, $this->{$type}, true)) {
            $this->{$type}[] = $type === 'css' ? css($filePath, $options) : js($filePath, $options);
            $this->preload[] = $this->preload($type, $filePath, $options);
        }
    }

    public function preload(string $type, string $filePath, $options)
    {
        $attributes = $type === 'css' ? [
            'rel'         => 'preload',
            'as'          => 'style',
            'href'        => $filePath,
            'crossorigin' => $options['crossorigin'] ?? false,
        ] : [
            'rel'         => 'modulepreload',
            'href'        => $filePath,
            'crossorigin' => $options['crossorigin'] ?? false,
        ];
        return \Kirby\Toolkit\Html::tag('link', '', $attributes);
    }


    /**
     * Outputs the registered asset paths as a string.
     *
     * @param string $type The type of assets to output ('css' or 'js').
     *
     * @return string A string containing the asset paths.
     *
     * @throws \InvalidArgumentException if the asset type is not 'css' or 'js'.
     */
    public function output(string $type): string
    {
        if (!in_array($type, ['css', 'js', 'preload'], true)) {
            throw new \InvalidArgumentException("Invalid asset type: {$type}");
        }
        return implode(PHP_EOL, $this->{$type});
    }

    /**
     * Resets the AssetManager instance.
     * This method is mainly intended for testing purposes to ensure a clean state.
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}
