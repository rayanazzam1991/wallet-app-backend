<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()
    ->uses(RefreshDatabase::class,TestCase::class)
    ->in(
        './Feature'
    );
