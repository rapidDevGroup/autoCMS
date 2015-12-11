<?php

class RSSData extends Data {
    public $dataFile = 'autocms-rss.json';

    function buildDataFile($files) {
        foreach ($files as $file) {
            $fileData = file_get_contents('../' . $file, true);

            $html = str_get_html($fileData);

            foreach($html->find('.auto-rss-link') as $rssFeed) {
                $settingsData = new SettingsData();
                $this->data['rss'] = Array('rss' => $settingsData->getHost() . "feed/", 'type' => 'rss');
                $this->data['rss-link'] = Array('text' => '<link rel="alternate" type="application/rss+xml" href="' . $this->data['rss']['rss'] . '" title="RSS feed for ' . $settingsData->getSiteName() . '">', 'type' => 'text');
                $rssFeed->href = "<?=get('$this->dataFile', 'rss')?>";
            }

            $fp = fopen('../' . $file, 'w');
            fwrite($fp, $html);
            fclose($fp);
        }

        if ($this->hasFeed()) $this->copyFeed();
    }

    public function hasFeed() {
        return isset($this->data['rss']);
    }

    public function getFeedLocation() {
        return $this->data['rss']['rss'];
    }

    public function copyFeed() {
        if (file_exists('./other/feed.php')) copy('./other/feed.php', '../feed.php');
    }
}