<?php

namespace LBF\HTML;

use LBF\Errors\MissingComponent\MissingComponent;

class TagGenerator {

    private bool $echo;

    private readonly array $params;

    private readonly string $inner_html;

    private readonly bool $opening_tag;
    private readonly bool $closing_tag;
    private readonly bool $single_tag;

    private string $html = '';


    public static function tag(string $tag): TagGenerator {
        return new TagGenerator($tag);
    }

    public function __construct(
        private readonly string $tag,
    ) {
    }

    public function opening_tag(): static {
        $this->opening_tag = true;
        return $this;
    }

    public function closing_tag(): static {
        $this->closing_tag = true;
        return $this;
    }

    public function single_tag(): static {
        $this->single_tag = true;
        return $this;
    }

    public function inner_html(string $html): static {
        $this->inner_html = $html;
        return $this;
    }

    public function params(array $params): static {
        $this->params = $params;
        return $this;
    }

    public function echo(bool $echo): static {
        $this->echo = $echo;
        return $this;
    }

    public function render(): string {
        $this->echo ??= true;
        $this->opening_tag ??= false;
        $this->closing_tag ??= false;
        $this->single_tag ??= false;

        if (!$this->opening_tag && !$this->closing_tag && !$this->single_tag) {
            throw new MissingComponent("A tag is required.", 404);
        }

        if ($this->opening_tag || $this->single_tag) {
            // Build the opening tag
            $this->html .= "<{$this->tag}";
            // Add params
            $this->html .= ">";
        }

        if (isset($this->inner_html)) {
            $this->html .= $this->inner_html;
        }

        if ($this->closing_tag) {
            $this->html .= "</{$this->tag}>";
        }

        if ($this->echo) {
            echo $this->html;
        }
        return $this->html;
    }
}
