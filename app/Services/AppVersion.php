<?php

namespace App\Services;

class AppVersion
{
    /**
     * Cached version metadata in-memory for zero request overhead.
     */
    protected static ?array $cachedMeta = null;

    /**
     * Get the core semantic software version.
     * Prioritizes explicit config/env, with dynamic Git Tag and release artifact resolution.
     */
    public static function getVersion(): string
    {
        $meta = self::resolveMetadata();
        return $meta['version'];
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
     * Get the build stamp or deployment date identifier (e.g. "20261111" or Git commit).
     */
    public static function getBuild(): string
    {
        $meta = self::resolveMetadata();
        return $meta['build'];
    }

    /**
     * Get the institutional release name or edition tag (e.g. "Statutory Recruitment OS").
     */
    public static function getReleaseName(): string
    {
        $meta = self::resolveMetadata();
        return $meta['release_name'];
    }

    /**
     * Check if the current versioning is dynamically derived from Git/build artifacts.
     */
    public static function isDynamic(): bool
    {
        $rawVersion = config('app.version', env('APP_VERSION'));
        $rawBuild = config('app.build', env('APP_BUILD'));
        return empty($rawVersion) || $rawVersion === 'auto' || empty($rawBuild) || $rawBuild === 'auto';
    }

    /**
     * Resolve all version metadata dynamically with layered fallbacks:
     * 1. config('app.version') / env('APP_VERSION')
     * 2. version.json deployment artifact (if created by CI/CD)
     * 3. Git Tags and Commit Timestamps
     * 4. Institutional release defaults
     */
    public static function resolveMetadata(): array
    {
        if (self::$cachedMeta !== null && !app()->runningUnitTests()) {
            return self::$cachedMeta;
        }

        // 1. Check version.json if present (common in Docker/CI-CD deployments)
        $fileMeta = self::readVersionFile();

        // 2. Resolve Version
        $configuredVersion = config('app.version', env('APP_VERSION'));
        if (!empty($configuredVersion) && $configuredVersion !== 'auto') {
            $version = (string) $configuredVersion;
        } elseif (!empty($fileMeta['version'])) {
            $version = (string) $fileMeta['version'];
        } else {
            $gitTag = self::getLatestGitTag();
            $version = $gitTag ?: 'v2.4.2';
        }

        // 3. Resolve Build (Supports explicit string, 'commit'/'hash', 'date', 'hybrid', or 'auto')
        $configuredBuild = config('app.build', env('APP_BUILD'));
        $gitDate = self::getGitCommitDate();
        $gitHash = self::getGitCommitHash();

        if (!empty($configuredBuild) && !in_array($configuredBuild, ['auto', 'commit', 'hash', 'date', 'hybrid'])) {
            $build = (string) $configuredBuild;
        } elseif ($configuredBuild === 'commit' || $configuredBuild === 'hash') {
            $build = $gitHash ?: ($gitDate ?: date('Ymd'));
        } elseif ($configuredBuild === 'date') {
            $build = $gitDate ?: date('Ymd');
        } elseif ($configuredBuild === 'hybrid') {
            $build = ($gitDate && $gitHash) ? "{$gitDate}.{$gitHash}" : ($gitDate ?: ($gitHash ?: date('Ymd')));
        } elseif (!empty($fileMeta['build'])) {
            $build = (string) $fileMeta['build'];
        } else {
            // Default when auto: commit date, or short git hash, or current date
            $build = $gitDate ?: ($gitHash ?: date('Ymd'));
        }

        // 4. Resolve Release Name
        $configuredRelease = config('app.release_name', env('APP_RELEASE_NAME'));
        $releaseName = !empty($configuredRelease) 
            ? (string) $configuredRelease 
            : ($fileMeta['release_name'] ?? 'Statutory Recruitment OS');

        return self::$cachedMeta = [
            'version' => $version,
            'build' => $build,
            'release_name' => $releaseName,
            'git_commit' => self::getGitCommitHash(),
            'git_tag' => self::getLatestGitTag(),
        ];
    }

    /**
     * Read version.json from project base path if present.
     */
    protected static function readVersionFile(): array
    {
        $path = base_path('version.json');
        if (file_exists($path)) {
            try {
                $content = json_decode(file_get_contents($path), true);
                if (is_array($content)) {
                    return $content;
                }
            } catch (\Throwable $e) {}
        }
        return [];
    }

    /**
     * Resolve latest Git Tag if in a repository.
     */
    public static function getLatestGitTag(): ?string
    {
        static $tag = null;
        if ($tag !== null) {
            return $tag ?: null;
        }

        // Direct git command fallback if shell_exec is enabled
        if (function_exists('shell_exec')) {
            try {
                $out = @shell_exec('git describe --tags --abbrev=0 2>nul');
                if ($out && trim($out)) {
                    return $tag = trim($out);
                }
            } catch (\Throwable $e) {}
        }

        // Pure PHP inspection of .git refs
        $gitPath = base_path('.git');
        if (is_dir($gitPath)) {
            $tagsDir = $gitPath . '/refs/tags';
            if (is_dir($tagsDir)) {
                $tagFiles = scandir($tagsDir);
                $tags = array_diff($tagFiles ?: [], ['.', '..']);
                if (!empty($tags)) {
                    rsort($tags, SORT_NATURAL);
                    return $tag = reset($tags);
                }
            }

            if (file_exists($gitPath . '/packed-refs')) {
                $lines = @file($gitPath . '/packed-refs', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
                $pTags = [];
                foreach ($lines as $line) {
                    if (str_contains($line, 'refs/tags/')) {
                        $parts = explode('refs/tags/', $line);
                        if (!empty($parts[1])) {
                            $pTags[] = trim($parts[1]);
                        }
                    }
                }
                if (!empty($pTags)) {
                    rsort($pTags, SORT_NATURAL);
                    return $tag = reset($pTags);
                }
            }
        }

        return $tag = '';
    }

    /**
     * Resolve Git commit date formatted as YYYYMMDD (e.g. 20261111).
     */
    public static function getGitCommitDate(): ?string
    {
        static $date = null;
        if ($date !== null) {
            return $date ?: null;
        }

        if (function_exists('shell_exec')) {
            try {
                $out = @shell_exec('git log -1 --format=%cd --date=format:%Y%m%d 2>nul');
                if ($out && trim($out) && preg_match('/^\d{8}$/', trim($out))) {
                    return $date = trim($out);
                }
            } catch (\Throwable $e) {}
        }

        return $date = '';
    }

    /**
     * Resolve short Git commit hash (e.g. "7148eb6").
     */
    public static function getGitCommitHash(): ?string
    {
        static $hash = null;
        if ($hash !== null) {
            return $hash ?: null;
        }

        // Pure PHP inspection of .git HEAD without running shell commands
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
            } catch (\Throwable $e) {}
        }

        if (function_exists('shell_exec')) {
            try {
                $out = @shell_exec('git log -1 --format=%h 2>nul');
                if ($out && trim($out)) {
                    return $hash = trim($out);
                }
            } catch (\Throwable $e) {}
        }

        return $hash = '';
    }

    /**
     * Clear runtime cached metadata.
     */
    public static function clearCache(): void
    {
        self::$cachedMeta = null;
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
     * e.g. "Hirely Statutory Recruitment OS • v2.4.2 (Build 20261111)"
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
            'git_tag' => self::getLatestGitTag(),
            'is_dynamic' => self::isDynamic(),
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }
}
