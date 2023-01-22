<?php

namespace Journal\Articles;

class Article {
    private int $id;
    private int $author_id;
    private string $title;
    private string $text;

    function __construct( string $title, string $text)
    {
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return $this->title . ' >>> ' . $this->text;
    }
}