<?php

if (SENTRY_DSN AND SENTRY_ALL) {
    Sentry\init([
        'dsn' => SENTRY_DSN,
        'capture_silenced_errors' => true,
        'environment' => ENV,
        'release' => RELEASE,
    ]);
}
