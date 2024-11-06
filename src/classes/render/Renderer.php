<?php
declare (strict_types = 1);

namespace iutnc\deefy\render;

interface Renderer
{
    const compact = "COMPACT";
    const long = "LONG";

    public function render(string $selector): string;
}