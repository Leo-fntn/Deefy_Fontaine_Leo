<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

class AudioTrackRenderer implements Renderer
{
    public AudioTrack $track;

    public function __construct(AudioTrack $track)
    {
        $this->track = $track;
    }

    public function render(string $selector): string{
        switch($selector){
            case self::long:
                return $this->long();
            default:
                return $this->court();
        }
    }
}