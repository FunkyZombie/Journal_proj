<?php

namespace Journal\Articles;

class Comment {
    private int $id;
    private int $author_id;
    private int $article_id;
    private string $text;

    function __construct(string $text)
    {
        $this->text = $text;
    }

    public function __toString(): string
    {
        return $this->text;
    }
}