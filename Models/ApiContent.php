<?php

namespace ApiContentProviderBundle\Models;

class ApiContent
{
    private $url;

    Private $title;

    private $keyValueStore = [];

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function addToKeyValueStore($key, $value)
    {
        $this->keyValueStore[$key] = $value;
    }

    public function setKeyValueStore($keyValueStore)
    {
        $this->keyValueStore = $keyValueStore;
    }

    public function getKeyValueStore()
    {
        return $this->keyValueStore;
    }

    public function getPublicData()
    {
        $publicData = [];

        $publicData['url'] = $this->getUrl();
        $publicData['title'] = $this->getTitle();

        foreach ($this->keyValueStore as $key => $value) {
            $publicData[$key] = $value;
        }

        return $publicData;
    }
}
