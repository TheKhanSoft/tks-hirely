<?php

namespace App\Services;

class AppVersion
{
    /**
     * Get the core semantic software version.
     */
    public static function getVersion(): string
    {
        return (string) config('app.version', env('APP_VERSION', 'v2.4.2'));
    }

    /**
     * Get the short version for compact pill badges (e.g. "v2.4.2").
     */
    public static function getShortVersion(): string
    {
        $v = trim(self::getVersion());
        return str_starts_with(strtolower($v), 'v') ? $v : 'v' . $v;
    }

    /**
     * Get the build stamp or deployment date identifier (e.g. "20261111").
     */
    public static function getBuild(): string
    {
        $build = config('app.build', env('APP_BUILD'));
        if (!empty($build)) {
            return (string) $build;
        }

        // Dynamic fallback: use git commit hash or current date
        $gitHash = self::getGitCommitHash();
        return $gitHash ?: date('Ymd');
    }

    /**
     * Get the release title or edition tag (e.g. "Statutory Recruitment OS").
     */
    public static function getReleaseName(): string
    {
        return (string) config('app.release_name', env('APP_RELEASE_NAME', 'Statutory Recruitment OS'));
    }

    /**
     * Resolve short Git commit hash if in a git repository.
     */
    public static function getGitCommitHash(): ?string
    {
        static $hash = null;
        if ($hash !== null) {
            return $hash ?: null;
        }

        $gitPath = base_path('.git');
        if (is_dir($gitPath) && file_exists($gitPath . '/HEAD')) {
            try {
                $head = trim(file_get_contents($gitPath . '/HEAD'));
                if (str_starts_with($head, 'ref:')) {
                    $refPath = $gitPath . '/' . trim(substr($head, 4));
                    if (file_exists($refPath)) {
                        $fullHash = trim(file_get_contents($refPath));
                        return $hash = substr($fullHash, 0, 7);
                    }
                } else {
                    return $hash = substr($head, 0, 7);
                }
            } catch (\Throwable $e) {
                // Silently fallback on failure
            }
        }

        return $hash = '';
    }

    /**
     * Get standard formatted version string with build stamp:
     * e.g. "v2.4.2 (Build 20261111)"
     */
    public static function format(): string
    {
        $v = self::getShortVersion();
        $b = self::getBuild();
        return "{$v} (Build {$b})";
    }

    /**
     * Get full release badge string:
     * e.g. "v2.4.2 • Build 20261111"
     */
    public static function getBadgeString(): string
    {
        $v = self::getShortVersion();
        $b = self::getBuild();
        return "{$v} • Build {$b}";
    }

    /**
     * Get public footer version string for login & guest screens:
     * e.g. "Hirely Recruitment OS • v2.4.2 (Build 20261111)"
     */
    public static function getPublicFooterString(): string
    {
        $appName = (string) config('app.name', 'Hirely');
        $release = self::getReleaseName();
        $formatted = self::format();
        return "{$appName} {$release} • {$formatted}";
    }

    /**
     * Detailed technical diagnostic array for admin portals.
     */
    public static function getDiagnostics(): array
    {
        return [
            'version' => self::getVersion(),
            'short_version' => self::getShortVersion(),
            'build' => self::getBuild(),
            'release_name' => self::getReleaseName(),
            'formatted' => self::format(),
            'git_commit' => self::getGitCommitHash(),
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }
}
