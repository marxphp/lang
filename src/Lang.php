<?php

namespace Max\Lang;

use Max\Utils\Arr;

/**
 * 使用方法
 * $lang = new Lang();
 * $lang->setLangPath(storage_path('lang/'))->setLocale('zh-cn')->load();
 * var_dump($lang->translate('app.system_error'));
 */
class Lang
{
    /**
     * 本地语言包
     *
     * @var string
     */
    protected string $locale;

    /**
     * 加载的语言包
     *
     * @var array
     */
    protected array $languages = [];

    /**
     * 语言包路径
     *
     * @var string
     */
    protected string $langPath;

    /**
     * 语言包文件后缀
     *
     * @var string
     */
    protected string $suffix = '.php';

    /**
     * 这只语言包路径
     *
     * @param string $langPath
     *
     * @return $this
     */
    public function setLangPath(string $langPath): static
    {
        $this->langPath = $langPath;

        return $this;
    }

    /**
     * 设置语言包后缀
     *
     * @param string $suffix
     *
     * @return $this
     */
    public function setLangSuffix(string $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * 加载语言包
     *
     * @param string $locale
     */
    public function load(string $locale = 'zh-cn')
    {
        if (!isset($this->locale)) {
            $this->setLocale($locale);
        }
    }

    /**
     * 设置本地语言
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;
        $packages     = glob($this->langPath . $locale . '/*' . $this->suffix);
        foreach ($packages as $package) {
            $key                            = pathinfo($package, PATHINFO_FILENAME);
            $this->languages[$locale][$key] = include($package);
        }

        return $this;
    }

    /**
     * 加载一个语言
     *
     * @param string $key
     * @param string $locale
     */
    protected function loadOne(string $key, string $locale)
    {
        if (!Arr::has($this->languages, $locale . '.' . $key)) {
            $package                        = $this->langPath . $locale . '/' . $key . $this->suffix;
            $this->languages[$locale][$key] = include($package);
        }
    }

    /**
     * 翻译语言
     *
     * @param string      $key
     * @param string|null $locale
     *
     * @return array|\ArrayAccess|mixed
     */
    public function translate(string $key, ?string $locale = null)
    {
        $filename = explode('.', $key)[0];
        if (isset($locale)) {
            $this->loadOne($filename, $locale);
        } else {
            $locale = $this->locale;
        }

        return Arr::get($this->languages, $locale . '.' . $key);
    }
}
