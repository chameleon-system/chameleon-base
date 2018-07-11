<?php

namespace ChameleonSystem\CoreBundle\Util;

class Hash implements HashInterface
{
    /**
     * {@inheritdoc}
     */
    public function hash32($data)
    {
        $stringRepresentation = var_export($data, true);
        $lines = explode("\n", $stringRepresentation);
        sort($lines);

        return md5(implode("\n", $lines));
    }
}
