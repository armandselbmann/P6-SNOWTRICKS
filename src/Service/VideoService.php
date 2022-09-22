<?php

namespace App\Service;


class VideoService
{
    /**
     * Verification of a link and its correspondence with a video platform
     *
     * @param string $link
     * @return string
     */
    public function checkVideoLink(string $link): string
    {
        if(str_contains($link, 'youtube.com/embed')) {
           return $this->obtainUrlVideoYT($link);
        }
        if(str_contains($link, 'youtu.be') || stristr($link, 'watch?v=')){
            return $this->convertUrlVideoYT($link);
        }
        if(str_contains($link, 'dailymotion.com/embed')) {
            return $this->obtainUrlVideoDM($link);
        }
        if(str_contains($link, 'dai.ly') || stristr($link, 'dailymotion.com/video')){
            return $this->convertUrlVideoDM($link);
        }
        if(str_contains($link, 'player.vimeo')) {
            return $this->obtainUrlVideoVI($link);
        }
        if(str_contains($link, 'vimeo')) {
            return $this->convertUrlVideoVI($link);
        }
        return '';
    }

    /**
     * Obtain url video in Youtube integration code
     *
     * @param string $link
     * @return string
     */
    private function obtainUrlVideoYT(string $link): string
    {
        // Extraction de l'url / Fetch url
        $explodeVideo = explode('"', $link);
        return $explodeVideo[5];
    }

    /**
     * Convert Youtube url
     *
     * @param string $link
     * @return array|false|string|string[]|void
     */
    private function convertUrlVideoYT(string $link)
    {
        if (str_contains($link, 'youtu.be')) {
            // Remplacement de chaine de caractère / Charactere string replace
            return str_replace('youtu.be', "www.youtube.com/embed", $link);
        } elseif (str_contains($link, 'watch?v=')) {
            // Remplacement de chaine de caractère / Charactere string replace
            $convertedUrl = str_replace('watch?v=', "embed/", $link);
            // Retourne la chaine de caractère avant & / Return charactere string before &
            return stristr($convertedUrl, '&', true);
        }
    }

    /**
     * Obtain url video in Dailymotion integration code
     *
     * @param string $link
     * @return string
     */
    private function obtainUrlVideoDM(string $link): string
    {
        // Extraction de l'url / Fetch url
        $explodeVideo = explode('"', $link);
        // Scindage de l'url pour supprimer le onload / Splitting the url to remove the onload
        $explodeVideo = explode('?', $explodeVideo[9]);
        return $explodeVideo[0];
    }

    /**
     * Convert Dailymotion url
     *
     * @param string $link
     * @return array|string|string[]|void
     */
    private function convertUrlVideoDM(string $link)
    {
        if (str_contains($link, 'dai.ly')) {
            // Remplacement de chaîne de caractère / Character string replacement
            return str_replace('dai.ly', "www.dailymotion.com/embed/video", $link);
        } elseif (str_contains($link, 'dailymotion.com/video')) {
            // Remplacement de chaîne de caractère / Character string replacement
            return str_replace('video', "embed/video", $link);
        }
    }

    /**
     * Obtain url video in Vimeo integration code
     *
     * @param string $link
     * @return string
     */
    private function obtainUrlVideoVI(string $link): string
    {
        // Extraction de l'url / Fetch url
        $explodeVideo = explode('"', $link);
        return $explodeVideo[1];
    }

    /**
     * Convert Vimeo url
     *
     * @param string $link
     * @return array|string
     */
    private function convertUrlVideoVI(string $link): array|string
    {
        // Remplacement de chaine de caractère / Charactere string replace
        return str_replace('vimeo.com', "player.vimeo.com/video", $link);
    }
}