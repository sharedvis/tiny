<?php

namespace Tiny\Helper;

class ApplicationVersion
{
    public static function get()
    {
        $existingVersion = @file_get_contents(APP_PATH . "/build_version_hash");

        if (!empty($existingVersion))
            return $existingVersion;

        $commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));
        $commitCount = implode(str_split(exec('git rev-list HEAD | wc -l')), ".");
        $commitDate = trim(exec('git log -n1 --pretty=%ci HEAD'));
        $commitBranch = trim(exec('git branch | grep \* | cut -d " " -f2'));

        return sprintf('v%s-' . $commitBranch . '.%s (%s)', $commitCount, $commitHash, $commitDate);
    }
}
