<?php
/**
 * TeiEditions
 *
 * @copyright Copyright 2018 King's College London Department of Digital Humanities
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

class TeiEditionsEntity
{
    public $name;
    public $slug;
    public $notes = [];
    public $urls = [];
    public $longitude = null;
    public $latitude = null;

    public function hasGeo()
    {
        return isset($this->longitude) and isset($this->latitude);
    }

    public function ref()
    {
        return isset($this->urls["normal"])
            ? $this->urls["normal"]
            : ("#" . $this->slug);
    }
    
    static function create($name, $url) {
        $e = new TeiEditionsEntity;
        $e->name = $name;
        $e->urls = ["normal" => $url];
        $e->slug = tei_editions_url_to_slug($url);
        return $e;
    }
}