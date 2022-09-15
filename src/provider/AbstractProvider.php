<?php
declare(strict_types=1);

namespace workbunny\IpLocation\provider;


abstract class AbstractProvider
{

    /** @var string  ip数据库文件路径 */
    protected string $path;

    /** @var array|mixed|string[] 语言 */
    protected array $language = ['zh-CN'];

    protected string $defaultIdentifier = "--";

    public function __construct()
    {
        $this->path = dirname(__DIR__) . "/src/libs";

        if (function_exists('config')) {
            $default = config("plugin.sunsgne.ip-location.app.config") ?? "default";
            $config  = config("plugin.sunsgne.ip-location.app." . $default) ?? [];

            $this->language = isset($config["language"])
                ? (!empty($config["language"]) ? $config["language"] : $this->language)
                : $this->language;

            $this->path = isset($config["mdbFileDir"])
                ? (!empty($config["mdbFileDir"]) ? $config["mdbFileDir"] : $this->path)
                : $this->path;

            $this->defaultIdentifier = isset($config["defaultIdentifier"])
                ? (!empty($config["defaultIdentifier"]) ? $config["defaultIdentifier"] : $this->defaultIdentifier)
                : $this->defaultIdentifier;
        }
    }


}