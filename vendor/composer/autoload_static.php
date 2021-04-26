<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit67ba9c559bc894a354349544928e6e6b
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Api\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Api\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit67ba9c559bc894a354349544928e6e6b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit67ba9c559bc894a354349544928e6e6b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit67ba9c559bc894a354349544928e6e6b::$classMap;

        }, null, ClassLoader::class);
    }
}