<?php

namespace ChameleonSystem\CmsCacheBundle\QueryCache;

enum QueryIsCacheableDecision: int
{
    /**
     * may be cached.
     */
    case YES = 1;
    /**
     * may not be cached.
     */
    case NO = 0;
    /**
     * another decision maker needs to decide.
     */
    case NOT_DECIDED = 2;
}
