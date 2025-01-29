<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core;

interface DecisionNode
{
    public function handle(): mixed;
}