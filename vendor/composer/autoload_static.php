<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit366b43613b3159d908e214ecf7af915e
{
    public static $classMap = array (
        'AwardImg' => __DIR__ . '/../..' . '/classes/AwardImg.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'OliTools' => __DIR__ . '/../..' . '/classes/OliTools.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit366b43613b3159d908e214ecf7af915e::$classMap;

        }, null, ClassLoader::class);
    }
}
