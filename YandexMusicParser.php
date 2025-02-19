<?php

class YandexMusicParser {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function parser($url) {
        $html = $this->loadHtml($url);
        $artist = $this->getArtistName($html);
        $listenersCount = $this->getListenersCount($html);
        $musics = $this->getMusics($html);

        $artistId = $this->getArtistId($artist);
        if (!$artistId) {
            $artistId = $this->addArtist($artist, $listenersCount);
        }

        $this->addMusics($musics, $artistId);

        return [
            'artist' => $artist,
            'listeners' => $listenersCount,
            'musics' => $musics
        ];
    }

    private function loadHTML($url) {
        $html = file_get_contents($url);
        if (!$html) {
            throw new Exception("Не удалось загрузить страницу");
        }
        return $html;
    }

    private function getArtistName($html) {
        preg_match('/<h1 class="page-artist__title[^"]*">(.*?)<\/h1>/', $html, $artistName);
        return trim($artistName[1] ?? 'Unknown');
    }

    private function getArtistId($artist) {
        $stmt = $this->pdo->prepare("SELECT id FROM artists WHERE name = ?");
        $stmt->execute([$artist]);
        return $stmt->fetchColumn();
    }

    private function addMusics($musics, $artistId) {
        foreach ($musics as $music) {
            $stmt = $this->pdo->prepare("SELECT id FROM music WHERE name = ? AND artist_id = ?");
            $stmt->execute([$music, $artistId]);

            if (!$stmt->fetchColumn()) {
                $stmt = $this->pdo->prepare("INSERT INTO music (name, artist_id) VALUES (?, ?)");
                $stmt->execute([$music, $artistId]);
            }
        }
    }

    private function getMusics($html) {
        preg_match_all('/<div class="d-music__name"[^>]*><a[^>]*>(.*?)<\/a>.*?<span class="typo-music deco-typo-secondary">(.*?)<\/span>/', $html, $musics);
        return array_map('trim', $musics[1] ?? []);
    }

    private function getListenersCount($html) {
        preg_match('/<div class="page-artist__summary[^"]*">.*?<span>([\d\s]+)<\/span>/', $html, $listeners);
        return trim($listeners[1] ?? '0');
    }
}