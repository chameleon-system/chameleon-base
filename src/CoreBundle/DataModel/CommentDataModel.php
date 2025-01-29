<?php

namespace ChameleonSystem\CoreBundle\DataModel;

class CommentDataModel
{
    public function __construct(
        public readonly string $text = '',
        public readonly bool $full = false,
    ) {
    }
}
